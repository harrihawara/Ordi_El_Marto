<?php
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

// Nur POST erlauben
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Methode nicht erlaubt']);
    exit;
}

// JSON-Body einlesen
$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage']);
    exit;
}

// Pflichtfelder prüfen
foreach (['vorname', 'nachname', 'email', 'telefon'] as $f) {
    if (empty(trim($data[$f] ?? ''))) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Pflichtfeld fehlt: $f"]);
        exit;
    }
}

// Daten bereinigen
$anrede      = htmlspecialchars(trim($data['anrede']      ?? ''),      ENT_QUOTES, 'UTF-8');
$vorname     = htmlspecialchars(trim($data['vorname']     ?? ''),      ENT_QUOTES, 'UTF-8');
$nachname    = htmlspecialchars(trim($data['nachname']    ?? ''),      ENT_QUOTES, 'UTF-8');
$email       = filter_var(trim($data['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$telefon     = htmlspecialchars(trim($data['telefon']     ?? ''),      ENT_QUOTES, 'UTF-8');
$behandlung  = htmlspecialchars(trim($data['behandlung']  ?? 'Nicht angegeben'), ENT_QUOTES, 'UTF-8');
$datum       = htmlspecialchars(trim($data['datum']       ?? 'Nicht angegeben'), ENT_QUOTES, 'UTF-8');
$beschwerden = htmlspecialchars(trim($data['beschwerden'] ?? 'Keine Beschreibung'), ENT_QUOTES, 'UTF-8');

// E-Mail-Adresse validieren
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Ungültige E-Mail-Adresse']);
    exit;
}

// Empfänger & Betreff
$to      = 'oliver.stojanovic@proton.me';
$subject = '=?UTF-8?B?' . base64_encode('WEB-Terminanfrage: ' . $behandlung) . '?=';

// E-Mail-Text
$body  = "KURZE BESCHREIBUNG:\n";
$body .= "$beschwerden\n\n";
$body .= "--- KONTAKTDATEN ---\n";
$body .= "Anrede:         $anrede\n";
$body .= "Vorname:        $vorname\n";
$body .= "Nachname:       $nachname\n";
$body .= "E-Mail:         $email\n";
$body .= "Telefon:        $telefon\n";
$body .= "Behandlungsart: $behandlung\n";
$body .= "Wunschdatum:    $datum\n";
$body .= "---\n";
$body .= "Gesendet via Webformular dr-elmarto.at";

// Header
$headers  = "From: Webformular Dr. El Marto <webformular@dr-elmarto.at>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "Content-Transfer-Encoding: 8bit\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

// Senden
if (mail($to, $subject, $body, $headers)) {
    echo json_encode(['success' => true, 'message' => 'E-Mail erfolgreich gesendet']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Serverfehler beim E-Mail-Versand']);
}
?>

<?php
/**
 * send_mail.php — Terminanfrage-Handler
 * OA Dr. Shady El Marto | ordination@dr-elmarto.at
 * World4You / Apache + PHP
 *
 * Empfängt JSON von termin.html, validiert, sendet E-Mail.
 */

// ── Konfiguration ─────────────────────────────────────────────────────────────
define('EMPFAENGER',    'ordination@dr-elmarto.at');
define('BETREFF_PREFIX','[Terminanfrage] ');
define('ABSENDER_NAME', 'Webformular dr-elmarto.at');
define('ABSENDER_MAIL', 'noreply@dr-elmarto.at');   // muss auf derselben Domain liegen
define('ERLAUBTE_ORIGIN','https://www.dr-elmarto.at');

// ── Headers ──────────────────────────────────────────────────────────────────
header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');

// CORS – nur eigene Domain
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin === ERLAUBTE_ORIGIN || strpos($origin, 'dr-elmarto.at') !== false) {
    header('Access-Control-Allow-Origin: ' . $origin);
}
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ── Nur POST erlaubt ──────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// ── JSON einlesen ─────────────────────────────────────────────────────────────
$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Ungültige Daten']);
    exit;
}

// ── Hilfsfunktion: bereinigen ─────────────────────────────────────────────────
function clean(string $val): string {
    return htmlspecialchars(strip_tags(trim($val)), ENT_QUOTES, 'UTF-8');
}

// ── Felder auslesen & bereinigen ──────────────────────────────────────────────
$anrede      = clean($data['anrede']      ?? '');
$vorname     = clean($data['vorname']     ?? '');
$nachname    = clean($data['nachname']    ?? '');
$email       = filter_var(trim($data['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$telefon     = clean($data['telefon']     ?? '');
$behandlung  = clean($data['behandlung']  ?? 'Nicht angegeben');
$datum       = clean($data['datum']       ?? 'Nicht angegeben');
$beschwerden = clean($data['beschwerden'] ?? 'Keine Beschreibung');

// ── Pflichtfelder prüfen ─────────────────────────────────────────────────────
if (!$vorname || !$nachname || !$email || !$telefon) {
    http_response_code(422);
    echo json_encode(['success' => false, 'error' => 'Pflichtfelder fehlen']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'error' => 'Ungültige E-Mail-Adresse']);
    exit;
}

// ── Spam-Schutz: einfacher Honeypot (optional im JS ergänzen) ────────────────
if (!empty($data['website'])) {   // verstecktes Feld – kein Mensch füllt das aus
    http_response_code(200);
    echo json_encode(['success' => true]);   // Spam still – kein Feedback
    exit;
}

// ── E-Mail zusammenbauen ──────────────────────────────────────────────────────
$betreff = BETREFF_PREFIX . $anrede . ' ' . $vorname . ' ' . $nachname;

$body  = "=== NEUE TERMINANFRAGE – dr-elmarto.at ===\n\n";
$body .= "Datum der Anfrage: " . date('d.m.Y H:i') . " Uhr\n";
$body .= str_repeat('-', 50) . "\n\n";
$body .= "PATIENT\n";
$body .= "Anrede:    $anrede\n";
$body .= "Vorname:   $vorname\n";
$body .= "Nachname:  $nachname\n";
$body .= "E-Mail:    $email\n";
$body .= "Telefon:   $telefon\n\n";
$body .= str_repeat('-', 50) . "\n\n";
$body .= "TERMINWUNSCH\n";
$body .= "Wunschdatum:    $datum\n";
$body .= "Behandlungsart: $behandlung\n\n";
$body .= "BESCHWERDEN / ANLIEGEN\n";
$body .= "$beschwerden\n\n";
$body .= str_repeat('-', 50) . "\n";
$body .= "Diese Anfrage wurde über das Webformular auf dr-elmarto.at gesendet.\n";
$body .= "Antworten Sie direkt an: $email\n";

// ── Mail-Header ───────────────────────────────────────────────────────────────
$headers  = "From: " . ABSENDER_NAME . " <" . ABSENDER_MAIL . ">\r\n";
$headers .= "Reply-To: $vorname $nachname <$email>\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "Content-Transfer-Encoding: 8bit\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

// ── Senden ────────────────────────────────────────────────────────────────────
$ok = mail(EMPFAENGER, '=?UTF-8?B?' . base64_encode($betreff) . '?=', $body, $headers);

// ── Bestätigungs-Mail an Patienten (optional) ─────────────────────────────────
if ($ok) {
    $conf_betreff = '=?UTF-8?B?' . base64_encode('Ihre Terminanfrage bei OA Dr. El Marto – Wien') . '?=';
    $conf_body    = "Sehr geehrte/r $anrede $vorname $nachname,\n\n";
    $conf_body   .= "vielen Dank für Ihre Terminanfrage. Wir haben Ihre Anfrage erhalten und\n";
    $conf_body   .= "melden uns so schnell wie möglich – in der Regel innerhalb von 24 Stunden\n";
    $conf_body   .= "per E-Mail oder Telefon bei Ihnen.\n\n";
    $conf_body   .= "Ihre Anfrage im Überblick:\n";
    $conf_body   .= "─────────────────────────────\n";
    $conf_body   .= "Behandlungsart: $behandlung\n";
    $conf_body   .= "Wunschdatum:    $datum\n\n";
    $conf_body   .= "Für dringende Anliegen erreichen Sie uns telefonisch:\n";
    $conf_body   .= "+43 664 328 44 65 (24/7)\n\n";
    $conf_body   .= "Mit freundlichen Grüßen\n";
    $conf_body   .= "OA Dr. med. univ. Shady El Marto\n";
    $conf_body   .= "Kniechirurg & Facharzt für Orthopädie & Traumatologie\n";
    $conf_body   .= "Am Hof 11/9 · 1010 Wien\n";
    $conf_body   .= "https://www.dr-elmarto.at\n\n";
    $conf_body   .= "─────────────────────────────\n";
    $conf_body   .= "Diese Nachricht wurde automatisch versandt. Bitte antworten Sie nicht auf diese E-Mail.\n";

    $conf_headers  = "From: OA Dr. Shady El Marto <" . EMPFAENGER . ">\r\n";
    $conf_headers .= "Reply-To: " . EMPFAENGER . "\r\n";
    $conf_headers .= "MIME-Version: 1.0\r\n";
    $conf_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $conf_headers .= "Content-Transfer-Encoding: 8bit\r\n";

    mail($email, $conf_betreff, $conf_body, $conf_headers);
}

// ── Antwort an Frontend ───────────────────────────────────────────────────────
if ($ok) {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'E-Mail erfolgreich gesendet']);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'E-Mail konnte nicht gesendet werden. Bitte rufen Sie uns an.'
    ]);
}

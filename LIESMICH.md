# Website Praxis für Heilmassage – Matthias Schmid

Statische Website (HTML/CSS/JS, kein Build-Prozess). Zum Ansehen einfach `index.html`
im Browser öffnen. Zum Veröffentlichen den kompletten Ordner auf den Webspace laden.

## Aufbau

```
index.html              Startseite (Hero-Foto mit Verdunkelungsverlauf)
behandlungen.html       7 Behandlungen mit Ankerlinks + FAQ
preise.html             Preistabellen + Hinweise
ueber-mich.html         Portrait, Qualifikation, Praxis, Bewertungs-Platzhalter
termin.html             Anfrageformular + Kontaktdaten + Karte
impressum.html          § 5 ECG / Mediengesetz (Entwurf)
datenschutz.html        DSGVO (Entwurf)
barrierefreiheit.html   Erklärung zur Barrierefreiheit (Entwurf)

styles.css              Alle Styles, Design-Tokens ganz oben
script.js               Mobile-Menü, Header beim Scrollen, Formular
logo.svg                Logo dunkel · logo-hell.svg für dunkle Flächen
favicon.svg             Favicon
bilder/                 Alle Bilder als WebP, optimiert
robots.txt, sitemap.xml SEO-Grundlagen
```

## Schrift und Symbole

Schrift: **Lato** (Google Fonts), Fallback **Poppins**, danach Systemschriften.
Überschriften laufen in Lato 300, Auszeichnungen und Buttons in Lato 700.

Symbole: 15 Icons liegen als Inline-SVG-Sprite ganz oben in jeder Seite
(`<symbol id="i-…">`). Verwendung im Markup:

```html
<svg class="icon" aria-hidden="true"><use href="#i-spa"/></svg>
```

Verfügbar: `i-massage`, `i-spa`, `i-heart`, `i-medical`, `i-cupping`, `i-tape`,
`i-light`, `i-calendar`, `i-phone`, `i-mail`, `i-pin`, `i-clock`, `i-check`,
`i-euro`, `i-info`. Die Symbole übernehmen Farbe und Größe vom Elternelement.

Wichtig: Wird ein neues Symbol gebraucht, muss es in **jeder** HTML-Datei in den
Sprite-Block eingetragen werden (der Sprite ist bewusst inline, damit die Seiten
auch lokal per Doppelklick funktionieren).

## Offene Punkte vor dem Livegang

Alle offenen Stellen sind auf den Seiten **rot markiert** (`[bitte ergänzen]`).
So findest du sie: im Editor nach `class="tbd"` suchen.

### Inhalte
- [ ] Telefonnummer, E-Mail-Adresse, Öffnungszeiten (kommen im Footer jeder Seite vor)
- [ ] Anfahrtshinweis (U-Bahn, Straßenbahn, Parkmöglichkeit)
- [ ] Kinesiotaping: Beschreibung, Dauer und Preis gegenprüfen
- [ ] Repuls-Lichttherapie: Text fachlich gegenprüfen (basiert auf Herstellerangaben, keine Heilversprechen)
- [ ] Narbenentstörung: Dauer und Preis festlegen
- [ ] Zahlungsmöglichkeiten und Stornobedingungen (Preisseite)
- [ ] Ausbildungsstätte, Abschlussjahr, Fortbildungen (Über mich)
- [ ] Bewertungs-Bereich auf „Über mich“: entweder echte Google-Rezensionen einbinden
      oder den Abschnitt entfernen – keine erfundenen Testimonials

### Technik
- [ ] Domain überall eintragen: `canonical`, `og:image`, `sitemap.xml`, `robots.txt`
      (aktuell steht überall `https://www.beispiel-domain.at/`)
- [ ] Terminformular mit einem Versandweg verbinden (Formular-Dienst des Hosters
      oder Buchungstool). Aktuell zeigt es nur eine Bestätigung an und versendet nichts.
- [ ] SSL-Zertifikat aktivieren, HTTP auf HTTPS weiterleiten
- [ ] Empfehlung Datenschutz: Google Fonts lokal einbinden statt von Google laden
- [ ] Empfehlung Datenschutz: Google-Maps-Karte erst nach Zustimmung laden
      („Zwei-Klick-Lösung“) oder durch einen Textlink ersetzen
- [ ] Copyright-Jahr im Footer aktuell halten

### Recht
- [ ] Impressum, Datenschutz und Barrierefreiheitserklärung sind sorgfältige Entwürfe,
      **keine Rechtsberatung**. Vor der Veröffentlichung von einer Rechtsanwältin /
      einem Rechtsanwalt oder der Wirtschaftskammer Wien prüfen lassen.

## Was bereits umgesetzt ist

**SEO** – eine H1 pro Seite, saubere Überschriftenstruktur, eigene Meta-Titles und
-Descriptions (alle in der empfohlenen Länge), NAP-Daten konsistent im Footer,
Google-Maps-Einbindung, beschreibende Alt-Texte, interne Verlinkung
Behandlungen → Preise → Termin, WebP-Bilder (alle unter 90 KB), Mobile-First.

**GEO** – kurze, in sich abgeschlossene Absätze; Fakten explizit ausformuliert;
pro Leistung je ein Satz zu Definition, Eignung und Wirkung; durchgängig
einheitliche Begriffe.

**AEO** – FAQ-Bereiche auf Startseite, Behandlungen und Preise, jeweils mit
`FAQPage`-Markup. Antworten in 1–3 Sätzen.

**EEAT** – echtes Portraitfoto, Berufsbezeichnung prominent, Hinweis auf die
Zusammenarbeit mit Sportorthopäde und Physiotherapeut, Qualifikationsliste,
Bewertungs-Platzhalter, transparente Kontaktangaben.

**Schema.org** – `MedicalBusiness` (Startseite), `FAQPage` (drei Seiten),
`Person` (Über mich), `OfferCatalog` (Preise).

**Barrierefreiheit** – Sprunglink, Tastaturbedienung, sichtbarer Fokus,
Kontraste nach WCAG AA, FAQ als natives `<details>` (funktioniert ohne JavaScript),
`prefers-reduced-motion` berücksichtigt.

**KI-Kennzeichnung** – Hinweis nach Art. 50 der EU-KI-Verordnung im Footer
jeder Seite sowie im Impressum.

## Bilder

Alle Bilder wurden aus dem übergeordneten Ordner übernommen, zugeschnitten,
auf WebP konvertiert und komprimiert. Bei zwei Bildern wurde ein sichtbares
KI-Wasserzeichen weggeschnitten. Der gesamte Bilderordner ist rund 360 KB groß.

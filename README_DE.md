# Maps_XH

Das Plugin ermöglicht die Anzeige von [OpenStreetMap](https://www.openstreetmap.org/)
Landkarten auf einer CMSimple_XH Website, ohne dass man etwas mit JavaScript basteln muss.
Solche Landkarten sind nützlich für eine "So können sie uns finden" Seite,
aber können auch für andere Zwecke genutzt werden.
Das Plugin ist per Voreinstellung auf Datenschutz ausgelegt, d.h. keine Daten
werden an fremde Server übermittelt, bevor nicht explizit zugestimmt wurde.
Die Landkarten können eine beliebige Anzahl von Markierungen (optional mit
Info-Text) enthalten, und grundlegender Import von GeoJSON Features ist möglich.

- [Voraussetzungen](#voraussetzungen)
- [Download](#download)
- [Installation](#installation)
- [Einstellungen](#einstellungen)
- [Verwendung](#verwendung)
  - [Definition von Landkarten](#definition-von-landkarten)
  - [Import von GeoJSON](#import-von-geojson)
- [Fehlerbehebung](#fehlerbehebung)
- [Lizenz](#lizenz)
- [Danksagung](#danksagung)

## Voraussetzungen

Maps_XH ist ein Plugin für [CMSimple_XH](https://cmsimple-xh.org/de/).
Es benötigt CMSimple_XH ≥ 1.7.0 und PHP ≥ 8.0.0.
Maps_XH benötigt weiterhin [Plib_XH](https://github.com/cmb69/plib_xh) ≥ 1.9;
ist dieses noch nicht installiert (see *Einstellungen*→*Info*),
laden Sie das [aktuelle Release](https://github.com/cmb69/plib_xh/releases/latest)
herunter, und installieren Sie es.

## Download

Das [aktuelle Release](https://github.com/cmb69/maps_xh/releases/latest)
kann von Github herunter geladen werden.

## Installation

Die Installation erfolgt wie bei vielen anderen CMSimple_XH-Plugins auch.

1. Sichern Sie die Daten auf Ihrem Server.
1. Entpacken Sie die ZIP-Datei auf Ihrem Rechner.
1. Laden Sie das ganzen Ordner `maps/` auf Ihren Server in das
   `plugins/` Verzeichnis von CMSimple_XH  hoch.
1. Machen Sie die Unterordner `config/`, `css/` und `languages/`
   beschreibbar.
1. Prüfen Sie unter `Plugins` → `Maps` im Administrationsbereich,
   ob alle Voraussetzungen erfüllt sind.

## Einstellungen

Die Plugin-Konfiguration erfolgt wie bei vielen anderen
CMSimple_XH-Plugins auch im Administrationsbereich der Website.
Gehen Sie zu `Plugins` → `Maps`.

Sie können die Voreinstellungen von Maps_XH unter
`Konfiguration` ändern. Hinweise zu den Optionen werden beim
Überfahren der Hilfe-Icons mit der Maus angezeigt.

Die Lokalisierung wird unter `Sprache` vorgenommen. Sie können die
Sprachtexte in Ihre eigene Sprache übersetzen, falls keine
entsprechende Sprachdatei zur Verfügung steht, oder diese Ihren
Wünschen gemäß anpassen.

Das Aussehen von Maps_XH kann unter `Stylesheet` angepasst werden.

## Verwendung

Um eine Landkarte auf einer Seite anzuzeigen:

    {{{maps('name')}}}

Um eine Landkarte im Template anzuzeigen:

    <?=maps('name')?>

Wobei `name` der Name der Landkarte ist. Details sind unter
[Definition von Landkarten](#definition-von-landkarten) zu finden.

Bei Wechsel in den Ansichtsmodus, wird, wenn der Kachel-Datenschutz aktiviert
ist (`Plugins` → `Maps` → `Konfiguration` → `Tile` → `Privacy`), was empfohlen
wird, nicht gleich die Karte, sondern nur ein grauer Bereich mit den Markierungen
(falls welche definiert wurden) angezeigt. Darunter befindet sich ein Formular
wo der Datenübertragung zunächst zugestimmt werden muss, um die Landkarte
vollständig anzuzeigen. Dies verhält sich ebenso für Besucher der Website.

**Bitte beachten Sie die Nutzungsbedingungen der Kachel-Anbieter.**
Das Plugin tut sein Möglichstest diese einzuhalten, aber schließlich liegt es an
Ihnen dies sicherzustellen. Für den voreingestellten Kachel-Anbieter siehe
<https://operations.osmfoundation.org/policies/tiles/>.

### Definition von Landkarten

Im Plugin-Backend (`Plugins` → `Map` → `Administration`) können die Landkarten
definiert werden. Die Benutzung sollte selbst erklärend sein, aber ein paar
Hinweise scheinen angebracht:

* Jede Landkarte hat einen eindeutigen Namen, under dem sie im `content/` Ordner
  gespeichert wird. Der Name kann nur durch Umbennung der entsprechenden Datei
  per FTP geändert werden.

* Die Koordinaten (Breiten-/Längengrad) bestimmen den Mittelpunkt der Landkarte,
  und werden als Dezimalzahlen (nicht Grad und Bogenminuten) angegeben.
  Eine Websuche kann nützlich sein, um die Koordinaten für den gewünschten Ort
  zu finden.

* Zoom-Stufe 0 bedeutet die ganze Welt; Stufe 20 ist ungefähr ein Gebäude.
  [Definition der Zoom-Stufen](https://wiki.openstreetmap.org/wiki/Zoom_levels).

* Das Seitenverhältnis bestimmt die Höhe der Landkarte, wenn sie angezeigt wird.
  Ihre Breite ist immer 100%.

* Es kann eine beliebige Anzahl von Markierungen definiert werden, die an den
  angegebenen Koordinaten angezeigt werden.  Diese Markierung können Info-Text
  enthalten, der angezeigt wird, wenn der Marker angeklickt wird, oder, wenn die
  entsprechende Checkbox angehakt ist, angezeigt wird, wenn die Karte angezeigt
  wird.

### Import von GeoJSON

Der Import von sogenannten [GeoJSON](https://geojson.org/) Features wird
unterstützt; nur Punkte (points) werden berücksichtigt, und diese werden als
Markierungen importiert. Das Import-Formular hat ein Feld für das `GeoJSON`
(kopieren und einfügen kann nützlich sein), und ein `Template` Feld, das
verwendet kann, um die Markierungsinfo mit Daten des GeoJSON Features zu befüllen.
Das Template erwartet HTML mit Platzhaltern, die durch die sogenannten Eigenschaften
(properties) des GeoJSON Features ersetzt werden. Existiert die Eigenschaft nicht,
dann findet keine Ersetzung statt. Ein Platzhalter ist der Name einer Eigenschaft,
der in geschweifte Klammern eingeschlossen ist, z.B. `{name}`.
Es ist zu beachten, dass gewählt werden kann, dass bestehende Markierung ersetzt
werden, was nützlich ist wenn ein aktualisiertes GeoJSON erneut import wird.

## Fehlerbehebung

Melden Sie Programmfehler und stellen Sie Supportanfragen entweder auf
[Github](https://github.com/cmb69/maps_xh/issues) oder im
[CMSimple_XH Forum](https://cmsimpleforum.com/).

## Lizenz

Maps_XH ist freie Software. Sie können es unter den Bedingungen der
GNU General Public License, wie von der Free Software Foundation
veröffentlicht, weitergeben und/oder modifizieren, entweder gemäß
Version 3 der Lizenz oder (nach Ihrer Option) jeder späteren Version.

Die Veröffentlichung von Maps_XH erfolgt in der Hoffnung, dass es
Ihnen von Nutzen sein wird, aber ohne irgendeine Garantie, sogar ohne
die implizite Garantie der Marktreife oder der Verwendbarkeit für einen
bestimmten Zweck. Details finden Sie in der GNU General Public License.

Sie sollten ein Exemplar der GNU General Public License zusammen mit
Maps_XH erhalten haben. Falls nicht, siehe <https://www.gnu.org/licenses/>.

Copyright © Christoph M. Becker

## Danksagung

Maps_XH wurde von *hufnala* angeregt.

Das Plugin wird angetrieben von [OpenStreetMap](https://www.openstreetmap.org/)
und [Leaflet](https://leafletjs.com/).
Vielen Danke für die Bereitstellung dieser großartigen Tools und Dienste für
die Gemeinschaft!

Das Plugin-Icon wurde von [Freepik - Flaticon](https://www.flaticon.com/free-icons/street-map) gestaltet.
Vielen Dank für die freie Verfügbarkeit dieses Icons.

Vielen Dank an die Community im
[CMSimple_XH-Forum](https://www.cmsimpleforum.com/) für Hinweise,
Anregungen und das Testen.

Und zu guter letzt vielen Dank an [Peter Harteg](https://www.harteg.dk/),
den „Vater“ von CMSimple, und allen Entwicklern von [CMSimple_XH](https://www.cmsimple-xh.org/de/)
ohne die es dieses phantastische CMS nicht gäbe.

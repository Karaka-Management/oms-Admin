# Einleitung

Das **Admin**-Modul ist eines der unverzichtbaren Kernmodule, das immer benötigt wird. Dieses Modul verwaltet grundlegende Konten-, Gruppen- und Modulverwaltungsaufgaben.

## Zielgruppe

Die Zielgruppe für dieses Modul ist jeder, da jede Anwendung dieses Modul haben muss. Allerdings sollten hauptsächlich Administratoren Zugriffsberechtigungen für dieses Modul haben, da grundlegende Anwendungseinstellungen geändert werden können.

# Setup

Für dieses Modul sind keine zusätzlichen Einrichtungsanforderungen erforderlich, da es während des Anwendungsinstallationsprozesses installiert wird. Dieses Modul kann nicht deinstalliert werden, wenn es manuell von der Festplatte gelöscht wird. Bitte laden Sie das Modul manuell von der Seite herunter und legen Sie es in das `Modules/`-Verzeichnis.

# Features

## Anwendungseinstellungen

Das Modul bietet grundlegende Anwendungseinstellungen wie:

### Sicherheit

Die folgenden Sicherheitseinstellungen sind nur anpassbar. Viele Sicherheitseinstellungen sind innerhalb der Anwendung festgelegt, und zusätzliche Sicherheitseinstellungen (z. B. iptables) sollten auf Serverseite vorgenommen werden.

* Passwortstruktur
* Intervall für Passwortänderungen
* Automatische Updates für Module

### Lokalisierung

Die Lokalisierungseinstellungen gelten nur für den Server. Benutzerkonten können ihre eigenen Lokalisierungseinstellungen haben, was besonders wichtig für den internationalen Gebrauch ist.

* Standort
* Sprache
* Zeitformat
* Numerisch (Zahlen- und Währungsformat)
* Einheiten (Gewicht, Geschwindigkeit, Länge, Temperatur usw.)

## Kontoverwaltung

Die Kontoverwaltung ermöglicht das Erstellen, Ändern und Löschen von Konten. Konten können Gruppen und individuellen Berechtigungen zugewiesen werden. Es ist einfach zu sehen, welchen Gruppen und Berechtigungen ein Konto zugewiesen ist. Jedes Konto hat eine numerische ID größer als 0.

### Kontotyp

Konten können die folgenden Typen haben:

* Person
* Organisation

Dies ermöglicht es Organisationen, ein Konto zu haben, und auch normale Benutzer. Es ist auch möglich, ein Konto einer Organisation zuzuweisen, was die Berechtigungsverwaltung in einer Organisation ermöglicht.

Der Anwendungsfall hierfür könnte ein Kundenkonto für ein Unternehmen sein und Benutzerkonten, die dem Unternehmen zugewiesen sind und jeweils unterschiedliche Berechtigungen innerhalb des Unternehmens haben. Die Einkaufsabteilung eines Unternehmens könnte z. B. ihre Bestellungen sehen können, während nur die Finanzabteilung dieses Unternehmens die Verbindlichkeiten ihres Unternehmens sehen darf.

### Kontostatus

Konten können die folgenden Status haben:

* Aktiv (Anmeldung möglich)
* Inaktiv (Anmeldung nicht möglich)
* Gesperrt (Anmeldung nicht möglich)
* Timeout (Anmeldung möglich in x Minuten)

Der Status repräsentiert nicht die Kontenaktivität, sondern dient nur dazu, die Anmeldung bei der Anwendung zu erlauben oder zu verhindern. Nur aktive Konten können sich bei der Anwendung anmelden. Es ist oft erforderlich, Konten im System für Interaktionen zu erstellen, aber deren Anmeldung im System zu verhindern. Dies kann durch einfaches Zuweisen des inaktiven Status erreicht werden.

## Gruppenverwaltung

Die Gruppenverwaltung ermöglicht das Erstellen, Ändern und Löschen von Gruppen. Gruppen können anderen Gruppen zugewiesen werden, wodurch die Gruppe Berechtigungen von den anderen Gruppen erbt. Es ist auch möglich, individuelle Berechtigungen einer Gruppe zuzuweisen. In der Gruppe können Konten hinzugefügt werden, und es ist einfach zu sehen, welche Konten Teil der Gruppe sind.

Gruppen haben eine numerische ID größer als 0. Jedes Modul hat seinen eigenen Berechtigungs-/Gruppenbereich. Module können bei der Installation Gruppen vordefinieren, um deren Verwendung zu erleichtern. Jedes Modul kann bis zu 99.999 Gruppen UND Berechtigungen haben.

## Modulverwaltung

Die Modulverwaltung ermöglicht das Installieren, Aktualisieren, Löschen und Konfigurieren von Modulen. Die Konfiguration jedes Moduls kann je nach Funktionalität des Moduls unterschiedlich sein.

Module können entweder manuell durch Hochladen des Moduls direkt in das `Modules/`-Verzeichnis oder über die Online-Installation installiert werden. Die Online-Installation erfordert das PHP-Modul `curl`.

Im Modul können Sie sehen, welche Gruppen Berechtigungen für das Modul haben, welche Berechtigungen für das Modul verfügbar sind und welche Auswirkungen sie haben.

# Empfehlung

Andere Module, die gut mit diesem Modul zusammenarbeiten, sind:

* [Job]({/}?id=Job)
* [Monitoring]({/}?id=Monitoring)
* [Backup]({/}?id=Backup)
# Einstellungen

## Allgemein

Im Admin-Modul unter `Allgemein` können die globalen Einstellungen vorgenommen werden.

### Organisation

Standard-Einheit.

![Allgemeine Einstellungen](Modules/Admin/Docs/Help/img/settings/settings.png)

### Sicherheit

Im Bereich Sicherheit können Sie die globalen Sicherheitseinstellungen definieren und ändern. Diese Einstellungen werden für jeden Benutzer verwendet.

![Allgemeine Einstellungen](Modules/Admin/Docs/Help/img/settings/settings_security.png)

#### Passwort Regex

In diesem Feld kann die Passwortstruktur definiert werden, die für jedes Konto erforderlich ist. Beispiele sind:

##### Passwort Beispiel 1

Mindestens 8 Zeichen, davon mindestens ein numerischer Wert, ein Kleinbuchstabe, ein Großbuchstabe, ein Sonderzeichen

```
^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&;:\(\)\[\]=\{\}\+\-])[.]{8,}
```

##### Passwort Beispiel 2

Mindestens 8 Zeichen, davon mindestens ein numerischer Wert, ein Großbuchstabe, ein Sonderzeichen

```
^(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&;:\(\)\[\]=\{\}\+\-])[.]{8,}
```

##### Passwort Beispiel 3

Mindestens 8 Zeichen, davon mindestens ein numerischer Wert, ein Sonderzeichen

```
^(?=.*\d)(?=.*[$@$!%*?&;:\(\)\[\]=\{\}\+\-])[.]{8,}
```

##### Passwort Beispiel 4

Mindestens 8 Zeichen einschließlich mindestens eines Sonderzeichens

```
^(?=.*[$@$!%*?&;:\(\)\[\]=\{\}\+\-])[.]{8,}
```

##### Passwort Beispiel 5

Mindestens 8 Zeichen

```
^[.]{8,}
```

##### Passwort Beispiel 6

Mindestens 12 Zeichen

```
^[.]{12,}
```

## Lokalisierung

Auf der Registerkarte „Lokalisierung“ können Sie die Standardeinstellungen für die Lokalisierung festlegen. Beachten Sie, dass die Benutzer möglicherweise andere Lokalisierungseinstellungen haben als die Standardeinstellungen. Diese Lokalisierungseinstellungen sind nur wichtig, um einen Fallback zu bieten, wenn die Benutzer-Lokalisierungseinstellungen nicht funktionieren.

[Lokalisierungseinstellungen](Module/Admin/Docs/Help/img/settings/localization.png)

### Standardeinstellungen

Im Feld Standardeinstellungen können Sie eine Standard-Lokalisierungskonfiguration auswählen, die Sie anschließend anpassen können.

[Localization Load](Module/Admin/Docs/Help/img/settings/localization_load.png)
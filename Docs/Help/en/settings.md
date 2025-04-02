# Settings

## General

In the admin module under `General` the global settings can be set.

### Organization

Default unit.

![General Settings](Modules/Admin/Docs/Help/img/settings/settings.png)

### Security

In the security section it's possible to define and modify the global security settings. These settings will be used for every user.

![General Settings](Modules/Admin/Docs/Help/img/settings/settings_security.png)

#### Password Regex

In this field the password structure can be defined that is required by every account. Examples are:

##### Password Example 1

At least 8 characters including at least one numeric value, one lower letter, one upper letter, one special char

```
^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&;:\(\)\[\]=\{\}\+\-])[.]{8,}
```

##### Password Example 2

At least 8 characters including at least one numeric value, one upper letter, one special char

```
^(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&;:\(\)\[\]=\{\}\+\-])[.]{8,}
```

##### Password Example 3

At least 8 characters including at least one numeric value, one special char

```
^(?=.*\d)(?=.*[$@$!%*?&;:\(\)\[\]=\{\}\+\-])[.]{8,}
```

##### Password Example 4

At least 8 characters including at least one special char

```
^(?=.*[$@$!%*?&;:\(\)\[\]=\{\}\+\-])[.]{8,}
```

##### Password Example 5

At least 8 characters

```
^[.]{8,}
```

##### Password Example 6

At least 12 characters

```
^[.]{12,}
```

## Localization

In the localization tab it's possible to define the default localization settings. Be aware that users may have localization settings different from the default settings. These localization settings are only important to provide a fallback if the user localization settings are not working.

![Localization Settings](Modules/Admin/Docs/Help/img/settings/localization.png)

### Defaults

In the defaults field you can select a default localization configuration which you can adjust afterwards.

![Localization Load](Modules/Admin/Docs/Help/img/settings/localization_load.png)
#### Nom

symfony_validator


#### Description

Applique un validateur symfony et vérifie si la valeur répond au exigeance de celui-ci


#### Options

| Nom              | Description                                                             |
|------------------|-------------------------------------------------------------------------|
| validator        | FQCN de la constraint à apliquer                                        |
| options          | options à passer à la contrainte                                        |
| groups           | groupes de validation à utiliser                                        |

#### Example

```yaml
type: symfony_validator
options:
    validator: 'Symfony\Component\Constraint\Validator\NotBlank'
    options: []
    groups: []
value: '@[data][phrase]'
```
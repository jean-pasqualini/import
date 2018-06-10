#### Nom

strpos


#### Description

Constate ou non la présence de la sous chaine attendu


#### Options

| Nom              | Description                                                             |
|------------------|-------------------------------------------------------------------------|
| substring        | sous-chaine à trouver dans la valeur                                    |

#### Example

```yaml
type: strpos
options:
    substring: 'maison'
value: '@[data][phrase]'
```
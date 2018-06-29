#### Nom

regex


#### Description

Constate ou non que le pattern attendu correspond à la valeur


#### Options

| Nom              | Description                                                             |
|------------------|-------------------------------------------------------------------------|
| pattern          | pattern à tester                                                        |

#### Example

```yaml
type: regex
options:
    pattern: '/[0-9]{2}/i'
value: '@[data][refext]'
```
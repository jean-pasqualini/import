#### Nom

value


#### Description

compare la valeur avec celle attendu de manière sticte.


#### Options

| Nom              | Description                                                             |
|------------------|-------------------------------------------------------------------------|
| expected         | valeur à comparer de manière stricte (type compris)                     |

#### Example

```yaml
type: value
options:
    expected: 'product'
value: '@[data][type]'
```
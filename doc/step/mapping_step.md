#### Darkilliant\ProcessBundle\Step\MappingStep

##### Rôle 

Permet de changer la structure d'un tableau d'un format vers un autre

##### Options

| Nom                  | Description                             |
|----------------------|-----------------------------------------|
| mapping              | mapping                                 |

##### Examples

```yaml
service: 'Darkilliant\ProcessBundle\Step\MappingStep'
options:
    mapping:
        title: '@[data][title]'
        ean: 'ean'
        price_ttc: '1'
        extra:
            picture: '@[data][picture]'
        boutique:
            name: '@[data][boutique]'
```

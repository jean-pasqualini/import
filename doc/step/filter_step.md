#### Darkilliant\ProcessBundle\Step\FilterStep

##### Rôle 

Filter les données dans le pipe

##### Options

| Nom                         | Description                                                          |
|-----------------------------|----------------------------------------------------------------------|
| filters                     | listes de filtres à appliquer                                        |
| filters[].type              | non du filtre à appliquer                                            |
| filters[].value             | valeur que le filtre doit tester                                     |
| filters[].options           | options à passer au filtre                                           |
| filters[].valid_when_return | si ommis true, valeur attendu pour considèrer la valeur comme valide |

#### Example

```yaml
service: 'Darkilliant\ProcessBundle\Step\FilterStep'
options:
    filters:
        -
            type: 'value'
            value: '@[data][name]'
            options:
                expected: 'bleu'
```
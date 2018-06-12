#### Darkilliant\ProcessBundle\Step\PredefinedDataStep

##### Rôle 

prédéfinir des données dans le pipe

##### Options

| Nom  | Description                                  |
|------|----------------------------------------------|
| data | valeur à passer à ProcessState::setData(...) |

##### Examples

```yaml
service: 'Darkilliant\ProcessBundle\Step\PredefinedDataStep'
options:
    data:
        - {ean: 'ean', label_tag_1: 'one', label_tag_2: 'two'}
```
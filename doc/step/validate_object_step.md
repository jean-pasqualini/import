#### Darkilliant\ProcessBundle\Step\ValidateObjectStep

##### Rôle 

Valide un objet avec le validateur de symfony

##### Options

| Nom             | Description                                               |
|-----------------|-----------------------------------------------------------|
| groups          | groups de validation à utiliser                           |

##### Example

```yaml
service: 'Darkilliant\ProcessBundle\Step\ValidateObjectStep'
options:
    groups: ['demo']
```
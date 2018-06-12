#### Darkilliant\ProcessBundle\Step\LaunchProcessStep

##### Rôle 

lancer un traitement

##### Options

| Nom         | Description                                             |
|-------------|---------------------------------------------------------|
| process     | nom du traitement à lancer                              |

##### Examples

```yaml
service: 'Darkilliant\ProcessBundle\Step\LaunchProcessStep'
options:
    process: 'import_product'
```
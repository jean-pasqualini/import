#### Darkilliant\ImportBundle\Step\LoadObjectNormalizedStep

##### Rôle 

onvertir un tableau php en entité doctrine avec ses relations

##### Options

| Nom        | Description                                           |
|------------|-------------------------------------------------------|
| class_name | Classe à constuire et à remplir avec les data du pipe |

##### Examples

```yaml
service: 'Darkilliant\ImportBundle\Step\LoadObjectNormalizedStep'
options:
    entity_class: 'App\Entity\Product'
    serializer: 'jms_serializer'
```
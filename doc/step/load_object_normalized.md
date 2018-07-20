#### Darkilliant\ImportBundle\Step\LoadObjectNormalizedStep

##### Rôle

onvertir un tableau php en entité doctrine avec ses relations

##### Options

| Nom        | Description                                           |
|------------|-------------------------------------------------------|
| class_name | Classe à constuire et à remplir avec les data du pipe |

##### Examples

Configuration coté step

```yaml
service: 'Darkilliant\ImportBundle\Step\LoadObjectNormalizedStep'
options:
    entity_class: 'App\Entity\Product'
    serializer: 'jms_serializer'
```

Pour permettre la résolution dynamique des entité il faut rajouter une configuration du type

```yaml
darkilliant_import:
    fields_entity_resolver:
        'App\Entity\Product':
            strategy: where
            options:
                service: null # When use custom service (implement Darkilliant\ImportBundle\WhereBuilder\WhereBuilderInterface)
                fields: ['ean']
        'App\Entity\Boutique': ['name']
        'App\Entity\Tag': ['name']
        'App\Entity\Category':
            'external_id': 'externalId'
    cache_entity_resolver:
        'App\Entity\Product': true
```

Ceci permet au resolver de savoir par quel champ rechercher les objets en base.<br>
De plus un cache qui n'est actif sur aucune des entité par défaut est activable et permet de créer un index local afin d'éviter des selects qui sont source de lenteur.<br>
! Attention : Ce cache en contrepartie va augmenter l'empreinte mémoire.

Du coup pour réaliser un import, il suffit d'assembler,

Prenont l'exemple suivant,

### 1 / On importe une boutique depuis un tableau prédéfinit mais cela pourrais être très bien depuis un csv.

```yaml
darkilliant_process:
    process:
        # nom du traitement (c'est le nom à fournir à la commande process:run)
        create_boutique:
            # logger vers où les infos sur le traitement seront envoyé
            logger: 'monolog.logger.create_boutique'
            steps:
                - # On prédéfinir le tableau php qui réprésente la boutique
                    service: 'Darkilliant\ProcessBundle\Step\PredefinedDataStep'
                    options:
                        data: {name: 'maison'}
                - # On demande au noramlizer de symfony de convertir se tableau en entité doctrine
                    service: 'Darkilliant\ImportBundle\Step\LoadObjectNormalizedStep'
                    options:
                        entity_class: 'App\Entity\Boutique'
                - # On persiste cette entité doctrine en bdd
                    service: 'Darkilliant\ImportBundle\Step\DoctrinePersisterStep'
                    options:
                        batch_count: 20
                        whitelist_clear: ['AppBundle\Entity\Boutique']
```

### 2 / Pour le lancer c'est très simple,
```bash
$ bin/console process:run create_boutique -vv
```

### 3 / Pour faire provenir d'un csv,
Il suffirait de changer juste un peu,

Fichier csv actuel
```text
name
chocolat
lait
```

```yaml
darkilliant_process:
    process:
        # nom du traitement (c'est le nom à fournir à la commande process:run)
        create_boutique:
            # logger vers où les infos sur le traitement seront envoyé
            steps:
                - # On extrait chaque ligne du fichier csv
                    service: 'Darkilliant\ImportBundle\Step\CsvExtractorStep'
                    options:
                        filepath: '[chemindufichier]'
                - # On demande au noramlizer de symfony de convertir se tableau en entité doctrine
                    service: 'Darkilliant\ImportBundle\Step\LoadObjectNormalizedStep'
                    options:
                        entity_class: 'App\Entity\Boutique'
                - # On persiste cette entité doctrine en bdd
                    service: 'Darkilliant\ImportBundle\Step\DoctrinePersisterStep'
                    options:
                        batch_count: 20
                        whitelist_clear: ['AppBundle\Entity\Boutique']
```

### 4 / Dans le cas où le nommage des colonnes différent de ce qui est attente par le denormalizer de symfony
Il suffit de transformer le tableau php vers la structure attendu


Fichier csv actuel
```text
titre
chocolat
lait
```

! Dans ce cas, il serait possible au niveau de l'extractor de donner un nom différent au colonne mais cela sera moins flexible
En effet l'ordre des colonnes aurais pour effet de casser l'import, la on se mappe directement sur les vrai noms
De plus cela sera utile par la suite pour des cas plus complexe

```yaml
darkilliant_process:
    process:
        # nom du traitement (c'est le nom à fournir à la commande process:run)
        create_boutique:
            # logger vers où les infos sur le traitement seront envoyé
            steps:
                - # On extrait chaque ligne du fichier csv
                    service: 'Darkilliant\ImportBundle\Step\CsvExtractorStep'
                    options:
                        filepath: '[chemindufichier]'
                - # On modifie la structure du tableau
                    service: 'Darkilliant\ImportBundle\Step\MappingTransformerStep'
                    options:
                        mapping:
                            name: '@[data][titre]'
                - # On demande au noramlizer de symfony de convertir se tableau en entité doctrine
                    service: 'Darkilliant\ImportBundle\Step\LoadObjectNormalizedStep'
                    options:
                        entity_class: 'App\Entity\Boutique'
                - # On persiste cette entité doctrine en bdd
                    service: 'Darkilliant\ImportBundle\Step\DoctrinePersisterStep'
                    options:
                        batch_count: 20
                        whitelist_clear: ['AppBundle\Entity\Boutique']
```

### 5 / Afin d'éviter que l'import recrée la catégorie si elle existe déjà

Il suffit de lui dire qu'elle champ doit être utiliser pour retrouver l'entité en base à modifier.
Si l'entité n'existe pas il l'a créra.

```yaml
darkilliant_import:
    fields_entity_resolver:
        'App\Entity\Boutique': ['name']
```

### 6 / Si vous avez des relations
Il s'uffit de les définir et il créera les association où attachera celle qui l'aura trouvé en base

Fichier csv actuel
```text
parent réf;réf;titre
;c;chocolat
c;l;lait
```

Resolver,
```yaml
darkilliant_import:
    fields_entity_resolver:
        'App\Entity\Boutique':
            external_id: externalId
```

```yaml
darkilliant_process:
    process:
        # nom du traitement (c'est le nom à fournir à la commande process:run)
        create_boutique:
            # logger vers où les infos sur le traitement seront envoyé
            steps:
                - # On extrait chaque ligne du fichier csv
                    service: 'Darkilliant\ImportBundle\Step\CsvExtractorStep'
                    options:
                        filepath: '[chemindufichier]'
                - # On modifie la structure du tableau
                    service: 'Darkilliant\ImportBundle\Step\MappingTransformerStep'
                    options:
                        mapping:
                            name: '@[data][titre]'
                            external_id: '@[data][ref]'
                            parent:
                                value: 
                                    external_id: '@[data][parent_ref]'
                - # On demande au noramlizer de symfony de convertir se tableau en entité doctrine
                    service: 'Darkilliant\ImportBundle\Step\LoadObjectNormalizedStep'
                    options:
                        entity_class: 'App\Entity\Boutique'
                - # On persiste cette entité doctrine en bdd
                    service: 'Darkilliant\ImportBundle\Step\DoctrinePersisterStep'
                    options:
                        batch_count: 20
                        whitelist_clear: ['AppBundle\Entity\Boutique']
```

Dans cet example, il va modifier la catégorie s'il la retrouve par son id external sinon il va la créer,
Ensuite, il va chercher son parent en bdd et l'associé à son enfant.

### 7 / Valider les données du csv

```yaml
darkilliant_process:
    process:
        # nom du traitement (c'est le nom à fournir à la commande process:run)
        create_boutique:
            # logger vers où les infos sur le traitement seront envoyé
            steps:
                - # On extrait chaque ligne du fichier csv
                    service: 'Darkilliant\ImportBundle\Step\CsvExtractorStep'
                    options:
                        filepath: '[chemindufichier]'
                - # On valide les données
                    service: 'Darkilliant\ImportBundle\Step\MappingTransformerStep'
                    options:
                        mapping:
                            name: 
                                value: '@[data][titre]'
                                transformers: ['string']
                            external_id: 
                                value: '@[data][titre]'
                                transformers: ['external_id']
                            parent_ref: 
                                value: '@[data][parent_ref]'
                                transformers: ['string']
```

Ainsi si une ligne contient des données invalide, elle sera loggué et ne sera pas importer.

Vous pouvez ajouter vos propres transformer, voic la procédure,
 - ajouter une classe qui hérite de `Darkilliant\ImportBundle\Transformer\AbstractTransformer` 
 - déclarer en tant que service et la taggué (l'alias est le nom du transformer)
 
```yaml
    Darkilliant\ImportBundle\Transformer\StringTransformer:
        public: true
        tags:
          - { name: darkilliant_import_transformer, alias: string }
```

Voici une liste de transformers,

```
- boolean
- float
- remove_empty_in_array
- string
- array
- contains_key
- datetime
- integer
- not_empty_string
```
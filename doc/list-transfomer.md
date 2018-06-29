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
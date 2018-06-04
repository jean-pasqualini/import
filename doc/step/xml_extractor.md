####  Darkilliant\ImportBundle\Step\XmlExtractorStep

##### Rôle 

extraire chaque noeud d'un type particulier d'un fichier xml sous forme d'un tableau php

##### Options

| Nom       | Description                                |
|-----------|--------------------------------------------|
| filepath  | chemin complet du fichier xml à parcourir  |
| node_name | type de noeud à parcourir                  |

##### Examples

```yaml
service: 'Darkilliant\ImportBundle\Step\XmlExtractorStep'
options:
    filepath: 'test.xml'
    node_name: 'Sku'
    progress_bar: true
```
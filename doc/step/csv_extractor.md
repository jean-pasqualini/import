####  Darkilliant\ImportBundle\Step\CsvExtractorStep

##### Rôle 

extraire chaque ligne d'un fichier csv sous forme d'un tableau php

##### Options

| Nom             | Description                                     |
|-----------------|-------------------------------------------------|
| filepath        | chemin complet du fichier csv à parcourir       |
| delimiter       | séparateur de colonnes                          |
| colums_names    | nom des colonnes si différent de première ligne |
| skip_first_line | faut-il ignorer la première ligne ?             |

##### Examples

```yaml
service: Darkilliant\ImportBundle\Step\CsvExtractorStep
options:
    filepath: 'file.csv'
    delimiter: ';'
    colums_names: null # auto use first_line when use null
    skip_first_line: false
```
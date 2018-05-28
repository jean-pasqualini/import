Ce bundle se base sur un système de micro-tache qui mit bout à bout permette de créer un traitement complexe tel qu'un import.

Chaque tache doit être assez unitaire pour être utilisable pour faire tout et n'importe quoi,
- Sortir un [recipient] et y mettre un [ingredient]
- Faire fondre le contenu du [recipient]
- Faire cuirre le contenu du [recipient]
- Faire bouillir le contenu du [recipient]
- Plonger [ingredient] dans [recipient]
- Egouter [ingredient]
- Transposer [ingredient] dans [recipient]

C'est ensuite le contexte et la composition de ces taches qui vont permetre de faire une tache complexe.

Dans notre cas, on pourrai faire des pattes,

- Sortir une casserolle et y mettre de l'eau froide
- Faire bouillir le contenu de la casserolle
- Plonger des pattes dans la casserolle
- Faire Egouter le contenu
- Transposer le contenu dans un plat en verre

Comme on pourrais également faire un steak haché,

- Sortir une poelle et y mettre un steak hacké
- Faire cuire le contenu de la poelle
- Transposer le contenu dans une assiette

L'important est de bien maitriser cette découpe pour augmenter la possibilité d'utiliser ces micro-taches.

Un ensemble de tâche composer s'apelle un traitement (ou process en anglais)

Peut être avez vous remarquer que pour un import, 
- l'ingrédient sera une donnée qui va être transformer en objet au final
- le recipient sera ce tableau pour finir par être la bdd

Ce bundle dispose de micro-tache spécialiser pour de l'import de donnée dans une bdd.

Nous allons décrire les miro-taches disponible,


| Class                                                                                      | Descriptif                                                                    |
|--------------------------------------------------------------------------------------------|-------------------------------------------------------------------------------|
| [Darkilliant\ImportBundle\Step\SplitExcelStep](./step/split_excel.md)                      | découper un fichier excel en autant de fichiers csv qu'il ne dispose d'onglet |
| [Darkilliant\ImportBundle\Step\CsvExtractorStep](./step/csv_extractor.md)                  | extraire chaque ligne d'un fichier csv sous forme d'un tableau php            |
| [Darkilliant\ProcessBundle\Step\IterateArrayStep](./step/iterate_array.md)                 | parcourir un tableau php                                                      |
| [Darkilliant\ImportBundle\Step\MappingTransformerStep](./step/mapping_transformer.md)      | transformer un tableau php et le valider                                      |
| [Darkilliant\ImportBundle\Step\LoadObjectNormalizedStep](./step/load_object_normalized.md) | convertir un tableau php en entité doctrine avec ses relations                |
| [Darkilliant\ImportBundle\Step\DoctrinePersisterStep](./step/doctrine_persister.md)        | persister une entité doctrine en bdd                                          |
| [Darkilliant\ProcessBundle\Step\DebugStep](./step/debug.md)                                | affiches les données dans le pipe                                             |
| [Darkilliant\ProcessBundle\Step\LaunchProcessStep](./step/launch_process.md)               | lancer un traitement                                                          |
| [Darkilliant\ProcessBundle\Step\PredefinedDataStep](./step/predefined_data.md)             | prédéfinir des données dans le pipe                                           |

Si vous désirez ajouter une miro tache,<br>
il vous suffit de déclarer un service en public dont sa classe hérite de `Darkilliant\ProcessBundle\Step\AbstractConfigurableStep`.<br>
Le nom du service doit être le FQCN de la classe et être utiliser ainsi dans la configuration d'un traitement.
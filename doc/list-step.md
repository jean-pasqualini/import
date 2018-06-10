| Class                                                                                           | Descriptif                                                                    |
|-------------------------------------------------------------------------------------------------|-------------------------------------------------------------------------------|
| [Darkilliant\ImportBundle\Step\SplitExcelStep](./step/split_excel.md)                           | découper un fichier excel en autant de fichiers csv qu'il ne dispose d'onglet |
| [Darkilliant\ImportBundle\Step\CsvExtractorStep](./step/csv_extractor.md)                       | extraire chaque ligne d'un fichier csv sous forme d'un tableau php            |
| [Darkilliant\ProcessBundle\Step\IterateArrayStep](./step/iterate_array.md)                      | parcourir un tableau php                                                      |
| [Darkilliant\ImportBundle\Step\MappingTransformerStep](./step/mapping_transformer.md)           | transformer un tableau php et le valider                                      |
| [Darkilliant\ImportBundle\Step\LoadObjectNormalizedStep](./step/load_object_normalized.md)      | convertir un tableau php en entité doctrine avec ses relations                |
| [Darkilliant\ImportBundle\Step\DoctrinePersisterStep](./step/doctrine_persister.md)             | persister une entité doctrine en bdd                                          |
| [Darkilliant\ProcessBundle\Step\DebugStep](./step/debug.md)                                     | affiches les données dans le pipe                                             |
| [Darkilliant\ProcessBundle\Step\LaunchProcessStep](./step/launch_process.md)                    | lancer un traitement                                                          |
| [Darkilliant\ProcessBundle\Step\PredefinedDataStep](./step/predefined_data.md)                  | prédéfinir des données dans le pipe                                           |
| [Darkilliant\ProcessBundle\Step\ArrayBatchIterableStep](./step/array_batch_iterable_step.md)    | attend d'avoir x élement dans le pipe avant de balancer à l'étape suivante    |
| [Darkilliant\ProcessBundle\Step\LaunchIsolateProcessStep](./step/launch_isolate_process_step.md)| lance de manière simultané un même traitement sur plusieurs itération         |
| [Darkilliant\ProcessBundle\Step\UniqueFilterStep](./step/unique_filter_step.md)                 | supprime les doublons                                                         |
| [Darkilliant\ProcessBundle\Step\ValidateObjectStep](./step/validate_object_step.md)             | Valide un objet avec le validateur de symfony                                 |
| [Darkilliant\ProcessBundle\Step\FilterStep](./step/filter_step.md)                              | Filter les données dans le pipe                                               |

Si vous désirez ajouter une miro tache,<br>
il vous suffit de déclarer un service en public dont sa classe hérite de `Darkilliant\ProcessBundle\Step\AbstractConfigurableStep`.<br>
Le nom du service doit être le FQCN de la classe et être utiliser ainsi dans la configuration d'un traitement.

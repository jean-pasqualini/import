| Class                                                                                           | Itérable | Descriptif                                                                    |
|-------------------------------------------------------------------------------------------------|----------|-------------------------------------------------------------------------------|
| [Darkilliant\ImportBundle\Step\SplitExcelStep](./step/split_excel.md)                           |   OUI    | découper un fichier excel en autant de fichiers csv qu'il ne dispose d'onglet |
| [Darkilliant\ImportBundle\Step\CsvExtractorStep](./step/csv_extractor.md)                       |   OUI    | extraire chaque ligne d'un fichier csv sous forme d'un tableau php            |
| [Darkilliant\ImportBundle\Step\XmlExtractorStep](./step/xml_extractor.md)                       |   OUI    | extraire chaque noeud xml d'un type particulier sous forme d'un tableau php   |
| [Darkilliant\ProcessBundle\Step\IterateArrayStep](./step/iterate_array.md)                      |   OUI    | parcourir un tableau php                                                      |
| [Darkilliant\ImportBundle\Step\WhileStep](./step/while_step.md)                                 |   OUI    | Boucle sur les steps qu'il exécute j'usqu'a un max d'iteration ou de temps    |
| [Darkilliant\ImportBundle\Step\MappingTransformerStep](./step/mapping_transformer.md)           |   NON    | transformer un tableau php et le valider                                      |
| [Darkilliant\ImportBundle\Step\LoadObjectNormalizedStep](./step/load_object_normalized.md)      |   NON    | convertir un tableau php en entité doctrine avec ses relations                |
| [Darkilliant\ImportBundle\Step\DoctrinePersisterStep](./step/doctrine_persister.md)             |   NON    | persister une entité doctrine en bdd                                          |
| [Darkilliant\ProcessBundle\Step\DebugStep](./step/debug.md)                                     |   NON    | affiches les données dans le pipe                                             |
| [Darkilliant\ProcessBundle\Step\LaunchProcessStep](./step/launch_process.md)                    |   NON    | lancer un traitement                                                          |
| [Darkilliant\ProcessBundle\Step\PredefinedDataStep](./step/predefined_data.md)                  |   NON    | prédéfinir des données dans le pipe                                           |
| [Darkilliant\ProcessBundle\Step\ArrayBatchIterableStep](./step/array_batch_iterable_step.md)    |   NON    | attend d'avoir x élement dans le pipe avant de balancer à l'étape suivante    |
| [Darkilliant\ProcessBundle\Step\LaunchIsolateProcessStep](./step/launch_isolate_process_step.md)|   NON    | lance de manière simultané un même traitement sur plusieurs itération         |
| [Darkilliant\ProcessBundle\Step\UniqueFilterStep](./step/unique_filter_step.md)                 |   NON    | supprime les doublons                                                         |
| [Darkilliant\ProcessBundle\Step\ValidateObjectStep](./step/validate_object_step.md)             |   NON    | Valide un objet avec le validateur de symfony                                 |
| [Darkilliant\ProcessBundle\Step\FilterStep](./step/filter_step.md)                              |   NON    | Filter les données dans le pipe                                               |
| [Darkilliant\ImportBundle\Step\TransformStep](./step/transform_step.md)                         |   NON    | Transforme et valide les donnés dans le pipe                                  |
| [Darkilliant\ImportBundle\Step\MappingStep](./step/mapping_step.md)                             |   NON    | Permet de changer la structure d'un tableau d'un format vers un autre         |


Si vous désirez ajouter une miro tache,<br>
il vous suffit de déclarer un service en public dont sa classe hérite de `Darkilliant\ProcessBundle\Step\AbstractConfigurableStep`.<br>
Le nom du service doit être le FQCN de la classe et être utiliser ainsi dans la configuration d'un traitement.

Il est possible de désactiver une table avec l'option `enabled` au même niveau que la clé `service`
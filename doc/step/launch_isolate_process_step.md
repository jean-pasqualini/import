#### Darkilliant\ProcessBundle\Step\LaunchIsolateProcessStep

##### Rôle 

lance de manière simultané un même traitement sur plusieurs itération

##### Options

| Nom              | Description                                                             |
|------------------|-------------------------------------------------------------------------|
| process_name     | nom du traitement à lancer                                              |
| max_concurency   | nombre maximum d'itération à lancer de manière simultané                |
| context          | contexte à passer au state du traitement                                |
| data             | data à passer dans le pipe du traitement                                |
| timeout          | délais au dela duquel le traitement est automatiquement killer          |
| bin_console_path | chemin absolue vers la console de symfony                               |

##### Example

```yaml
service: 'Darkilliant\ProcessBundle\Step\LaunchIsolateProcessStep'
options:
    process_name: demo_isolate_process_sub
    max_concurency: 10
    bin_console_path: '%kernel.root_dir%/console.php'
    data: '@[data]'
```
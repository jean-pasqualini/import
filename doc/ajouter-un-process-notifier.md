#### Ecouter tout le cycle de vie d'un traitement

Il suffit de déclarer un process notifier,
C'est une classe qui implément `Darkilliant\ProcessBundle\ProcessNotifier\ProcessNotifierInterface`.

Example,
```yaml
    Darkilliant\ProcessBundle\ProcessNotifier\BreakerProcessNotifier:
        tags: [{ name: darkilliant_process_notifier }]
```

Voici les différents évenements du cycle de vie,

| Method                  | Description                                                                                                      |
|-------------------------|------------------------------------------------------------------------------------------------------------------|
| onStartProcess          | Juste avant l'éxécution d'une micro-tâche                                                                        |
| onStartIterableProcess  | Juste après qu'une micro-tache itérative se soit exécuter                                                        |
| onUpdateIterableProcess | A chaque itération d'une micro-tache itérative                                                                   |
| onEndProcess            | Juste après qu'une micro-tache se soit exécuter (si c'est une tache itérative c'est après qu'il ai itéré dessus) |
| onExecutedProcess       | Juste après qu'une micro-tache se soit exécuter (si c'est une tache itérative c'est avant qu'il ai itéré dessus) |
| onSuccessLoop           | Quand une itération réussie                                                                                      |
| onFailedLoop            | Quand une itération fail (exception disponible dans la clé last_error du context)                                |
| onStartRunner           | Quand le runner commande un traitement                                                                           |
| onEndRunner             | Quand le runner à finit un traitement                                                                            |

### Schéma

A venir
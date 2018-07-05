#### Darkilliant\ProcessBundle\Step\FileFinderIteratorStep

##### Rôle 

Part à la recherche de fichiers qui matchent avec les critères définit dans les options.

##### Options

| Nom                                   | Description                                                                                                              |
|---------------------------------------|--------------------------------------------------------------------------------------------------------------------------|
| in                                    | le/les dossiers dans lequels il faut chercher                                                                            |
| recursive                             | faut t-il chercher également dans les sous-dossier ? (défaut: true)                                                      |
| depth                                 | à quel profondeur faut-il se limiter ? (ex: >1 ou <3)                                                                    |
| name                                  | le pattern de nom de fichier à prendre dans ses filets                                                                   |
| date                                  | la date de dernière modification doit être (strtotime) (ex: > now - 2 hours)                                             |
| track_loop_state                      | faut t-il créer un dossier wait/success/failed et placer les fichier en fonction du return success ou non de l'itération |
| track_loop_state_remove_on_success    | faut-il supprimer le fichier, quand il est traité avec succès par l'itération ?                                          |

Pour infos, une micro-tâche itérative va boucler sur les étapes qui se trouve en dessous. et chaque tour de boucle est une itération.

##### Examples

```yaml
service: Darkilliant\ProcessBundle\Step\FileFinderIteratorStep
options:
    in: '%kernel.project_dir%/build/coverage/phpunit'
    name: '*.html'
    recursive: false
    track_loop_state: true
    track_loop_state_remove_on_success: false
```
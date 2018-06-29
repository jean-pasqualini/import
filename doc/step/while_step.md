#### Darkilliant\ProcessBundle\Step\WhileStep

##### Rôle 

boucle les des steps jusqu'a atteindre un nombre d'itération ou de temps d'éxécution définit par sa config.<br>
En règle général, on peut coupler cela avec un supervisor pour le relancer quand il s'arête afin d'avoir une bonne gestion de la mémoire.

##### Options

| Nom                  | Description                                         |
|----------------------|-----------------------------------------------------|
| max_iteration        | limite du nombre d'itération avant de tout stopper  |
| max_time             | limite du nombre de temps avant de tout stopper     |
| sleep_between        | temp à attendre entre les itération (défaut: 1)     |

##### Examples

```yaml
service: 'Darkilliant\ProcessBundle\Step\WhileStep'
options:
    max_iteration: 10
    max_time: 60 # time in seconds
    sleep_between: 1 # time in seconds
    progress_bar: true
```
#### Ajouter un breaker

##### Rôle 

compte le nombre d'itération et le temps d'exécution d'une step itérable et la stop quand elle atteind un nombre d'itération et/ou de temps d'éxécution définit par sa config.<br>
En règle général, on peut coupler cela avec un supervisor pour le relancer quand il s'arête afin d'avoir une bonne gestion de la mémoire.

Ces options son disponible sur toute step itérable.

##### Options

| Nom                          | Description                                         |
|------------------------------|-----------------------------------------------------|
| breaker                      | active le breaker (par défaut : false)              |
| breaker_max_iteration        | limite du nombre d'itération avant de tout stopper  |
| breaker_max_time             | limite du nombre de temps avant de tout stopper     |
| breaker_sleep_between        | temp à attendre entre les itération (défaut: 1)     |

##### Examples

```yaml
service: 'Darkilliant\ProcessBundle\Step\WhileStep'
options:
    breaker: true
    breaker_max_iteration: 10
    breaker_max_time: 60 # time in seconds
    breaker_sleep_between: 1 # time in seconds
```
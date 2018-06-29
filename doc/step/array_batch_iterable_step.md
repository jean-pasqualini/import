#### Darkilliant\ProcessBundle\Step\ArrayBatchIterableStep

##### Rôle 

attend d'avoir x élement dans le pipe avant de balancer à l'étape suivante

##### Options

| Nom             | Description                                                             |
|-----------------|-------------------------------------------------------------------------|
| batch_count     | nombre d'itération à empiler en mémoire avant de passer au step suivant |

##### Example

```yaml
service: 'Darkilliant\ProcessBundle\Step\ArrayBatchIterableStep'
options:
    batch_count: 15
```
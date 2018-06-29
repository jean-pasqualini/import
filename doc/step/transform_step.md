#### Darkilliant\ImportBundle\Step\TransformStep

##### Rôle 

Transforme et valide les donnés dans le pipe

##### Options

| Nom                  | Description                             |
|----------------------|-----------------------------------------|
| transforms           | transformations à appliquer             |
| transforms[].type    | nom du transformer à utiliser           |
| transforms[].source  | valeur à envoyé à dans la destination   |
| transforms[].target  | à quel endroit la valeur sera setter    |
| transforms[].options | options à passer au transfomer          |

##### Examples

```yaml
service: 'Darkilliant\ImportBundle\Step\TransformStep'
options:
    transforms:
        - { type: string, 'source': '@[data][title]', 'target': '[title]' }
```

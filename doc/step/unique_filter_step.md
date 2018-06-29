#### Darkilliant\ProcessBundle\Step\UniqueFilterStep

##### Rôle 

supprime les doublons

##### Options

| Nom    | Description                                                     |
|--------|-----------------------------------------------------------------|
| data   | si différent des data dans le pipe                              |
| fields | les clé du tableau sur lequels les valeurs doivent êtres unique | 

##### Example

```yaml
service: 'Darkilliant\ProcessBundle\Step\UniqueFilterStep'
options:
    fields: ['name']
```
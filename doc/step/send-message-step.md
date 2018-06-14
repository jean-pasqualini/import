#### Darkilliant\MqProcessBundle\Step\SendMessageStep

##### Rôle 

Envoi un message sur un broker qui sera router dans une queue (file d'attente).<br>
! Attention, pensez à configurer un broker. (voir [comment configurer un broker ?](./../configurer-broker.md))

##### Options

| Nom                      | Description                                                                               |
|--------------------------|-------------------------------------------------------------------------------------------|
| exchange                 | Routeur de messsage                                                                       |
| queue                    | File d'attente                                                                            |
| persistant               | Faut-il persister les messages sur le disque afin de survivre à un rédémarage du broker ? |
| client                   | type de client supporté (seulement amqp_lib pour le moment)                               |
| batch_count              | nombre de messages à mettre dans chaque lot envoyé                                        |
| progress_bar             | faut-il afficher une progressbar pour voir les messages être consomé ?                    |

##### Examples

```yaml
service: Darkilliant\MqProcessBundle\Step\SendMessageStep
options:
    exchange: 'amq.direct'
    queue: 'import_product'
    batch_count: 20000
```

#### Darkilliant\MqProcessBundle\Step\ConsumeMessageStep

##### Rôle 

Itère sur une file d'attente de message dans un broker<br>
! Attention, pensez à configurer un broker. (voir [comment configurer un broker ?](./../configurer-broker.md))

##### Options

| Nom             | Description                                                    |
|-----------------|----------------------------------------------------------------|
| exchange        | Routeur de messsages                                           |
| queue           | File d'attente                                                 |
| ack_required    | Envoie un ok/ko au broker ?                                    |
| client          | type de client supporté (seulement amqp_lib pour le moment)    |
| batch_count     | nombre de message à récupérer dans chaque lots                 |
| persistant      | Faut-il persister les messages sur le disque ?                 |
| requeue_on_fail | Faut-il remettre en file d'attente les messages qui ont fail ? |

!Astuce : <br>
Il est possible de monter de 3000 messages par seconde à 20000 messages par seconde en désactivant le renvoie ok/ko.<br>
Cependant celà diminue la fiabilité car les messages disparaissent définitevement dès qu'il sont consomé.

##### Examples

```yaml
service: 'Darkilliant\MqProcessBundle\Step\ConsumeMessageStep'
options:
    exchange: 'amq.direct'
    queue: 'import_product'
    ack_required: true
```

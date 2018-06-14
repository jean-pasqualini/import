#### Configurer un broker de message

Afin de configurer le broker, il suffit d'adapter la configuration suivante<br><br>

Pour le moment seul le broker de type `amqp_lib` (rabbitmq compatible est supporté).<br><br>

! Attention penser à activer le bundle `Darkilliant\MqProcessBundle\DarkilliantMqProcessBundle`.

```yaml
darkilliant_mq_process:
    client:
        host: 127.0.0.1
        port: 5672
        user: root
        password: root
        vhost: rabbitmq
```

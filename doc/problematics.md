1. Comment gérer la rapidité ?

1.1 Avec des process linux

```
- Step CsvSplitter
- Boucle files
-     Boucle steps
-          Debug
-          Process
-     Finalise steps
-          Debug
-          Process
```

1.2 Avec rabbit mq

```
- Step FileBrower
- Boucle files
-     Boucle steps
-          Debug
-          Process
-          Step CsvIterator
-          Boucle steps
-                send to rabbit

- Step waiting rabbit
- Boucle items
     - Denormalize
     - Persist
```

2. Comment gérer la validation ?

2.1 Soit avec l'objet

On fait appel au validateur symfony sur l'objet

2.2 Soit avant

On fait appel au transformer et peut être qu'on permet la transformation à la volée
avec le format '@[data][titre]|string'

Soit avec une première étape quand les données sont à plat
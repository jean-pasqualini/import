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

Analyse suite à benchmark

```
BDD Sqlite (insert)

150 000 produit
Batch de 20000
10 process simulaté
Timeout process à 20 minutes

Début 20:11:12
Fin 20:29:00

18 minutes pour 150.000 Produits équivalent à un ratio de 138 produits par seconde.
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
avec le format '@[data][titre]|string' ou '@[data][date]|datetime("d/m/Y")'

Soit avec une première étape quand les données sont à plat

3. Désérialization de relation sans id

Par example un Product peut avoir une relation ProductExtraData.

Pour un produit remplissant les conditions suivantes,
- ProductExtraData déjà en base et lié au produit
- relation extra data présent dans les donnée à denormalizer

Il sera recréer avec les données dénormalizer même si ces données sont partielle.

L'objet déjà lié ne sera pas conservé. C'est un cas à connaitre pour adapter sa logique d'import.

Nous allons chercher pour la prochaine version une solution à cette problématique si elle est ressenti par nos utilisateurs

3. ArrayBatchIterableStep

```php
<?php

$items = [1, 2, 3, 4, 5, 6, 7];
$batchSize = 3;

$batch = [];
foreach ($items as $key => $value) {
    $batch[] = $value;
    if (count($batch) >= 3) {
        // Do process
        $batch = [];
    }
}

// Do process
```
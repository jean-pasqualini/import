#### Utilise des options dynamique

Chaque step est configurable par ces options.<br>
Celles-ci se configure au niveau de la step.

```yaml
darkilliant_process:
    process:
        xxx:
            steps:
                -
                    service: xxx
                    options: []
```

il est possible d'utiliser des valeurs dans les options qui seront résolu dynamiquement avant chaque exécution de la step.<br>

Par examples on peut y injecter une valeur présent dans le contexte où les data qui sont dans le pipe.

Example,

```yaml
darkilliant_process:
    process:
        xxx:
            steps:
                -
                    service: xxx
                    options: 
                        name: '@[data][title]'
```

Ici, par la détection du préfixé '@', la valeur sera résolue dynamiquement avec le property accessor de symfony.<br>
Dans l'example on va chercher la valeur de la clé title dans les données actuellement dans le pipe.<br><br>

Ce format est décrit dans la documentation de symfony, voir https://symfony.com/doc/current/components/property_access.html#reading-from-arrays.<br>

L'usage du property accessor est puissant car il peut travailler autan avec des objets que des tableaux mais est fort consomateur en temps processeur.<br>
Pour les cas nominaux de manipulation de tableau une implémentation basique mais bien plus performante à été prévu.<br>

Voici un cas d'usage,
```yaml
darkilliant_process:
    process:
        xxx:
            steps:
                -
                    service: xxx
                    options: 
                        name: '@!data->title'
```

Ce format est différent et limité car il ne gère pas aujourdh'ui la profondeur, ni les objets.<br>
Cependant pour la plupart des cas, ceci est bien suffisant.<br>

Bien sur, il est tout à fait possible de mixer les approches,<br>

```yaml
darkilliant_process:
    process:
        xxx:
            steps:
                -
                    service: xxx
                    options: 
                        name: '@!data->title'
                        priceTtc: '@[data][prix]'
```

C'est moins facile à lire quand c'est mixé mais le besoin de flexibilité et de perfomance le justifie plainement.<br>


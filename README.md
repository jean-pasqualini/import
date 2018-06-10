# ImportBundle

### Qualité

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jean-pasqualini/import/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jean-pasqualini/import/?branch=master)

[![Code Coverage](https://scrutinizer-ci.com/g/jean-pasqualini/import/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/jean-pasqualini/import/?branch=master)

[![Build Status](https://travis-ci.org/jean-pasqualini/import.svg?branch=master)](https://travis-ci.org/jean-pasqualini/import)

### Contribution

##### Lancer les tests

```
make test-unit
```

##### Analyser le coding style

```
make test-cs
```

##### Comment contribuer ?

[Comment contribuer ?](./doc/contribution.md)

##### Prérequis
- Symfony >= 3.4.0, < 4.0.0
- PHP >= 7.0.0

#### Installation

##### Etape 1: Télécharger le bundle

Ouvirr le terminal, entrer dans le répertoire du projet et éxécuter
ces commandes pour télécharger la dernière version stable de ce bundle:

```console
$ composer require darkilliant/import
```


Cette comamnde requis d'avoir le composer d'installer globalement, ceci
est expliquer dans le [chapitre d'installation](https://getcomposer.org/doc/00-intro.md)
de la documentation du composer.

##### Etape 2: Activer le Bundle

Puis, activer le bundle en l'ajoutant dans la liste du
fichier `app/AppKernel.php`

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Darkilliant\ImportBundle\DarkilliantImportBundle(),
        );

        // ...
    }

    // ...
}
```

##### Releasing

- Version semver (majeure.mineur.bugfix)
- De nouvelles fonctionalitées tous les 3 mois (0.1, 0.2, 0.3, 0.4, 1.1, 1.2, 1.3, ...)
- Une version majeure tous les ans (même quand il n'y à pas changement majeur mais permettant de supprimer le BC)
- Nous assurons la compatibilité descendante
- Pas plus de deux version en cours maintenu pour les fix (version stable et version en cours de dev)

##### BC Break

Considèrer comme du cassage de compatiblité,
- Changement de signature de méthode public, sauf si mis en @internal au niveau de la méthode ou de la classe
- Suppression de méthode public, sauf si mis en @internal au niveau de la méthode ou de la classe
- Suppression de propriété public
- Suppression d'option dans un transformer ou une step
- Ajout d'option sans default dans un transformer ou une step
- Changement du comportement d'un transformer ou d'une step avec une même configuration


Qu'est-ce qu'on protège globalement par le contrat de retro-compatiblité ?
- Le comportements des step, transformer ne doit pas changé avec une même configuration
- Le fonctionnement du StepRunner avec une même configuration
- Les méthodes publlques du ProcessState
- Les méthodes publiques des steps et du step runner

#### Roadmap

- [0.3](https://github.com/jean-pasqualini/import/issues/4)
- [0.4](https://github.com/jean-pasqualini/import/issues/10)

| Version | Date de publication | Date de fin de maintenance | BC ? |
|---------|---------------------|----------------------------|------|
| 0.2     | Juin 2018           | Juillet 2018               | NON  |
| 0.3     | Juillet 2018        | Octobre 2018               | NON  |
| 0.4     | Octobre 2018        | Janvier 2019               | NON  |
| 1.1     | Janvier 2019        | Avril 2019                 | OUI  |
| 1.2     | Avril 2019          | Juillet 2019               | NON  |
| 1.3     | Juillet 2019        | Octobre 2019               | NON  |
| 1.4     | Octobre 2019        | Janvier 2020               | NON  |
| 2.1     | Janvier 2020        | Avril 2020                 | OUI  |


### Usage

Règles,

- Vous utilisez donc vous contribuer à la documentation et aux bugfix. raller c'est bien mais agir c'est mieu.
- Toujours travailler sur une version stable, une version en cours de développement n'est pas conseillé.

Cookbook,
- [Lisez ceci avant tout](./doc/lisez-ceci-avant-tout.md)
- [Example, pas à pas de création d'un import.](./doc/pas_a_pas.md)
- [Liste des transformers disponible](./doc/list-transfomer.md)
- [Liste des micro-tâches disponible](./doc/list-step.md)
- [Liste des filtres disponible](./doc/list-filter.md)
- [Comment lancer un traitement ?](./doc/lancer-un-traitement.md)
- [Comment lister les traitements disponible ?](./doc/lister-traitement.md)

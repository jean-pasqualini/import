# ImportBundle

### Qualité

Badge de scrutinizer et travis-ci

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

##### Releasing

- Version semver (majeure.mineur.bugfix)
- De nouvelles fonctionalité tous les 3 mois (0.1, 0.2, 0.3, 0.4, 1.1, 1.2, 1.3, ...)
- Une version majeure tous les ans (même si pas de changement majeur mais permettant de supprimer le BC)
- Nous assurons la compatibilité descendante
- Pas plus de deux version en cours maintenu pour les fix (version stable et version en cours de dev)

##### BC Break

Considèrer comme du cassage de compatiblité,
- Changement de signature de méthode public, sauf si mis en @internal au niveau de la méthode ou de la classe
- Suppression de méthode public, sauf si mis en @internal au niveau de la méthode ou de la classe
- Suppression de propriété public

##### Contributing

- Vous utilisez donc vous contribuer à la documentation et aux bugfix. raller c'est bien mais agir c'est mieu.
- Toujours travailler sur une version stable, une version en cours de développement n'est pas conseillé.
- Vérifier que le bug est bien présent
- Si besoin urgent ne pas hésiter à faire une step sur son propre projet puis à préparer une contribution par la suite
- Préférer de petite PR qui passeront rapidement
- Respecter le template de Merge request du projet 

#### Roadmap

##### 0.1
- Import
- N'afficher la progressbar que sur les step itérable où l'ont active l'option progress_bar à true
- Casser la dépendance force à la progressbar et la plugger sur un event qui notifie l'avancement d'un traitement

##### 0.2
- Permettre de lancer des process de manière simulatané avec l'option pararrel: 5
- (Pas sur) Permettre de passer par rabbitmq pour gérer de très gros import avec une grande scalabilité 
- Rester à l'écoute des utilisateurs de l'outils afin de trouver le chemin qui réponde aux besoins tout en gardant un code évolutif et solid

### Usage

Ce bundle se base sur un système de micro-tache qui mit bout à bout permette de créer un traitement complexe tel qu'un import.

Chaque tache doit être assez unitaire pour être utilisable pour faire tout et n'importe quoi,
- Sortir un [recipient] et y mettre un [ingredient]
- Faire fondre le contenu du [recipient]
- Faire cuirre le contenu du [recipient]
- Faire bouillir le contenu du [recipient]
- Plonger [ingredient] dans la [recipient]
- Egouter [ingredient]
- Transposer [ingredient] dans [recipient]

C'est ensuite le contexte et la composition de ces taches qui vont permetre de faire une tache complexe.

Dans notre cas, on pourrai faire des pattes,

- Sortir une casserolle et y mettre de l'eau froide
- Faire bouillir le contenu de la casserolle
- Plonger des pattes dans la casserolle
- Faire Egouter le contenu
- Transposer le contenu dans un plat en verre

Comme on pourrais également faire un steak haché,

- Sortir une poelle et y mettre un steak hacké
- Faire cuire le contenu de la poelle
- Transposer le contenu dans une asiette

L'important est de bien maitriser cette découpe pour augmenter la possibilité d'utiliser ces micro-taches.

Un ensemble de tache composer s'apelle un traitement (ou process en anglais)

Peut être avez vous remarquer que pour un import, 
- l'ingrédient sera une donnée qui va être transformer en objet au final
- le recipient sera ce tableau pour finir par être la bdd

Ce bundle dispose de micro-tache spécialiser pour de l'import de donnée dans une bdd.

Nous allons décrire les miro-taches disponible,

- découper un fichier excel en autan de fichier csv qu'il ne dispose d'onglet
- extraire chaque ligne d'un fichier csv sous forme d'un tableau 
- parcourir un tableau php
- transformer un tableau php et le valider
- convertir un tableau php en entité doctrine avec ses relations
- persister une entité doctrine en bdd
- affiches les données dans le pipe
- lancer un traitement
- prédéfinir des données dans le pipe

[Example, pas à pas de création d'un import.](./doc/pas_a_pas.md)
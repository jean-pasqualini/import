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
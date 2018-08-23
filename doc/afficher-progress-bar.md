#### Afficher une barre de progression

Une barre de progression monitoré est disponible.
Elle affiche des informations tel quel,
- Le nombre d'élement à traiter
- Le nombre d'élement traiter
- La mémoire utilisé (il y a 1 seconde, 10 secondes, 20 secondes)
- Le nombre d'élement par seconde traité (il y a 1 seconde, 10 secondes, 20 secondes)

Pour cela, il suffit sur une step dite itérable d'ajouter l'option ```progress_bar: true```.

Example,

```yaml
service: 'Darkilliant\ProcessBundle\Step\IterateArrayStep'
options:
    progress_bar: true
    progress_bar_min_item: 50   # min items par seconds acceptable=
    progress_bar_max_memory: 30 # max memory acceptable 
```

Pour savoir si une step est itérable, consulter la liste des steps disponible.
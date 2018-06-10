| Class                                                                                           | Descriptif                                                                             |
|-------------------------------------------------------------------------------------------------|----------------------------------------------------------------------------------------|
| [Darkilliant\ProcessBundle\Filter\RegexFilter](./filter/regex_filter.md)                        | Constate ou non que le pattern attendu correspond à la valeur                          |
| [Darkilliant\ProcessBundle\Filter\StrposFilter](./filter/strpos_filter.md)                      | Constate ou non la présence de la sous chaine attendu                                  |
| [Darkilliant\ProcessBundle\Filter\ValidatorFilter](./filter/symfony_validate_filter.md)          | Applique un validateur symfony et vérifie si la valeur répond au exigeance de celui-ci |
| [Darkilliant\ProcessBundle\Filter\ValueFilter](./filter/value_filter.md)                        | compare la valeur avec celle attendu de manière sticte.                                |

Si vous désirez ajouter un filter,<br>
 - il vous suffit de déclarer un service en public dont sa classe hérite de `Darkilliant\ProcessBundle\Filter\AbstractFilter`.<br>
 - déclarer en tant que service et la taggué (l'alias est le nom du filter)
 
```yaml
    Darkilliant\ProcessBundle\Filter\ValueFilter:
        public: true
        tags:
          - { name: darkilliant_process_filter, alias: value }
```
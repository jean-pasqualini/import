#### Darkilliant\ImportBundle\Step\MappingTransformerStep

:red_circle: Cette step est déprécié, utiliser plutot `MappingStep` et `TransformStep`

##### Rôle 

transformer un tableau php et le valider    

##### Options

| Nom     | Description                                                                                       |
|------   |---------------------------------------------------------------------------------------------------|
| mapping | configuration du mapping sous la forme de key: 'value' ou key: { value: '', transformers: [...] } |

##### Examples

```yaml
service: 'Darkilliant\ImportBundle\Step\MappingTransformerStep'
options:
    mapping:
        ean: "@[data][ean]"
        tags:
            value:
                -
                    name: "@[data][label_tag_1]"
                -
                    name: "@[data][label_tag_2]"
                -
                    name: "@[data][label_tag_3]"
                -
                    name: "@[data][label_tag_4]"
                -
                    name: "@[data][label_tag_5]"
                -
                    name: "@[data][label_tag_6]"
            transformers: ["remove_empty_in_array"]
```

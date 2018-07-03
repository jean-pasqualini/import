#### Darkilliant\ProcessBundle\Step\FilesystemEventStreamStep 

##### Rôle 

A chaque fois d'un événement (écriture, lecture, suppression...) se produit sur le filesystem, celui si est passé dans le pipe<br>

Cette step boucle sur les steps suivante en itérant sur les événements.

##### Options

| Nom             | Description                                                                           |
|-----------------|---------------------------------------------------------------------------------------|
| folder          | Le dossier à surveiller                                                               |
| event_name      | le type d'évenement à écouter (modification, lecture, suppression, ...) ?             |
| recursive       | faut-il surveiller également les dossiers à l'intérieur (par défaut: false)           |

#### Liste événements supporté

```
access          file or directory contents were read
modify          file or directory contents were written
attrib          file or directory attributes changed
close_write     file or directory closed, after being opened in
                writable mode
close_nowrite   file or directory closed, after being opened in
                read-only mode
close           file or directory closed, regardless of read/write mode
open            file or directory opened
moved_to        file or directory moved to watched directory
moved_from      file or directory moved from watched directory
move            file or directory moved to or from watched directory
create          file or directory created within watched directory
delete          file or directory deleted within watched directory
delete_self     file or directory was deleted
unmount         file system containing file or directory unmounted
```

##### Examples

```yaml
steps:
    -
        service: Darkilliant\ProcessBundle\Step\FilesystemEventStreamStep
        options:
            folder: /tmp/darkilliant_process_inotify/
            event_name: close_write
    -
        service: Darkilliant\ProcessBundle\Step\DebugStep
        options: []
```
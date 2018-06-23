### Lancer un traitement

```
Usage:
  process:run [options] [--] [<process>]...

Arguments:
  process                 process

Options:
  -c, --context=CONTEXT   context (multiple values allowed)
      --input-from-stdin  enable data pass in stdin with json body
      --force-color       force use color when not autodetect support
      --dry-run           dry run
      --profiling         enable profiling (generate stat.json)
  -h, --help              Display this help message
  -q, --quiet             Do not output any message
  -V, --version           Display this application version
      --ansi              Force ANSI output
      --no-ansi           Disable ANSI output
  -n, --no-interaction    Do not ask any interactive question
  -e, --env=ENV           The Environment name. [default: false]
      --no-debug          Switches off debug mode.
  -v|vv|vvv, --verbose    Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```

##### Example

```bash
$ bin/console process:run [nom_process/traitement]
```
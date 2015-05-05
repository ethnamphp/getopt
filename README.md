# Ethnam Getopt Component

re-invention of PEAR's Console_Getopt

## INSTALL

In you composer.json

```json
{
  "require" : {
    "ethnam/getopt" : "1.*"
  }
}
```

## USAGE

```php
use Ethnam\Getopt\Getopt;
$opt = new Getopt();
$arg_list = $opt->readGlobalArgv();
array_shift($arg_list);  // remove commant itself
$opt->getopt($my_arg_list, "v", array("version"));
```

## SEE ALSO

http://pear.php.net/manual/en/package.console.console-getopt.php

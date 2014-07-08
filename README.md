Puzzle-configuration
====================

Hide configuration implementation behind common interface. 

Some advantages :
* Application does not depend upon configuration implementation details 
* Application does not have to manage filesystem issues (for filesystem based implementations)
* Application can be easily tested, even for configuration edge cases (missing or wrong configuration values)
* Define configuration as a service in your dependency injection container

QA
--

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/635b04b7-6238-4200-8526-72766767fd22/big.png)](https://insight.sensiolabs.com/projects/635b04b7-6238-4200-8526-72766767fd22)

Service | Result
--- | ---
**Jenkins** | [![Build Status](http://jenkins.deboo.fr/job/Puzzle-configuration/badge/icon)](http://jenkins.deboo.fr/job/Puzzle-configuration/)
**Travis CI** (PHP 5.4 + 5.5) | [![Build Status](https://travis-ci.org/Niktux/puzzle-configuration.png?branch=master)](https://travis-ci.org/Niktux/puzzle-configuration)
**Scrutinizer** | [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/Niktux/puzzle-configuration/badges/quality-score.png?s=595d09c72316b5e706c3f78fb00807bc6b1515f1)](https://scrutinizer-ci.com/g/Niktux/puzzle-configuration/)
**Packagist** | [![Latest Stable Version](https://poser.pugx.org/puzzle/configuration/v/stable.png)](https://packagist.org/packages/puzzle/configuration) [![Total Downloads](https://poser.pugx.org/puzzle/configuration/downloads.svg)](https://packagist.org/packages/puzzle/configuration)

Installation
------------
Use composer :
```json
{
    "require": {
		    "puzzle/configuration" : "~1.4"
    }
}
```

Documentation
-------------

### Configuration as service ###


```php
<?php

class Example
{
    public function __construct(Puzzle\Configuration $config)
    {
        $threshold = $config->read('app/detection/threshold');
    }
}
```

The way the configuration value is read depends on the chosen implementation.

Up to now, 2 implementations are provided :
* Memory (for unit testing purpose)
* Yaml (based on Symfony/Yaml). 

For YAML one, ```'app/detection/threshold'``` means ```detection[thresold]``` in app.yml file. 
When you instanciate YamlConfiguration object, you need to provide where yaml files can be found : 

```php
<?php

$fileSystem = new Gaufrette\Filesystem(
    new Local('path/to/yaml/files/root/dir')
);
$config = new Puzzle\Configuration\Yaml($fileSystem);

$example = new Example($config);

```

```yaml
# app.yml 

detection:
  threshold: 3
```

### Unit testing ###

```php
<?php

$config = new Puzzle\Configuration\Memory(array(
    'app/detection/threshold' => 2
);

$example = new ExampleTest($config);

```

### Default values ###

```php
<?php

$configuration->read('a/b/c', 'default value if a/b/c does not exist');
```

But if ```a/b/c``` is required :

```php
<?php

// will throw an exception if a/b/c does not exist
$configuration->readRequired('a/b/c');
```




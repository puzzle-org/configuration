Puzzle-configuration ![PHP >= 5.6](https://img.shields.io/badge/php-%3E%3D%205.6-blue.svg)
====================

Hide configuration implementation behind common interface. 

Some advantages :
* Application does not depend upon configuration implementation details 
* Application does not have to manage filesystem issues (for filesystem based implementations)
* Application can be easily tested, even for configuration edge cases (missing or wrong configuration values)
* Define configuration as a service in your dependency injection container

QA
--


Service | Result
--- | ---
**CI** | [![CI](https://github.com/puzzle-org/configuration/actions/workflows/ci.yml/badge.svg)](https://github.com/puzzle-org/configuration/actions/workflows/ci.yml)
**Packagist** | [![Latest Stable Version](https://poser.pugx.org/puzzle/configuration/v/stable.png)](https://packagist.org/packages/puzzle/configuration) [![Total Downloads](https://poser.pugx.org/puzzle/configuration/downloads.svg)](https://packagist.org/packages/puzzle/configuration)

Installation
------------
Use composer :
```json
{
    "require": {
            "puzzle/configuration" : "~8.3"
    }
}
```

_PHP 8.0 & 8.1 users please use puzzle/configuration 5.x_

_PHP 7.x users please use puzzle/configuration 4.x_

_PHP 5.6 users please use puzzle/configuration 3.x_

Documentation
-------------

### Configuration as a service ###


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

### Fallback strategy ###

```php
<?php

// Since 1.5.0
// returns value associated to first existing key
// will throw an exception if none exist
$configuration->readFirstExisting('a/b/c', 'd/e/f', 'x/y/z');
```

### Override configuration ###

If you need some configuration to (partially or not) override another one :
```php
<?php

// Since 1.6.0
$defaultFileSystem = new Gaufrette\Filesystem(
    new Local('path/to/yaml/files/root/dir')
);
$defaultConfig = new Puzzle\Configuration\Yaml($defaultFileSystem);

$fileSystem = new Gaufrette\Filesystem(
    new Local('path/to/another/config/files')
);
$localConfig = new Puzzle\Configuration\Yaml($fileSystem);

$config = new Puzzle\Configuration\Stacked();
$config->overrideBy($defaultConfig)
       ->overrideBy($localConfig);

// values will be read in localConfig first. They will be read in default config only if they don't exist in local one.
```

Another example : 
```php
<?php

$fileSystem = new Gaufrette\Filesystem(
    new Local('path/to/yaml/files/root/dir')
);
$defaultConfig = new Puzzle\Configuration\Yaml($fileSystem);

$overrideConfig = new Puzzle\Configuration\Memory(array(
    'app/detection/threshold' => 2
);

$config = new Puzzle\Configuration\Stacked();
$config->overrideBy($defaultConfig)
       ->overrideBy($overrideConfig);
```
You can add as many as configuration instances you want in the stack. The last inserted is the most prioritary.

If you want to add the least prioritary, use the ```addBase()``` method :
```php
<?php

// Since 2.0.0
$config = new Puzzle\Configuration\Stacked();
$config->overrideBy($overrideConfig)
       ->addBase($defaultConfig);
```

### Prefixed configuration ###

You can use automatic prefix decorator ```PrefixedConfiguration```. It can be useful for "namespace like" configurations such as loggers or multiple databases ones.

```yaml
# logger.yml 

app:
  filename: app.log
  verbosity: INFO
users:
  filename: users.log
  verbosity: WARNING
```

```php
<?php

// Since 1.7.0
$fileSystem = new Gaufrette\Filesystem(
    new Local('path/to/yaml/files/root/dir')
);
$config = new Puzzle\Configuration\Yaml($fileSystem);
$config = new Puzzle\PrefixedConfiguration($config, "logger/$loggerName");

$filename = $config->readRequired('filename');
$verbosity = $config->readRequired('verbosity');
```

Changelog
---------

**5.x --> 8.3**

- Drop php 8.0 & 8.1 support. Minimal version is 8.3
- Version names are now match the PHP version as the library does not need to functionaly evolve anymore
- Get rid of Gaufrette. Own interface is provided, please implements it. A Gaufrette adapter is provided for smooth migration

**4.x --> 5.x**

 - Drop php 7 support. Minimal version is 8.0
 
**3.x --> 4.x**

 - Drop php 5.6 & 7.0 support. Minimal version is 7.1.0
 
 **2.x --> 3.x**

 - Drop php 5.5 support. Minimal version is 5.6.0
 
**1.x -> 2.x**

 - Drop php 5.4 support. Minimal version is 5.5.0

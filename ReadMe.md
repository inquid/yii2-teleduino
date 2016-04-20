# Yii2-teleduino

## Overview

Yii2-teleduino is the module for Yii2 framework, which implements convenient web based interface, as well as console command, for interaction with the [Teleduino API](https://www.teleduino.org/documentation/api/328-full) (v328) compliant web services.


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist suoi/yii2-teleduino "*"
```

or add

```
"suoi/yii2-teleduino": "*"
```

to the require section of your `composer.json` file.
```

* Declare new module ID `teleduino` in the modules property of the application. Use the following application configuration in the file `config/web.php`:
```php
return [
    ......
    'modules'=>[
       'teleduino'=>[
           'class' => 'madand\teleduino\Module',
           'apiKeys'=>[
               [
                   'key'=>'00000000000000000000000000000000',
                   'name'=>'My Key 1',
               ],
               [
                   'key'=>'11111111111111111111111111111111',
                   'name'=>'My Key 2',
               ],
           ],
       ],
       ......
    ],
    ......
];
```

* Open the following URL: `http://yourdomain.com/path/to/app/index.php?r=teleduino` in your browser.


## Configuration

In addition to the required module configuration, described at p.2 of the "Installation" section,
you can also configure API component properties by utilizing Yii's Dependency Injection Container.

In this example each option is assigned with its respective default value.

```php
<?php

Yii::$container->set(
    \madand\teleduino\components\Api::className(),
    [
        // Default API Endpoint (Request URL).
        // You can override this in the web interface "API Options" form.
        // This parameter is useful for console command, since if set, you can omit setting a corresponding option in the command line.
        'apiEndpoint' => 'https://us01.proxy.teleduino.org/api/1.0/328.php',

        // Default API key.
        // This parameter is useful for console command, since if set, you can omit setting a corresponding option in the command line.
        'apiKey' => null,

        // User agent string that will be supplied to the API server with each request.
        'userAgent' => 'Yii-teleduino/1.0',

        // Whether to check endpoint's certificate for validity, if the endpoint is a HTTPS server.
        // Set this to FALSE only if you are using self signed certificates!
        'sslVerifyPeer' => true,
    ]
);

$params = require(__DIR__ . '/params.php');

$config = [
....
```

Please also look at the example configuration files in `example-config` directory.

## Access control

If you want to apply some access restrictions for the module, you need to edit the file `Module.php`.
 Locate the following method in the file:

```php
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            // Place access checking code here, if needed.
            return true;
        } else {
            return false;
        }
    }
```

And replace the corresponding comment with the actual access checking code.


## Customization

You can easily customize the module's web interface appearance, by editing its styles file.
 Please look into the `assets` folder and you will find two files with styles:

1. teleduino.less - this is the styles written in the language called [LESS](http://lesscss.org/).
 Please, consider editing this file and then generating CSS file, since LESS is much more convenient.

2. teleduino.css - this is a CSS file generated out of the teleduino.less.
 This file is included into web page. You could edit it directly, if you don't want to use LESS.

In fact, those files are almost empty because the module relies on Bootstrap for styling.
But your still override some styles as needed.


## Console command

Yii2-teleduino module includes console command that provide full access to the API from command line and shell scripts.

In order to activate console command you need to modify your console application configuration file (usually located at `config/console.php`).
You need to declare module `teleduino` the same way, as described in p. 2 of the "Installation" section.
Then you need to add `teleduino` to the `bootstrap` property of your console application.

Also, you can configure API component's properties

Resulting configuration should resemble the following:

```php
<?php

Yii::$container->set(
    \madand\teleduino\components\Api::className(),
    [
        // Default endpoint URL. Will be used, if no --apiEndpoint was specified in the command line.
        'apiEndpoint' => 'https://us01.proxy.teleduino.org/api/1.0/328.php',
        // Default API Key. Will be used, if no --apiKey was specified in the command line.
        'apiKey' => '00000000000000000000000000000000',
    ]
);

$params = require(__DIR__ . '/params.php');

$config = [
    .....
    'bootstrap' => ['teleduino'],
    'modules'=>[
        'teleduino'=>[
            'class' => 'madand\teleduino\Module',
        ],
    ],
    ......
];
....
```

Please do note, if you will not declare `apiEndpoint` and/or `apiKey` in the configuration file, you will need to specify them explicitly in command line every time!

Now you should be able to get help for the `teleduino` command in the following way:
`./yii help teleduino` - running this way will show general help with the list of all available methods.

To get detailed info about a particular method, run the command like this:
`./yii help teleduino/api/defineSerial`

Here are few examples of invoking some methods:

1. `./yii teleduino/api/getVersion` - get firmware version in human readable format

2. `./yii teleduino/api/getEeprom --responseFormat=json --offset=0 --byte_count=200` - get first 200 bytes of EEPROM, return result as JSON.

3. `./yii teleduino/api/getAllInputs --apiEndpoint="http://example.com" --apiKey="alternate-key"` - get the input values of all the digital and analog pins, using the explicit API Edpoint and Key.


## Sponsor and Original Idea

SUOI Develop
Luis Armando González
* Email: luisarmando1234@gmail.com
* Twitter: gogl92


## Author

Andriy Kmit'
* Email: dev@madand.net
* Elance: [Andriy Kmit'](https://www.elance.com/s/andkmit/)

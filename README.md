# RithisImagePlaceholderBundle

Symfony2 Bundle which generate placeholder image like http://www.placehold.it

## Installation

Run this command in your project directory:

``` bash
$ composer.phar require rithis/image-placeholder-bundle:@dev
```

After that enable bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Rithis\ImagePlaceholderBundle\RithisImagePlaceholderBundle(),
    );
}
```

## Usage

### Routing

First of all include bundle routing:

``` yaml
# app/routing.yml

_rithis_image_placeholder:
    resource: "@RithisImagePlaceholderBundle/Resources/config/routing.yml"
    prefix: _placeholder
```

After that try to open placeholder url:

```
/_placeholder/{x},{y},{backgroundColor},{textColor}?text={text}
```

For example: `/_placeholder/200,100,00FF00,FF00FF?text=Hello%20World!`.

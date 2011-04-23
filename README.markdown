# kRatePlugin

kRatePlugin provides a Doctrine behavior, controllers, templates and assets to
set up a noting system quickly.

## Installing the plugin

* add it as a submodule : `git submodule add git@github.com:K-Phoen/kRatePlugin.git plugins/kRatePlugin`
* enable the plugin in your **ProjectConfiguration** class

_config/ProjectConfiguration.class.php_
```php
<?php

require_once dirname(__FILE__).'/../lib/vendor/symfony/lib/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

class ProjectConfiguration extends sfProjectConfiguration
{
  public function setup()
  {
    $this->enablePlugins('sfDoctrinePlugin');
    // ...
    $this->enablePlugins('kRatePlugin');
    // ...
  }
  // ...
}
```

## Doctrine behavior

To declare that a model is ratable, add the behavior in your
_config/doctrine/schema.yml_ file:

```yaml
Article:
  actAs:
    Ratable:

  columns:
    # ...
```

After having rebuilt your models (`./symfony doctrine:build --all --and-load`), a new table, notes, is created to store the article (and other models) notes.

## Display rating stars

Include the plugin assets in your _apps/front/config/view.yml_ file:

````yaml
stylesheets:
  - main.css
  - /kRatePlugin/css/jquery.rating.css
  - ...

javascripts:
  - jquery.min.js # not included in the plugin
  - /kRatePlugin/js/jquery.rating.js
  - ...
```

Include the rate/formRate component in your record template.
```php
<?php include_component('rate', 'formRate', array('object' => $article)) ?>
```

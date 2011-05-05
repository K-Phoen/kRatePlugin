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

* and activate the _rate_ module in your frontend app :

_apps/frontend/config/settings.yml_

```yaml
enabled_modules:        [..., rate, ...]
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

Include the plugin assets in your _apps/frontend/config/view.yml_ file:

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

## Configure the plugin

### Maximum note

The number of stars to display (the maximum note) can be configured in the _apps/frontend/app.yml_
file :

```yaml
all:
  # ...

  kRatePlugin:
    max:  5

  # ...
```

### kRatePlugin and sfGuardPlugin

A vote can be bound to a a sfGuardUser (enabled by default) :

_apps/frontend/app.yml_

```yaml
all:
  # ...

  kRatePlugin:
    guardbind: true

  # ...
```

It's also possible to only allow authenticated users to vote (disabled by default) :

_apps/frontend/app.yml_

```yaml
all:
  # ...

  kRatePlugin:
    guardbind: true
    restrict:  true

  # ...
```

Note that both **guardbind** and **restrict** options have to be set to true.

## New methods for ratables objects

```php
<?php
$article = new Article(); // hasRates() : false, getNbRates() : 0, getAvgRating() : NULL


$rate = new Rate();
$rate->setValue(3);

$article->addRate($rate); // add the rate and update the article

$article->hasRates(); // true
$article->getNbRates(); // 1
$article->getAvgRating(); // 3.0


$other_rate = new Rate();
$other_rate->setValue(4);

$article->addRate($rate, $this->getUser()); // when guardbind = true

$article->hasRates(); // true
$article->getNbRates(); // 2
$article->getAvgRating(); // 3.5
$article->getRate($this->getUser()); // returns a rate object corresponding to the given user (or null)
$article->getAllRates();

```

## New methods for users

```php
<?php
// in an action for instance :
$this->getUser()->canVote(); // returns a boolean depanding on the guardbind and the restrict options, and the authentication state of the user
```

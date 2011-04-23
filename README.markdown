kNotePlugin provides a Doctrine behavior, controllers, templates and
assets to set up a noting system quickly.

## Doctrine behavior

To declare that a model is notable, add the behavior in your
config/doctrine/schema.yml file:

    Article:
      actAs:
        Ratable:

A new table, notes, is created to store the article (and other models)
notes.

## Display rating stars

Include the plugin assets in your apps/front/config/view.yml file:

    stylesheets:
      - main.css
      - /kRatePlugin/css/jquery.rating.css
      - ...

    javascripts:
      - jquery.min.js # not included in the plugin
      - /kRatePlugin/js/jquery.rating.js
      - ...

Include the note/formNote component from your record template.

    <?php include_component('note', 'formNote', array('object' => $article)) ?>
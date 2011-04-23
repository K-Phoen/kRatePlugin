<?php

/**
 *
 */
class kRateRouting
{
  /**
   * Listens to the routing.load_configuration event.
   *
   * @param sfEvent An sfEvent instance
   * @static
   */
    static public function listenToRoutingLoadConfigurationEvent(sfEvent $event)
    {
      $r = $event->getSubject();

      // preprend our routes
      $r->prependRoute('add-rate',
        new sfRoute('/add-rate/:model/:id',
          array('module' => 'rate', 'action' => 'addRate'),
          array('sf_method' => array('post'), 'id' => '\d+')
        ));
    }
}

<?php

/**
 * kRatePlugin configuration.
 *
 * @package     kRatePlugin
 * @subpackage  config
 * @author      Your name here
 * @version     SVN: $Id: PluginConfiguration.class.php 17207 2009-04-10 15:36:26Z Kris.Wallsmith $
 */
class kRatePluginConfiguration extends sfPluginConfiguration
{
  const VERSION = '1.0.0-DEV';

  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    // add the routing
    $this->dispatcher->connect('routing.load_configuration',
      array('kRateRouting', 'listenToRoutingLoadConfigurationEvent'));

    // allows us to access the current user in the RateForm
    $this->dispatcher->connect('context.load_factories',
      array($this, 'listenToLoadFactoriesEvent'));
  }

  public function listenToLoadFactoriesEvent(sfEvent $event)
  {
    PluginRateForm::setUser($event->getSubject()->getUser());
  }
}

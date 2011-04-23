<?php

/**
 * BaseRateComponents components.
 *
 * @package     kRatePlugin
 * @subpackage  note
 * @author      Your name here
 * @version     SVN: $Id: BaseActions.class.php 12534 2008-11-01 13:38:27Z Kris.Wallsmith $
 */
class BaseRateComponents extends sfComponents
{
  public function executeFormRate(sfWebRequest $request)
  {
    $this->form = new RateForm(null, array('user' => $this->getUser(), 'object' => $this->object));
  }
}

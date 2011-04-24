<?php

/**
 * Base actions for the kRatePlugin rate module.
 *
 * @package     kRatePlugin
 * @subpackage  rate
 * @author      Your name here
 * @version     SVN: $Id: BaseActions.class.php 12534 2008-11-01 13:38:27Z Kris.Wallsmith $
 */
abstract class BaseRateActions extends sfActions
{
  /**
   * entry point for AddRate action
   *
   * @param sfWebRequest $request the user request
   * @return void
   */
  public function executeAddRate(sfWebRequest $request)
  {
    // check if the current user can rate this item
    $this->forward404Unless(kRateTools::canVote($this->getUser()), 'You can not rate this item.').

    // we retrieve the object to rate
    $object = Doctrine::getTable($request->getParameter('model'))->find($request->getParameter('id'));

    $form = new RateForm(null, array('user' => $this->getUser(), 'object' => $object));

    return $this->processForm($form, $request);
  }

  protected function processForm(sfForm $form, sfWebRequest $request)
  {
    $form->bind($request->getParameter($form->getName()));
    if ($form->isValid())
    {
      $form->save();

      if ($request->isXmlHttpRequest())
      {
        $this->getResponse()->setHeaderOnly(true);
        $this->getResponse()->setStatusCode(200);

        return sfView::NONE;
      }

      $this->redirect($request->getReferer());
    }

    // incorrect form submitted: error 500
    $this->getResponse()->setHeaderOnly(true);
    $this->getResponse()->setStatusCode(500);

    return sfView::NONE;
  }
}

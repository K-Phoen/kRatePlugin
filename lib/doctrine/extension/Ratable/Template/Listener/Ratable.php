<?php

class Doctrine_Template_Listener_Ratable extends Doctrine_Record_Listener
{
  public function postDelete(Doctrine_Event $event)
  {
    $event->getInvoker()->getAllRates()->delete();
  }
}

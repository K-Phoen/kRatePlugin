<?php

class kRateUser
{
  public static function listenToMethodNotFound(sfEvent $event)
  {
    if(method_exists('kRateUser', $event['method']))
    {
      $event->setReturnValue(call_user_func_array(
        array('kRateUser', $event['method']),
        array_merge(array($event->getSubject()), $event['arguments'])
      ));

      return true;
    }
  }

  /**
   * Tells if the given user can vote.
   *
   * @param sfUser $user The user to test
   *
   * @return bool
   * @author Kevin Gomez <contact@kevingomez.fr>
   */
  static public function canVote($user)
  {
    if (!kRateTools::isRatingRestricted())
    {
      return true;
    }

    return !is_null($user) && $user->isAuthenticated();
  }
}

<?php

class Doctrine_Template_Ratable extends Doctrine_Template
{
  public function setTableDefinition()
  {
    $this->addListener(new Doctrine_Template_Listener_Ratable($this->_options));
  }

  public function getModel()
  {
    return $this->_invoker->getTable()->getComponentName();
  }

  public function getItemId()
  {
    return $this->_invoker->get('id');
  }

  /**
   * Returns the average rating of the current item.
   *
   * @return float The average rating.
   * @author Kevin Gomez <contact@kevingomez.fr>
   */
  public function getAvgRating()
  {
    return Doctrine_Query::create()
            ->select('AVG(value) as avg_val')
            ->from('Rate')
            ->where('record_model = ? AND record_id = ?', array(
                $this->getModel(),
                $this->getItemId(),
              ))
            ->fetchOne()

            ->avg_val;

  }

  /**
   * Tells whether the ratable item already has rates.
   *
   * @return bool
   * @author Kevin Gomez <contact@kevingomez.fr>
   */
  public function hasRates()
  {
    return $this->getNbRates() > 0;
  }

  /**
   * Returns the total number of rates for the current item.
   *
   * @return int
   * @author Kevin Gomez <contact@kevingomez.fr>
   */
  public function getNbRates()
  {
    return $this->getRatesQuery()->count();
  }

  /**
   * Adds a rate to the current item
   *
   * @param Rate    $rate     The rate object to add to the current item.
   * @param sfUser  $user     The user who rated the item. Leave to null if
   *                          there is no user to bind.
   *
   * @return mixed The current item.
   * @author Kevin Gomez <contact@kevingomez.fr>
   */
  public function addRate(Rate $rate, sfUser $user)
  {
    $rate->set('record_model', $this->getModel());
    $rate->set('record_id', $this->getItemId());

    // if needed, we bind the vote to an user
    if(kRateTools::isGuardBindEnabled() && !is_null($user) && $user->isAuthenticated())
    {
      $rate->set('user_id',$user->getGuardUser()->getId());
    }

    $rate->save();

    return $this->_invoker;
  }

  /**
   * Returns all the rates of the current item.
   *
   * @return Doctrine_Collection The rates.
   * @author Kevin Gomez <contact@kevingomez.fr>
   */
  public function getAllRates()
  {
    return $this->getRatesQuery();
  }

  /**
   * Returns the query used to access all the rates of the current item.
   *
   * @return Doctrine_Collection The rates query.
   * @author Kevin Gomez <contact@kevingomez.fr>
   */
  public function getRatesQuery()
  {
    $query = Doctrine::getTable('Rate')->createQuery('n')
              ->where('n.record_id = ?', $this->getItemId())
              ->andWhere('n.record_model = ?', $this->getModel());

    if (kRateTools::isGuardBindEnabled())
    {
      $query->leftJoin('n.User as u');
    }

    return $query;
  }

  /**
   * Returns the rate given by a user to the current item.
   *
   * @param sfUser $user The user to check
   *
   * @return bool
   * @author Kevin Gomez <contact@kevingomez.fr>
   */
  public function getRate(sfUser $user)
  {
    if (!kRateTools::isGuardBindEnabled())
    {
      return null;
    }

    return $this->getRatesQuery()
                ->andWhere('n.user_id = ?', array($user->getGuardUser()->getId()))
                ->fetchOne();
  }
}

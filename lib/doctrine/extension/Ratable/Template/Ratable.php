<?php

class Doctrine_Template_Ratable extends Doctrine_Template
{
  public function setTableDefinition()
  {
    $this->hasColumn('avg_rating', 'float');
    $this->hasColumn('nb_rates', 'integer', 4, array(
        'unsigned'  => true,
        'default'   => 0
      )
    );

    $this->addListener(new Doctrine_Template_Listener_Ratable());
  }

  public function getModel()
  {
    return $this->getInvoker()->getTable()->getComponentName();
  }

  public function getItemId()
  {
    return $this->getInvoker()->get('id');
  }

  /**
   * Returns the average rating of the current item by computing it using
   * the rates stored in the "rates" table.
   *
   * @return float The average rating.
   * @author Kevin Gomez <contact@kevingomez.fr>
   */
  public function computeAvgRating()
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
    return $this->getInvoker()->getNbRates() > 0;
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
  public function addRate(Rate $rate, sfUser $user=null)
  {
    if (is_null($this->getItemId()))
    {
      throw new LogicException('You can not rate an object before having saved it');
    }

    // update the ratable object
    if ($rate->getCreatedAt() == $rate->getUpdatedAt())
    {
      $this->getInvoker()->setNbRates($this->getInvoker()->getNbRates() + 1);
      $this->getInvoker()->setAvgRating(($this->getInvoker()->getAvgRating() + $rate->getValue()) / $this->getInvoker()->getNbRates());
    }
    else
    {
      $this->getInvoker()->setAvgRating($this->getInvoker()->computeAvgRating());
    }

    // update the rate object itself
    $rate->set('record_model', $this->getModel());
    $rate->set('record_id', $this->getItemId());

    // if needed, we bind the vote to an user
    if(kRateTools::isGuardBindEnabled() && !is_null($user) && $user->isAuthenticated())
    {
      $rate->set('user_id', $user->getGuardUser()->getId());
    }

    // save all
    $rate->save();
    $this->getInvoker()->save();

    return $this->getInvoker();
  }

  /**
   * Returns all the rates of the current item.
   *
   * @return Doctrine_Collection The rates.
   * @author Kevin Gomez <contact@kevingomez.fr>
   */
  public function getAllRates()
  {
    return $this->getRatesQuery()->execute();
  }

  /**
   * Returns the query used to access all the rates of the current item.
   *
   * @return Doctrine_Collection The rates query.
   * @author Kevin Gomez <contact@kevingomez.fr>
   */
  protected function getRatesQuery()
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

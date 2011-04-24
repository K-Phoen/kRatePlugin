<?php

/**
 * PluginRate form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginRateForm extends BaseRateForm
{
  /**
   * Constructor. Checks if the following options are given :
   *  - user : a sfUser instance representing the current user (if enabled in
   *    app.yml).
   *  - object : the object to rate.
   *
   * @see sfFormObject
   * @return void
   * @author Kevin Gomez <contact@kevingomez.fr>
   */
  public function __construct($object = null, $options = array(), $CSRFSecret = null)
  {
    if (!isset($options['user']) && kRateTools::isGuardBindEnabled())
    {
      throw new LogicException('The "user" option is missing');
    }

    if (!isset($options['object']))
    {
      throw new LogicException('The "object" option is missing');
    }

    // check ig the user already rated this item
    $note = null;
    if (kRateTools::canVote($options['user']))
    {
      $note = $options['object']->getRate($options['user']);
    }

    parent::__construct($note, $options, $CSRFSecret);
  }

  /**
   * Tells if the current user can vote using this form.
   *
   * @return bool
   * @author Kevin Gomez <contact@kevingomez.fr>
   */
  public function canVote()
  {
    return kRateTools::canVote($this->options['user']);
  }

  /**
   * Used to display only the "value" field.
   *
   * @see sfForm
   * @return void
   * @author Kevin Gomez <contact@kevingomez.fr>
   **/
  public function setup()
  {
    parent::setup();

    $this->useFields(array('value'));

    // tweak the value's widget to display a select instead of a simple text input
    $this->widgetSchema['value'] = new sfWidgetFormChoice(array(
      'choices'  => $this->generatePossibleRates(),
    ));
    $this->validatorSchema['value'] = new sfValidatorChoice(array(
      'choices' => array_keys($this->generatePossibleRates()),
    ));

    // add labels
    $this->widgetSchema->setLabel('value', 'Note');

    // add help texts
    //$this->widgetSchema->setHelp('value', sprintf('The note must be between %d and %d.', kRateTools::getMinNote(), kRateTools::getMaxNote()));
  }

  /**
   * Overrides the doUpdateObject method to add the following fields to the
   * object to save :
   *  - user_id (if needed)
   *  - record_id
   *  - record_model
   *
   * @see sfFormObject
   * @return void
   * @author Kevin Gomez <contact@kevingomez.fr>
   **/
  protected function doUpdateObject($values)
  {
    $values = array_merge($values, array(
      'record_id'     => $this->options['object']->getItemId(),
      'record_model'  => $this->options['object']->getModel(),
    ));

    // if needed, we bind the vote to an user
    if (kRateTools::canVote($this->options['user']))
    {
      $values['user_id'] = $this->options['user']->getGuardUser()->getId();
    }

    parent::doUpdateObject($values);
  }

  /**
   * Generates an array with all the possible rates.
   *
   * @return array Array of possible notes.
   * @author Kevin Gomez <contact@kevingomez.fr>
   */
  protected function generatePossibleRates()
  {
    $rates = array();
    foreach(range(kRateTools::getMinRate(), kRateTools::getMaxRate()) as $i)
    {
      $rates[$i] = $i;
    }

    return $rates;
  }
}

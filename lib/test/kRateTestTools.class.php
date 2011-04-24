<?php

/**
 * Som test tools
 */
class kRateTestTools
{
  public static function loadData()
  {
    Doctrine_Core::loadData(sfConfig::get('sf_test_dir').'/fixtures');
  }

  public static function getNonRatedObject()
  {
    return Doctrine::getTable('RatableTestObject')->find(2);
  }

  public static function getRatedObject()
  {
    return Doctrine::getTable('RatableTestObject')->find(1);
  }

  public static function getMultiRatedObject()
  {
    return Doctrine::getTable('RatableTestObject')->find(3);
  }
}

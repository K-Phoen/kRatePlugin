<?php

/**
 * Som test tools
 */
class kRateTestTools
{
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

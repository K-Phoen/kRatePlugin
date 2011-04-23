<?php

class kRateTools
{
  const GUARD_BIND_OPTION = 'app_kRatePlugin_guardbind';
  const MAX_RATE_OPTION   = 'app_kRatePlugin_max';
  const MIN_RATE_OPTION   = 'app_kRatePlugin_min';

  /**
   * Tells if we should bind a vote to a guard user
   *
   * @return boolean
   */
  static function isGuardBindEnabled()
  {
    return sfConfig::get(self::GUARD_BIND_OPTION);
  }

  /**
   * Returns the mininum rate allowed.
   *
   * @return int The minimum rate.
   * @author Kevin Gomez <contact@kevingomez.fr>
   */
  static public function getMinRate()
  {
    return sfConfig::get(self::MIN_RATE_OPTION);
  }

  /**
   * Returns the maxinum rate allowed.
   *
   * @return int The maximum rate.
   * @author Kevin Gomez <contact@kevingomez.fr>
   */
  static public function getMaxRate()
  {
    return sfConfig::get(self::MAX_RATE_OPTION);
  }
}

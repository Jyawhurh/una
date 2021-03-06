<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    BaseProfile Base classes for profile modules
 * @ingroup     TridentModules
 *
 * @{
 */

/**
 * View entry actions menu in popup
 */
class BxBaseModProfileMenuViewActions extends BxBaseModProfileMenuView
{
    protected function getMenuItemsRaw ()
    {
        return $this->_getMenuItemsCombined ();
    }
}

/** @} */

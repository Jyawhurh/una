<?php
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 */

class BxPhotosUpdater extends BxDolStudioUpdater
{
    function __construct($aConfig)
	{
        parent::__construct($aConfig);
    }

	public function actionExecuteSql($sOperation)
    {
    	if($sOperation == 'install') {
    		if(!$this->oDb->isFieldExists('bx_photos_entries', 'score'))
        		$this->oDb->query("ALTER TABLE `bx_photos_entries` ADD `score` int(11) NOT NULL default '0' AFTER `svotes`");
			if(!$this->oDb->isFieldExists('bx_photos_entries', 'sc_up'))
        		$this->oDb->query("ALTER TABLE `bx_photos_entries` ADD `sc_up` int(11) NOT NULL default '0' AFTER `score`");
			if(!$this->oDb->isFieldExists('bx_photos_entries', 'sc_down'))
        		$this->oDb->query("ALTER TABLE `bx_photos_entries` ADD `sc_down` int(11) NOT NULL default '0' AFTER `sc_up`");
		}

    	return parent::actionExecuteSql($sOperation);
    }
}

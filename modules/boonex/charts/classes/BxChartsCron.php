<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Charts Charts
 * @ingroup     UnaModules
 *
 * @{
 */

class BxChartsCron extends BxDolCron
{
    protected $_sModule;
    protected $_oModule;

    public function __construct()
    {
        $this->_sModule = 'bx_charts';
        $this->_oModule = BxDolModule::getInstance($this->_sModule);

        parent::__construct();
    }

    function processing()
    {
        $this->processingTopByLikes();
        $this->processingMostActiveProfiles();
    }
    
    function processingTopByLikes()
    {
        $aSystems = BxDolVote::getSystems();
        $aModules = array_keys($this->_oModule->serviceGetTextModules());
        $sModulesDisabled = explode(',', getParam('bx_charts_chart_top_contents_by_likes_modules_disabled'));
        $aModules = array_diff($aModules, $sModulesDisabled);
        $this->_oModule->_oDb->clearTopByLikes();
        foreach($aModules as $sModule){
            $oModule = BxDolModule::getInstance($sModule);
            $sSystem = $oModule->_oConfig->CNF['OBJECT_VOTES'];
            if(empty($sSystem))
                continue;
            $this->_oModule->_oDb->saveTopByLikes($sModule, $aSystems[$sSystem]['table_main']);
        }
    }
    
    function processingMostActiveProfiles()
    {
        $this->_oModule->_oDb->clearMostActiveProfiles();
        $sContentModule = getParam('bx_charts_chart_most_active_profiles_posts_for_module');
        $oContentModule = BxDolModule::getInstance($sContentModule);
        if ($oContentModule){
            $sContentTable = $oContentModule->_oConfig->CNF['TABLE_ENTRIES'];
            $sColumnAuthor = $oContentModule->_oConfig->CNF['FIELD_AUTHOR'];
            if (!empty($sContentTable) && !empty($sColumnAuthor)){
                $aSystems = BxDolView::getSystems();
                $aModules = array_keys($this->_oModule->serviceGetProfileModules());
                $sModulesDisabled = explode(',', getParam('bx_charts_chart_most_active_profiles_modules_disabled'));
                $aModules = array_diff($aModules, $sModulesDisabled);
                foreach($aModules as $sModule){
                    $oModule = BxDolModule::getInstance($sModule);
                    $sSystem = $oModule->_oConfig->CNF['OBJECT_VIEWS'];
                    if(empty($sSystem))
                        continue;
                    $this->_oModule->_oDb->saveMostActiveProfiles_View($sModule, $aSystems[$sSystem]['table_track']);
                    $this->_oModule->_oDb->saveMostActiveProfiles_Create($sModule, $sContentTable, $sColumnAuthor);
                }
            }
        }
    }
}

/** @} */

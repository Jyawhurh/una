<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    BaseNotifications Base classes for Notifications like modules
 * @ingroup     UnaModules
 *
 * @{
 */

class BxBaseModNotificationsTemplate extends BxBaseModGeneralTemplate
{
	function __construct(&$oConfig, &$oDb)
    {
        parent::__construct($oConfig, $oDb);
    }

	public function getCssJs()
    {
        $this->addCss(array(
            'view.css',
            'view-media-tablet.css',
            'view-media-desktop.css',
        ));
        $this->addJs(array(
        	'jquery.anim.js',
            'main.js',
            'view.js',
        ));
    }

	public function getJsCode($sType, $aParams = array(), $bWrap = true)
    {
        $oModule = $this->getModule();

        $aParams = array_merge(array(
        	'iOwnerId' => $oModule->_iOwnerId,
            'sAnimationEffect' => $this->_oConfig->getAnimationEffect(),
            'iAnimationSpeed' => $this->_oConfig->getAnimationSpeed(),
            'aHtmlIds' => $this->_oConfig->getHtmlIds($sType)
        ), $aParams);

        $this->getCssJs();
        return parent::getJsCode($sType, $aParams, $bWrap);
    }
    
    public function getUnit(&$aEvent, $aBrowseParams = array())
    {
    	return '';
    }

	function unit($aData, $isCheckPrivateContent = true, $sTemplateName = 'unit.html')
    {
    	$this->getCssJs();

    	if($sTemplateName == 'unit.html')
    		return $this->getUnit($aData);

        $oModule = $this->getModule();
        $CNF = &$this->_oConfig->CNF;

        if ($isCheckPrivateContent && CHECK_ACTION_RESULT_ALLOWED !== ($sMsg = $oModule->isAllowedView($aData))) {
            $aVars = array (
                'summary' => $sMsg,
            );
            return $this->parseHtmlByName('unit_private.html', $aVars);
        }

        list($sAuthorName, $sAuthorUrl, $sAuthorIcon) = $oModule->getUserInfo($aData['object_id']);
        $bAuthorIcon = !empty($sAuthorIcon);

        // generate html
        $aVars = array (
            'id' => $aData['id'],
            'author' => $sAuthorName,
            'author_url' => $sAuthorUrl,
            'title' => bx_process_output($aData['title'], BX_DATA_HTML),
        	'item_url' => $this->_oConfig->getItemViewUrl($aData),
            'item_date' => bx_time_js($aData['date'], BX_FORMAT_DATE),
            'module_name' => _t($CNF['T']['txt_sample_single_ext']),
            'ts' => $aData['date'],
            'bx_if:show_icon' => array(
                'condition' => $bAuthorIcon,
                'content' => array(
                    'author_icon' => $sAuthorIcon
                )
            ),
            'bx_if:show_icon_empty' => array(
                'condition' => !$bAuthorIcon,
                'content' => array()
            ),
        );

        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

}

/** @} */

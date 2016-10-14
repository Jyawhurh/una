<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    BaseText Base classes for text modules
 * @ingroup     TridentModules
 *
 * @{
 */

class BxBaseModTextSearchResult extends BxBaseModGeneralSearchResult
{
    protected $aUnitViews = array('extended' => 'unit.html', 'gallery' => 'unit_gallery.html', 'full' => 'unit_full.html');
    protected $sUnitViewDefault = 'gallery';
    protected $sUnitViewParamName = 'unit_view';

    function __construct($sMode = '', $aParams = array())
    {
        parent::__construct($sMode, $aParams);

        if (!empty($aParams['unit_view']))
            $this->sUnitViewDefault = $aParams['unit_view'];

        $this->aGetParams = array($this->sUnitViewParamName);
        $this->sUnitTemplate = $this->aUnitViews[$this->sUnitViewDefault];
        if (isset($this->aUnitViews[bx_get($this->sUnitViewParamName)]))
            $this->sUnitTemplate = $this->aUnitViews[bx_get($this->sUnitViewParamName)];

        if ('unit_gallery.html' == $this->sUnitTemplate)
            $this->addContainerClass (array('bx-def-margin-sec-lefttopright-neg', 'bx-base-text-unit-gallery-wrapper'));
    }

    protected function processReplaceableMarkers($oProfileAuthor) 
    {
        if ($oProfileAuthor) {
            $this->addMarkers($oProfileAuthor->getInfo()); // profile info is replaceable
            $this->addMarkers(array('profile_id' => $oProfileAuthor->id())); // profile id is replaceable
            $this->addMarkers(array('display_name' => $oProfileAuthor->getDisplayName())); // profile display name is replaceable
        }

        $this->sBrowseUrl = $this->_replaceMarkers($this->sBrowseUrl);
        $this->aCurrent['title'] = $this->_replaceMarkers($this->aCurrent['title']);
    }

    protected function addConditionsForPrivateContent($CNF, $oProfileAuthor) 
    {
        // add conditions for private content
        $oPrivacy = BxDolPrivacy::getObjectInstance($CNF['OBJECT_PRIVACY_VIEW']);
        $a = $oPrivacy ? $oPrivacy->getContentPublicAsCondition($oProfileAuthor ? $oProfileAuthor->id() : 0) : array();
        if (isset($a['restriction']))
            $this->aCurrent['restriction'] = array_merge($this->aCurrent['restriction'], $a['restriction']);
        if (isset($a['join']))
            $this->aCurrent['join'] = array_merge($this->aCurrent['join'], $a['join']);

        $this->setProcessPrivateContent(false);
    }

    protected function getCurrentOnclick($aAdditionalParams = array(), $bReplacePagesParams = true) 
    {
        // always add UnitView as additional param
        $sUnitView = bx_process_input(bx_get($this->sUnitViewParamName));
        if ($sUnitView && isset($this->aUnitViews[$sUnitView]))
            $aAdditionalParams = array_merge(array($this->sUnitViewParamName => $sUnitView), $aAdditionalParams);

        return parent::getCurrentOnclick($aAdditionalParams, $bReplacePagesParams);
    }

    protected function _updateCurrentForAuthor($sMode, $aParams, &$oProfileAuthor)
    {
        $CNF = &$this->oModule->_oConfig->CNF;

        $oProfileAuthor = BxDolProfile::getInstance((int)$aParams['author']);
        if (!$oProfileAuthor) {
            $this->isError = true;
            break;
        }

        $iProfileAuthor = $oProfileAuthor->id();
        $this->aCurrent['restriction']['author']['value'] = $iProfileAuthor;

        if(!empty($aParams['except']))
        	$this->aCurrent['restriction']['except']['value'] = is_array($aParams['except']) ? $aParams['except'] : array($aParams['except']); 

        if(!empty($aParams['per_page']))
        	$this->aCurrent['paginate']['perPage'] = is_numeric($aParams['per_page']) ? (int)$aParams['per_page'] : (int)getParam($aParams['per_page']);

        $this->sBrowseUrl = 'page.php?i=' . $CNF['URI_AUTHOR_ENTRIES'] . '&profile_id={profile_id}';
        $this->aCurrent['title'] = _t($CNF['T']['txt_all_entries_by_author']);
        $this->aCurrent['rss']['link'] = 'modules/?r=posts/rss/' . $sMode . '/' . $iProfileAuthor;
    }

    protected function _updateCurrentForFavorite($sMode, $aParams, &$oProfileAuthor)
    {
        $CNF = &$this->oModule->_oConfig->CNF;

        $oProfileAuthor = BxDolProfile::getInstance((int)$aParams['user']);
        if (!$oProfileAuthor) {
            $this->isError = true;
            break;
        }

        $iProfileAuthor = $oProfileAuthor->id();
        $oFavorite = $this->oModule->getObjectFavorite();
        if(!$oFavorite->isPublic() && $iProfileAuthor != bx_get_logged_profile_id()) {
            $this->isError = true;
            break;
        }

        $aConditions = $oFavorite->getConditionsTrack($this->aCurrent['table'], 'id', $iProfileAuthor);
        if(!empty($aConditions) && is_array($aConditions))
            $this->aCurrent = array_merge($this->aCurrent, $aConditions);

        $this->sBrowseUrl = 'page.php?i=' . $CNF['URI_AUTHOR_ENTRIES'] . '&profile_id={profile_id}';
        $this->aCurrent['title'] = _t($CNF['T']['txt_all_entries_by_author']);
        $this->aCurrent['rss']['link'] = 'modules/?r=posts/rss/' . $sMode . '/' . $iProfileAuthor;
    }

    function _getPseud ()
    {
        return array(
            'id' => 'id',
            'title' => 'title',
            'text' => 'text',
            'added' => 'added',
            'author' => 'author',
            'photo' => 'photo',
        );
    }
}

/** @} */

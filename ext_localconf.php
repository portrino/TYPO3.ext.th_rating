<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

//require_once( t3lib_extMgm::extPath($_EXTKEY) . 'Classes/Utility/ExtensionManagementUtility.php');
require_once( t3lib_extMgm::extPath($_EXTKEY) . 'Classes/Utility/TCALabelUserFuncUtility.php');

/**
 * Configure the Plugin to call the 
 * right combination of Controller and Action according to
 * the user input (default settings, FlexForm, URL etc.)
 */
Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,	// The extension name (in UpperCamelCase) or the extension key (in lower_underscore)
	'Pi1',		// A unique name of the plugin in UpperCamelCase
	array(		// An array holding the controller-action-combinations that are accessible 
		'Vote' 			=> 'ratinglinks,index,show,create,new,singleton',	// The first controller and its first action will be the default 
	),
	array(		// An array of non-cachable controller-action-combinations (they must already be enabled)
		'Vote' 			=> 'new,create,ratinglinks',
		)
);

// here we register "tx_thrating_unlinkDynCss_eval" to remove the dynamic CSS file when values are modified in the BE
$TYPO3_CONF_VARS['SC_OPTIONS']['tce']['formevals']['tx_thrating_unlinkDynCss_eval'] = 'EXT:th_rating/Classes/Utility/BE.tx_thrating_unlinkDynCss_eval.php';
//add hook to remove the dynamic CSS file when cache is cleared
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][]='EXT:th_rating/Classes/Utility/TCALabelUserFuncUtility.php:&Tx_ThRating_Utility_TCALabelUserFuncUtility->clearCachePostProc';

// Example for using signals of this extension:
//$signalSlotDispatcher = t3lib_div::makeInstance('Tx_Extbase_SignalSlot_Dispatcher');
//$signalSlotDispatcher->connect('Tx_ThRating_Controller_VoteController', 'afterRatinglinkAction', 'Tx_ThRating_Controller_VoteController', 'afterRatinglinkActionHandler',FALSE);
//$signalSlotDispatcher->connect('Tx_ThRating_Controller_VoteController', 'afterCreateAction', 'Tx_ThRating_Controller_VoteController', 'afterafterCreateActionHandler',FALSE);
?>
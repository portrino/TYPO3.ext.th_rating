<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Thomas Hucke <thucke@web.de> 
*  All rights reserved
*
*  This class is a backport of the corresponding class of FLOW3.
*  All credits go to the v5 team.
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * A validator for Ratings
 *
 * @author		Thomas Hucke <thucke@web.de>
 * @copyright Copyright belongs to the respective authors
 * @scope singleton
 */
class Tx_ThRating_Domain_Validator_StepconfValidator extends Tx_Extbase_Validation_Validator_AbstractValidator {

	/**
     * @var Tx_ThRating_Domain_Repository_StepconfRepository
     */
    protected $stepconfRepository;

    /**
     * @param Tx_ThRating_Domain_Repository_StepconfRepository $stepconfRepository
     * @return void
     */
    public function injectStepconfRepository(Tx_ThRating_Domain_Repository_StepconfRepository $stepconfRepository) {
        $this->stepconfRepository = $stepconfRepository;
    }
	/**
     * @var Tx_ThRating_Domain_Repository_StepnameRepository
     */
    protected $stepnameRepository;

    /**
     * @param Tx_ThRating_Domain_Repository_StepnameRepository $stepnameRepository
     * @return void
     */
    public function injectStepnameRepository(Tx_ThRating_Domain_Repository_StepnameRepository $stepnameRepository) {
        $this->stepnameRepository = $stepnameRepository;
    }
	
	/**
	 * If the given step is valid
	 *
	 * @param Tx_ThRating_Domain_Model_Stepconf $stepconf
	 * @return boolean
	 */
	public function isValid($stepconf) {
		//a stepconf object must have a ratingobject
		if (!$stepconf->getRatingobject() instanceof Tx_ThRating_Domain_Model_Ratingobject) {
			$this->addError(Tx_Extbase_Utility_Localization::translate('error.validator.stepconf.ratingobject', 'ThRating'), 1284700846);
			return FALSE;
		}
		//at least a steporder value must be set
		$steporder = $stepconf->getSteporder();
		if (empty($steporder)) {
			$this->addError(Tx_Extbase_Utility_Localization::translate('error.validator.stepconf.steps', 'ThRating'), 1284700903);
			return FALSE;
		}

		//steporder must be positive integer ( >0 )
		If ( !is_int($stepconf->getSteporder()) or $stepconf->getSteporder()<1 ) {
			$this->addError(Tx_Extbase_Utility_Localization::translate('error.validator.stepconf.invalidSteporderNumber', 'ThRating'), 1368123953);
			return FALSE;
		}

		//check if given steporder is valid (integer, maximum +1)
		$maxSteporderStepconfobject = $this->stepconfRepository->findByRatingobject($stepconf->getRatingobject());
		$maxSteporder = $maxSteporderStepconfobject[$maxSteporderStepconfobject->count()-1]->getSteporder();
		If ( $stepconf->getSteporder() > $maxSteporder+1 ) {
			$this->addError(Tx_Extbase_Utility_Localization::translate('error.validator.stepconf.maxSteporder', 'ThRating'), 1368123970);
			return FALSE;
		}
		
		//check if a stepname is given that at least the default language exists
		$stepname = $stepconf->getStepname();
		If (is_object($stepname)) {
			$countNames = $stepname->count();
		}
		If ($countNames!=0) {
			$firstStepname = $stepname->current();
			$defaultName = $this->stepnameRepository->findDefaultStepname($firstStepname);
			If (empty($defaultName)) {
				$this->addError(Tx_Extbase_Utility_Localization::translate('error.validator.stepconf.defaultStepname', 'ThRating', array($firstStepname->getStepconf()->getUid())), 1384374165);
				return FALSE;
			}
			//Finally check on language constistency
			$checkConsistency = $this->stepnameRepository->checkConsistency($firstStepname);
			If ($checkConsistency['doubleLang']) {
				$this->addError(Tx_Extbase_Utility_Localization::translate('error.validator.stepconf.doubleLangEntry', 'ThRating', array($firstStepname->getStepconf()->getUid())), 1384374589);
				return FALSE;
			}		
			If ($checkConsistency['existLang']) {
				$this->addError(Tx_Extbase_Utility_Localization::translate('error.validator.stepconf.notExistingLanguage', 'ThRating', array($firstStepname->getUid())), 1384374589);
				return FALSE;
			}		
		}
		return TRUE;
	}
}
?>
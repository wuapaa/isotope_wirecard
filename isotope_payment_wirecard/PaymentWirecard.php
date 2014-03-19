<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  iBROWs Web Communications GmbH 2010
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @author	   Andreas Wilm <andreas.wilm@wuapaa.com>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 * @version    $Id$
 */


class PaymentWirecard extends IsotopePayment
{
	
	
	
	private $requestFingerprintOrder = "";
	private $requestFingerprintSeed = "";
	
	/**
	 * The hosting gateway URL to create payinit URL
	 * @var string
	 */
	protected $strInitUrl = 'https://secure.wirecard-cee.com/qpay/init.php';
	 
	
	/**
	* Return a list of status options.
	* @return array
	*/
	public function statusOptions()
	{
		return array('pending', 'processing', 'complete', 'on_hold');
	}
	/**
	* Process payment
	* @return boolean
	*/
	
	
	public function processPayment()
	{
		return true;
	}
	
	
	/**
	 * HTML form for checkout
	 *
	 * @access public
	 * @return mixed
	 */
	public function checkoutForm()
	{
		
		
		$strComplete = $this->Environment->base."system/modules/isotope_payment_wirecard/success.php";
		$strFailed = $this->Environment->base."system/modules/isotope_payment_wirecard/failure.php";
		$strCancel = $this->Environment->base."system/modules/isotope_payment_wirecard/failure.php";
		$strCompleteReturn = $this->Environment->base . $this->addToUrl('step=complete');
		$strFailedReturn = $this->Environment->base . $this->addToUrl('step=failed');
		$strCancelReturn = $this->Environment->base . $this->addToUrl('step=failed');
		$strServiceUrl = $this->Environment->base;
		$strSecret = $this->wirecard_secret;
		$strAccount = $this->wirecard_accountid;
		$strDescription = $this->wirecard_description;
		$strAmount = $this->Isotope->Cart->grandTotal;
		$strCurrency = $this->Isotope->Config->currency;
		$strLanguage = strtoupper($GLOBALS['TL_LANGUAGE']);
		$objOrder = $this->Database->prepare("SELECT order_id FROM tl_iso_orders WHERE cart_id=?")
                             ->limit(1)
                             ->execute($this->Isotope->Cart->id);
		
		
		$requestFingerprintOrder .= "secret,";   
		$requestFingerprintSeed  .= $strSecret;     
		$requestFingerprintOrder .= "customerId,"; 
		$requestFingerprintSeed  .= $strAccount;   
		$requestFingerprintOrder .= "amount,"; 
		$requestFingerprintSeed  .= $strAmount;
		$requestFingerprintOrder .= "currency,"; 
		$requestFingerprintSeed  .= $strCurrency;
		$requestFingerprintOrder .= "language,"; 
		$requestFingerprintSeed  .= $strLanguage;
		$requestFingerprintOrder .= "orderDescription,"; 
		$requestFingerprintSeed  .= $strDescription;
		$requestFingerprintOrder .= "successURL,"; 
		$requestFingerprintSeed  .= $strComplete; 
		$requestFingerprintOrder .= "requestFingerprintOrder";  
		$requestFingerprintSeed  .= $requestFingerprintOrder;    
		$requestFingerprint = md5($requestFingerprintSeed);
		$objTemplate = new FrontendTemplate('wirecard');
		$objTemplate->strInitUrl = $this->strInitUrl;
		$objTemplate->strAccount = $strAccount;
		$objTemplate->strComplete = $strComplete;
		$objTemplate->strFailed = $strFailed;
		$objTemplate->strCancel = $strFailed;
		$objTemplate->paymenttype = "CCARD";
		$objTemplate->strServiceUrl = $strServiceUrl;
		$objTemplate->strImageUrl = $strImageUrl;
		$objTemplate->strAmount = $strAmount;
		$objTemplate->strLanguage = $strLanguage;
		$objTemplate->strCurrency = $strCurrency;
		$objTemplate->strDescription = $strDescription;
		$objTemplate->requestFingerprintOrder = $requestFingerprintOrder;
		$objTemplate->requestFingerprint = $requestFingerprint;
		$objTemplate->cancelReturn = $strCancelReturn;
		$objTemplate->failedReturn = $strFailedReturn;
		$objTemplate->completeReturn = $strCompleteReturn;
		return $objTemplate->parse();

	}
}


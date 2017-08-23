<?php
/**
 * Frontuser Integration
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. Frontuser does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * =================================================================
 *                  DISCLAIMER
 * =================================================================
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Frontuser
 * @package     Frontuser_Integration
 * @author Frontuser Team <support@frontuser.com>
 * @copyright  Copyright (c) 2017 Frontuser. (https://frontuser.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Frontuser_Integration_IndexController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
	    $code = $this->getRequest()->getParam('code');
	    $track = $this->getRequest()->getParam('_track');
	    $campaign = $this->getRequest()->getParam('_campaign');

	    $redirect = 'checkout/cart';
	    if(!empty($track) && !empty($campaign)) {
		    $redirect .= "?_track=$track&_campaign=$campaign";
	    }

	    $quote = Mage::getSingleton('sales/quote')->load($code,'futoken');
	    if(!empty( $quote )) {
		    Mage::getSingleton('checkout/session')->setQuoteId($quote->getId());
	    }

	    $this->_redirect($redirect);
    }
}

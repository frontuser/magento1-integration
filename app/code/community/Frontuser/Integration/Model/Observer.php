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

class Frontuser_Integration_Model_Observer
{

	/**
	 * @param SchemaSetupInterface $setup
	 * @param ModuleContextInterface $context
	 */
	public function upgrade(Varien_Event_Observer $observer)
	{
		$quote = $observer->getEvent()->getQuote();

		if(!$quote->getFutoken()) {
			$token = Mage::helper( 'core' )->getRandomString( 32 );
			$quote->setFutoken( $token );
		}

		return $observer;
	}
}

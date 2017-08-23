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

class Frontuser_Integration_Helper_Data extends Mage_Core_Helper_Abstract
{

    const ACTIVE_MODULE = 'frontuser/general/active';
    const WEBSITE_HASH = 'frontuser/general/website_hash';
    const MATRIX_VARIABLE_FEATURE = 'frontuser/general/matrix_variable_feature';
    const GENERAL_CUSTOM_MAP_FIELDS = 'frontuser/general/customer_map_fields';
    const GENERAL_PRODUCT_MAP_FIELDS = 'frontuser/general/product_map_fields';
    const DEFAULT_LABEL = 0;

    /*
     * Check extension activation
     *
     * @return boolean
     */
    public function isActive()
    {
        $configValue = Mage::getStoreConfig(self::ACTIVE_MODULE);
        return strlen($configValue) > 0 ? $configValue : self::DEFAULT_LABEL;
    }

    /*
     * Get Website Hash Code
     *
     * @return varchar
     */
    public function getWebsiteHash()
    {
        return $configValue = Mage::getStoreConfig(self::WEBSITE_HASH);
    }

    /*
     * Check matrix data activation
     *
     * @return boolean
     */
    public function isMatrixDataActive()
    {
        $configValue = Mage::getStoreConfig(self::MATRIX_VARIABLE_FEATURE);
        return strlen($configValue) > 0 ? $configValue : self::DEFAULT_LABEL;
    }


    /**
     * Create string for current scope with format scope-scopeId.
     *
     * @return string
     */
    public function getScopeString()
    {
        $scopeArray = $this->getConfigScopeId();
        if (isset($scopeArray['websiteId'])) {
            $scopeString = 'websites-' . $scopeArray['websiteId'];
        } elseif (isset($scopeArray['storeId'])) {
            $scopeString = 'stores-' . $scopeArray['storeId'];
        } else {
            $scopeString = 'default-0';
        }
        return $scopeString;
    }


    /**
     * Get storeId and/or websiteId if scope selected on back end
     *
     * @param null $storeId
     * @param null $websiteId
     * @return array
     */
    public function getConfigScopeId($storeId = null, $websiteId = null)
    {
        $scopeArray = array();
        if ($code = Mage::getSingleton('adminhtml/config_data')->getStore()) {
            // store level
            $storeId = Mage::getModel('core/store')->load($code)->getId();
        } elseif ($code = Mage::getSingleton('adminhtml/config_data')->getWebsite()) {
            // website level
            $websiteId = Mage::getModel('core/website')->load($code)->getId();
            $storeId = Mage::app()->getWebsite($websiteId)->getDefaultStore()->getId();
        }
        $scopeArray['websiteId'] = $websiteId;
        $scopeArray['storeId'] = $storeId;
        return $scopeArray;
    }

    /**
     * Get Config value for certain scope.
     *
     * @param $path
     * @param $scopeId
     * @param null $scope
     * @return mixed
     * @throws Mage_Core_Exception
     */
    public function getConfigValueForScope($path, $scopeId, $scope = null)
    {
        if ($scope == 'websites') {
            $configValue = Mage::app()->getWebsite($scopeId)->getConfig($path);
        } else {
            $configValue = Mage::getStoreConfig($path, $scopeId);
        }
        return $configValue;
    }

    /**
     * Get customer merge fields configured for the given scope.
     *
     * @param $scopeId
     * @param null $scope
     * @return mixed
     */
    public function getCustomMergeFieldsSerialized($scopeId, $scope = null)
    {
        return $this->getConfigValueForScope(self::GENERAL_CUSTOM_MAP_FIELDS, $scopeId, $scope);
    }

    /**
     * Get product merge fields configured for the given scope.
     *
     * @param $scopeId
     * @param null $scope
     * @return mixed
     */
    public function getProductMergeFieldsSerialized($scopeId, $scope = null)
    {
        return $this->getConfigValueForScope(self::GENERAL_PRODUCT_MAP_FIELDS, $scopeId, $scope);
    }

	/**
	 * Get total order amount
	 *
	 * @return string
	 */
	public function getRevenue()
	{
		$revenue = 0;
		$order = Mage::getModel('sales/order')->load(Mage::getSingleton('checkout/session')->getLastOrderId());
		if(!empty( $order)) {
			$revenue = $order->getGrandTotal();
		}
		return number_format($revenue, 2, '.', '');
	}

	/**
	 * Get store currency code
	 *
	 * @return string
	 */
	public function getCurrency()
	{
		return Mage::app()->getStore()->getCurrentCurrencyCode();
	}

	/**
	 * Format price into 2 decimal point value
	 *
	 * @param int $price
	 * @return float
	 */
	public function formatPrice($price = 0)
	{
		return floatval(number_format($price, 2, '.', ''));
	}

	/**
	 * Return type of page based on handle
	 *
	 * @return string
	 */
	public function getPageType()
	{
		$page = Mage::getSingleton('cms/page');
		$pagetype = $handler = Mage::app()->getFrontController()->getAction()->getFullActionName();

		$action = explode( "_", $handler);
		if(is_array( $action) && count( $action) > 0) {
			$pagetype = current( $action );
		}

		if ($page->getId()) {
			if ($page->getIdentifier() == Mage::getStoreConfig('web/default/cms_home_page')) {
				$pagetype = 'home';
			} else {
				$pagetype = strtolower($page->getTitle());
			}
		}
		if (strpos($handler, 'catalogsearch_') === 0) {
			$pagetype = 'search';
		}
		if (strpos($handler, 'customer_account_') === 0) {
			$pagetype = 'customer account';
		}
		if (strpos($handler, 'customer_address_') === 0) {
			$pagetype = 'customer address';
		}
		if (strpos($handler, 'wishlist_') === 0) {
			$pagetype = 'wishlist';
		}
		if (strpos($handler, 'sales_') === 0) {
			$pagetype = 'order';
		}
		if (strpos($handler, 'review_customer_') === 0) {
			$pagetype = 'customer review';
		}
		if (strpos($handler, 'newsletter_manage_') === 0) {
			$pagetype = 'newsletter';
		}

		switch ($handler) {
			case 'catalog_category_view': $pagetype = 'category'; break;
			case 'catalog_product_view': $pagetype = 'product'; break;
			case 'checkout_cart_index': $pagetype = 'cart'; break;
			case 'checkout_onepage_index': $pagetype = 'checkout'; break;
			case 'checkout_onepage_success': $pagetype = 'success'; break;
			case 'customer_account_login': $pagetype = 'login'; break;
			case 'customer_account_create': $pagetype = 'register'; break;
			case 'customer_account_logoutSuccess': $pagetype = 'logout'; break;
			case 'customer_account_index': $pagetype = 'dashboard'; break;
			case 'review_product_list': $pagetype = 'product review'; break;
			case 'downloadable_customer_products': $pagetype = 'downloadable product'; break;
			case 'catalog_seo_sitemap_category': $pagetype = 'sitemap'; break;
			case 'contacts_index_index': $pagetype = 'contactus'; break;
			case 'catalog_product_compare_index': $pagetype = 'compare product'; break;
		}

		return $pagetype;
	}
}
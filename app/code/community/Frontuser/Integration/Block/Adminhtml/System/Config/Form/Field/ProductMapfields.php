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

class Frontuser_Integration_Block_Adminhtml_System_Config_Form_Field_ProductMapfields extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $_productAttributes;

    public function __construct()
    {
        $this->addColumn(
            'fu_product_key', array(
            'label' => Mage::helper('frontuser')->__('Key'),
            'style' => 'width:120px',
            )
        );
        $this->addColumn(
            'fu_product_value', array(
            'label' => Mage::helper('frontuser')->__('Product'),
            'style' => 'width:120px',
            )
        );
        $this->_addAfter = false;
        parent::__construct();
        $this->setTemplate('frontuser/integration/system/config/form/field/array_dropdown.phtml');

        $this->_productAttributes = array();
        $productAttrs = Mage::getResourceModel('catalog/product_attribute_collection') ->setEntityTypeFilter(1)
            ->addSetInfo()
            ->getData();


        foreach ($productAttrs as $option) {
            /** @var Mage_Catalog_Model_Resource_Eav_Attribute $productAttr */
            if ($option['frontend_label']) {
                $this->_productAttributes[$option['attribute_code']] = $option['frontend_label'];
            }
        }

        ksort($this->_productAttributes);
    }

    protected function _renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }

        $column = $this->_columns[$columnName];
        $inputName = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';

        if ($columnName == 'fu_product_value') {
            $rendered = '<select name="' . $inputName . '">';
            foreach ($this->_productAttributes as $att => $name) {
                $rendered .= '<option value="' . $att . '">' . $name . '</option>';
            }

            $rendered .= '</select>';
        } else {
            return '<input type="text" name="' . $inputName . '" value="#{' . $columnName . '}" ' . ($column['size'] ? 'size="' . $column['size'] . '"' : '') . '/>';
        }

        return $rendered;
    }

}
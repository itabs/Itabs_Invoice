<?php
/**
 * This file is part of the Itabs_Invoice extension.
 *
 * PHP version 5
 *
 * @category  Itabs
 * @package   Itabs_Invoice
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2013 ITABS GmbH (http://www.itabs.de/). All rights served.
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   1.1.0
 * @link      https://github.com/itabs/Itabs_Invoice
 */
/**
 * System Config Customer Groups
 *
 * @category  Itabs
 * @package   Itabs_Invoice
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2013 ITABS GmbH (http://www.itabs.de/). All rights served.
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   1.1.0
 * @link      https://github.com/itabs/Itabs_Invoice
 */
class Itabs_Invoice_Model_System_Config_Source_Customer_Group
{
    /**
     * @var array Customer Groups
     */
    protected $_options;

    /**
     * Returns the customer groups as an array for system configuration
     *
     * @return array Customer Groups
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $collection = Mage::getResourceModel('customer/group_collection')
                ->loadData()
                ->toOptionArray();
            $this->_options = $collection;

            array_unshift(
                $this->_options,
                array(
                    'value' => '',
                    'label' => Mage::helper('invoice')->__('-- Please Select --')
                )
            );
        }

        return $this->_options;
    }
}

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
 * System Config Invoice States
 *
 * @category  Itabs
 * @package   Itabs_Invoice
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2013 ITABS GmbH (http://www.itabs.de/). All rights served.
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   1.1.0
 * @link      https://github.com/itabs/Itabs_Invoice
 */
class Itabs_Invoice_Model_System_Config_Source_Sales_Invoice_State
{
    /**
     * @var array Invoice States
     */
    protected $_options;

    /**
     * Returns the invoice states as an array for system configuration
     *
     * @return array Invoice States
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $options = array();
            $options[] = array(
                'value' => Mage_Sales_Model_Order_Invoice::STATE_OPEN,
                'label' => Mage::helper('invoice')->__('Open')
            );
            $options[] = array(
                'value' => Mage_Sales_Model_Order_Invoice::STATE_PAID,
                'label' => Mage::helper('invoice')->__('Paid')
            );
            $this->_options = $options;
        }

        return $this->_options;
    }
}

<?php
/**
 * This file is part of the Itabs_Invoice extension.
 *
 * PHP version 5
 *
 * @category  Itabs
 * @package   Itabs_Invoice
 * @author    ITABS GmbH <info@itabs.de>
 * @copyright 2013-2015 ITABS GmbH (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   1.4.0
 * @link      https://github.com/itabs/Itabs_Invoice
 */

/**
 * Payment Method Form
 */
class Itabs_Invoice_Block_Form extends Mage_Payment_Block_Form
{
    /**
     * Set the template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('invoice/form.phtml');
    }
}

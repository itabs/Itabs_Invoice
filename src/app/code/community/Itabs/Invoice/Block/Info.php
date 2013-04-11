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
 * Payment Method Info
 *
 * @category  Itabs
 * @package   Itabs_Invoice
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2013 ITABS GmbH (http://www.itabs.de/). All rights served.
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   1.1.0
 * @link      https://github.com/itabs/Itabs_Invoice
 */
class Itabs_Invoice_Block_Info extends Mage_Payment_Block_Info
{
    /**
     * (non-PHPdoc)
     * @see Mage_Payment_Block_Info::_construct()
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('invoice/info.phtml');
    }

    /**
     * Sets the template for PDF print-outs
     *
     * @return string Text for PDF print-out
     */
    public function toPdf()
    {
        $this->setTemplate('invoice/pdf.phtml');

        return $this->toHtml();
    }

    /**
     * Retrieve the payment method code
     *
     * @return string
     */
    public function getMethodCode()
    {
        return $this->getInfo()->getMethodInstance()->getCode();
    }
}

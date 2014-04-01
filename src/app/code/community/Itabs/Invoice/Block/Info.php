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
 * @version   1.2.0
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
 * @version   1.2.0
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

    /**
     * Retrieve the due date for the order
     *
     * @return bool|string
     */
    public function getDueDate()
    {
        // Check if we are in the order mode
        if (!($this->getInfo() instanceof Mage_Sales_Model_Order_Payment)) {
            return false;
        }

        // Check if we should calculate the due date
        $calculateDay = (bool) $this->getMethod()->getConfigData('calculate_due_date');
        if (!$calculateDay) {
            return false;
        }

        // Check if there is a payment due value set
        $paymentDue = $this->getMethod()->getConfigData('payment_due');
        if (empty($paymentDue) || $paymentDue <= 0) {
            return false;
        }

        /* @var $order Mage_Sales_Model_Order */
        $order = $this->getInfo()->getOrder();

        $date = Mage::app()->getLocale()->storeDate($order->getStoreId(), strtotime($order->getCreatedAt()), false);
        $date->addDay($paymentDue);
        $dueDate = $date->toString(Mage::app()->getLocale()->getDateFormatWithLongYear());

        return $dueDate;
    }
}

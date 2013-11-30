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
 * Invoice Model
 *
 * @category  Itabs
 * @package   Itabs_Invoice
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2013 ITABS GmbH (http://www.itabs.de/). All rights served.
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   1.2.0
 * @link      https://github.com/itabs/Itabs_Invoice
 */
class Itabs_Invoice_Model_Invoice extends Mage_Payment_Model_Method_Abstract
{
    protected $_isGateway               = false;
    protected $_canAuthorize            = false;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

    /**
     * unique internal payment method identifier
     *
     * @var string [a-z0-9_]
     */
    protected $_code = 'invoice';

    /**
     * payment form block
     *
     * @var string MODULE/BLOCKNAME
     */
    protected $_formBlockType = 'invoice/form';

    /**
     * payment info block
     *
     * @var string MODULE/BLOCKNAME
     */
    protected $_infoBlockType = 'invoice/info';

    /**
     * (non-PHPdoc)
     * @see Mage_Payment_Model_Method_Abstract::authorize()
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        if (Mage::getStoreConfigFlag('payment/'.$this->getCode().'/create_invoice')) {
            /* @var $order Mage_Sales_Model_Order */
            $order = $payment->getOrder();
            $realOrderId = $payment->getOrder()->getRealOrderId();
            $order->loadByIncrementId($realOrderId);

            if ($order->canInvoice()) {
                /* @var $invoice Mage_Sales_Model_Order_Invoice */
                $invoice = $order->prepareInvoice();
                $invoice->register()->capture();
                Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder())->save();
                $order->addRelatedObject($invoice);

                $invoice->setState($this->getConfigData('invoice_state'));

                if (Mage::getStoreConfigFlag('payment/'.$this->getCode().'/send_invoice_email')) {
                    $invoice->sendEmail();
                }

                // Add comment to order history
                $order->addStatusHistoryComment('Invoiced order amount: '.$amount);
                $order->save();
            }
        }

        return $this;
    }
}

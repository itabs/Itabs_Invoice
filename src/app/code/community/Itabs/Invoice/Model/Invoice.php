<?php
/**
 * This file is part of the Itabs_Invoice module.
 *
 * PHP version 5
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category  Itabs
 * @package   Itabs_Invoice
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2011-2013 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://github.com/itabs/Itabs_Invoice
 */
/**
 * Invoice Model
 *
 * @category  Itabs
 * @package   Itabs_Invoice
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2011-2013 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
                Mage::getModel('core/resource_transaction')->addObject($invoice)->addObject($invoice->getOrder())->save();
                $order->addRelatedObject($invoice);

                $invoice->setState($this->getConfigData('invoice_state'));

                if (Mage::getStoreConfigFlag('payment/'.$this->getCode().'/send_invoice_email')) {
                    $invoice->sendEmail();
                }
            }
        }

        return $this;
    }
}

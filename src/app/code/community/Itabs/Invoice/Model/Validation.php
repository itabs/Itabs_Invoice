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
 * Validation model
 *
 * @category  Itabs
 * @package   Itabs_Invoice
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2013 ITABS GmbH (http://www.itabs.de/). All rights served.
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   1.1.0
 * @link      https://github.com/itabs/Itabs_Invoice
 */
class Itabs_Invoice_Model_Validation
{
    /**
     * @var null|Mage_Sales_Model_Resource_Order_Collection
     */
    protected $_customerOrders = null;

    /**
     * @var null|Mage_Sales_Model_Resource_Order_Collection
     */
    protected $_customerOrdersEmail = null;

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->hasSpecificCustomerGroup()
            && $this->hasMinimumOrderCount()
            && $this->hasMinimumOrderAmount()
            && $this->hasOpenInvoices()
            ;
    }

    /**
     * Check if the customer is in a specific customer group
     *
     * @return bool
     */
    public function hasSpecificCustomerGroup()
    {
        if (!Mage::getStoreConfigFlag('payment/invoice/specificgroup_all')) {
            $allowedGroupIds = explode(',', Mage::getStoreConfig('payment/invoice/specificgroup'));
            if (!in_array($this->_getCustomerGroupId(), $allowedGroupIds)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the customer has placed less complete orders than required..
     *
     * @return bool
     */
    public function hasMinimumOrderCount()
    {
        $minOrderCount = Mage::getStoreConfig('payment/invoice/customer_order_count');
        if ($minOrderCount > 0) {
            $customerId = $this->_getCustomer()->getId();
            if (is_null($customerId)) {
                return false;
            }

            $orders = $this->_getCustomerOrders($customerId);
            if (count($orders) < $minOrderCount) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the order amount of all customer order are below the
     * required order amount
     *
     * @return bool
     */
    public function hasMinimumOrderAmount()
    {
        $minOrderSum = Mage::getStoreConfig('payment/invoice/customer_order_amount');
        if ($minOrderSum > 0) {
            $customerId = $this->_getCustomer()->getId();
            if (is_null($customerId)) {
                return false;
            }

            $orders = $this->_getCustomerOrders($customerId);
            $orderTotal = 0;
            foreach ($orders as $order) {
                $orderTotal += $order->getData('grand_total');
            }

            if ($orderTotal < $minOrderSum) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if a customer has not paid invoices..
     *
     * @return bool
     */
    public function hasOpenInvoices()
    {
        if (Mage::getStoreConfig('payment/invoice/check_open_invoices')) {
            $email = $this->_getCustomerEmail();

            // Load all customer orders by email because we need to check the guests too
            if (null === $this->_customerOrdersEmail) {
                $this->_customerOrdersEmail = Mage::getResourceModel('sales/order_collection')
                    ->addAttributeToFilter('customer_email', $email)
                    ->load();
            }

            /* @var $orders Mage_Sales_Model_Resource_Order_Collection */
            $orders = $this->_customerOrdersEmail;

            $hasOpenInvoices = false;
            foreach ($orders as $order) {
                /* @var $order Mage_Sales_Model_Order */
                /* @var $invoices Mage_Sales_Model_Resource_Order_Invoice_Collection */
                $invoices = $order->getInvoiceCollection();
                if ($invoices->count() > 0) {
                    foreach ($invoices as $invoice) {
                        /* @var $invoice Mage_Sales_Model_Order_Invoice */
                        if ($invoice->getState() == 1) {
                            $hasOpenInvoices = true;
                        }
                    }
                }
            }

            if ($hasOpenInvoices) {
                return false;
            }
        }

        return true;
    }

    /**
     * Retrieve the current session
     *
     * @return Mage_Adminhtml_Model_Session_Quote|Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            /* @var $session Mage_Adminhtml_Model_Session_Quote */
            $session = Mage::getSingleton('adminhtml/session_quote');
        } else {
            /* @var $session Mage_Customer_Model_Session */
            $session = Mage::getSingleton('customer/session');
        }

        return $session;
    }

    /**
     * Retrieve the current customer
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
        return $this->_getSession()->getCustomer();
    }

    /**
     * Retrieve the customer group id of the current customer
     *
     * @return int
     */
    protected function _getCustomerGroupId()
    {
        $customerGroupId = Mage_Customer_Model_Group::NOT_LOGGED_IN_ID;
        if (Mage::app()->getStore()->isAdmin()) {
            $customerGroupId = $this->_getSession()->getQuote()->getCustomerGroupId();
        } else {
            if ($this->_getSession()->isLoggedIn()) {
                $customerGroupId = $this->_getSession()->getCustomerGroupId();
            }
        }

        return $customerGroupId;
    }

    /**
     * Retrieve the email address of the current customer
     *
     * @return string
     */
    protected function _getCustomerEmail()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            $email = $this->_getCustomer()->getEmail();
        } else {
            if ($this->_getSession()->isLoggedIn()) {
                $email = $this->_getCustomer()->getEmail();
            } else {
                /* @var $quote Mage_Sales_Model_Quote */
                $quote = Mage::getSingleton('checkout/session')->getQuote();
                $email = $quote->getBillingAddress()->getEmail();
            }
        }

        return $email;
    }

    /**
     * Retrieve the order collection of a specific customer
     *
     * @param  int $customerId
     * @return Mage_Sales_Model_Resource_Order_Collection
     */
    protected function _getCustomerOrders($customerId)
    {
        if (null === $this->_customerOrders) {
            $orders = Mage::getResourceModel('sales/order_collection')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('customer_id', $customerId)
                ->addAttributeToFilter('status', Mage_Sales_Model_Order::STATE_COMPLETE)
                ->addAttributeToFilter(
                    'state',
                    array(
                        'in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()
                    )
                )
                ->load();
            $this->_customerOrders = $orders;
        }

        return $this->_customerOrders;
    }
}

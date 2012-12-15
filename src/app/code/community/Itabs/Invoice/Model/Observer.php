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
 * Observer for payment method availability checks
 *
 * @category  Itabs
 * @package   Itabs_Invoice
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2011-2013 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://github.com/itabs/Itabs_Invoice
 */
class Itabs_Invoice_Model_Observer
{
    /**
     * @var null|Mage_Sales_Model_Resource_Order_Collection
     */
    protected $_customerOrders = null;

    /**
     * paymentMethodIsActive
     *
     * Checks if Invoice is allowed for specific customer groups and if a
     * registered customer has the required minimum amount of orders to be
     * allowed to order via Invoice.
     *
     * @magentoEvent payment_method_is_active
     * @param  Varien_Event_Observer $observer Observer
     * @return void
     */
    public function paymentMethodIsActive($observer)
    {
        $methodInstance = $observer->getEvent()->getMethodInstance();

        // Check if method is Invoice
        if ($methodInstance->getCode() != 'invoice') {
            return;
        }

        // Check if payment method is active
        if (!Mage::getStoreConfigFlag('payment/invoice/active')) {
            return;
        }

        // Get preconditions for checks
        $customerGroupId = Mage_Customer_Model_Group::NOT_LOGGED_IN_ID;
        if (Mage::app()->getStore()->isAdmin()) {
            /* @var $session Mage_Adminhtml_Model_Session_Quote */
            $session = Mage::getSingleton('adminhtml/session_quote');
            /* @var $customer Mage_Customer_Model_Customer */
            $customer = $session->getCustomer();
            $customerGroupId = $session->getQuote()->getCustomerGroupId();
        } else {
            /* @var $session Mage_Customer_Model_Session */
            $session = Mage::getSingleton('customer/session');
            /* @var $customer Mage_Customer_Model_Customer */
            $customer = $session->getCustomer();
            if ($session->isLoggedIn()) {
                $customerGroupId = $session->getCustomerGroupId();
            }
        }

        // Check if payment is allowed only for specific customer groups
        if (!Mage::getStoreConfigFlag('payment/invoice/specificgroup_all')) {
            $allowedGroupIds = explode(',', Mage::getStoreConfig('payment/invoice/specificgroup'));
            if (!in_array($customerGroupId, $allowedGroupIds)) {
                $observer->getEvent()->getResult()->isAvailable = false;
                return;
            }
        }

        // Check minimum orders count
        $minOrderCount = Mage::getStoreConfig('payment/invoice/customer_order_count');
        if ($minOrderCount > 0) {
            $customerId = $customer->getId();
            if (is_null($customerId)) {
                $observer->getEvent()->getResult()->isAvailable = false;
                return;
            }

            $orders = $this->_getCustomerOrders($customerId);
            if (count($orders) < $minOrderCount) {
                $observer->getEvent()->getResult()->isAvailable = false;
                return;
            }
        }

        // Check minimum order amount
        $minOrderSum = Mage::getStoreConfig('payment/invoice/customer_order_amount');
        if ($minOrderSum > 0) {
            $customerId = $session->getCustomerId();
            if (is_null($customerId)) {
                $observer->getEvent()->getResult()->isAvailable = false;
                return;
            }

            $orders = $this->_getCustomerOrders($customerId);
            $orderTotal = 0;
            foreach ($orders as $order) {
                $orderTotal += $order->getData('grand_total');
            }

            if ($orderTotal < $minOrderSum) {
                $observer->getEvent()->getResult()->isAvailable = false;
                return;
            }
        }
    }

    /**
     * Retrieve the order collection of a specific customer
     *
     * @param  int                                        $customerId
     * @return Mage_Sales_Model_Resource_Order_Collection
     */
    protected function _getCustomerOrders($customerId)
    {
        if (is_null($this->_customerOrders)) {
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

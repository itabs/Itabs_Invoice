<?php

class Itabs_Invoice_Test_Block_Info extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var Itabs_Invoice_Block_Info
     */
    protected $_block;

    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_block = self::app()->getLayout()->createBlock('invoice/info');
    }

    /**
     * @test
     */
    public function testBlockInstance()
    {
        $this->assertInstanceOf('Itabs_Invoice_Block_Info', $this->_block);
    }

    /**
     * @test
     * @loadFixture ~Itabs_Invoice/default
     * @loadFixture ~Itabs_Invoice/customer
     * @loadFixture ~Itabs_Invoice/order
     */
    public function getDueDateNoCalculate()
    {
        $order = Mage::getModel('sales/order')->load(1);
        $this->_block->setData('info', $order->getPayment());
        $this->assertFalse($this->_block->getDueDate());
    }

    /**
     * @test
     * @loadFixture ~Itabs_Invoice/default
     * @loadFixture ~Itabs_Invoice/customer
     * @loadFixture ~Itabs_Invoice/order
     * @loadFixture getDueDateNoPaymentDue
     */
    public function getDueDateNoPaymentDue()
    {
        $order = Mage::getModel('sales/order')->load(1);
        $this->_block->setData('info', $order->getPayment());
        $this->assertFalse($this->_block->getDueDate());
    }

    /**
     * @test
     * @loadFixture ~Itabs_Invoice/default
     * @loadFixture ~Itabs_Invoice/customer
     * @loadFixture ~Itabs_Invoice/order
     * @loadFixture getDueDate
     */
    public function getDueDate()
    {
        $order = Mage::getModel('sales/order')->load(1);
        $this->_block->setData('info', $order->getPayment());
        $this->assertEquals('4/15/2014', $this->_block->getDueDate());
    }
}

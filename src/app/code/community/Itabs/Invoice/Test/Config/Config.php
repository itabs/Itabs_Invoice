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
 * @version   1.3.0
 * @link      https://github.com/itabs/Itabs_Invoice
 */
/**
 * Tests for module config
 *
 * @group Itabs_Invoice
 */
class Itabs_Invoice_Test_Config_Config extends EcomDev_PHPUnit_Test_Case_Config
{
    /**
     * @test
     * @loadExpections
     */
    public function globalConfig()
    {
        $this->assertModuleVersion($this->expected('module')->getVersion());
        $this->assertModuleCodePool($this->expected('module')->getCodePool());

        foreach ($this->expected('module')->getDepends() as $depends) {
            $this->assertModuleDepends($depends);
        }
    }
}

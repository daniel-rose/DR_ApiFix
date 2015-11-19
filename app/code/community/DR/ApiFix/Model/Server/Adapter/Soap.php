<?php

/**
 * Webservice soap adapter
 *
 * @category   DR
 * @package    DR_ApiFix
 * @author     Daniel Rose <daniel-rose@gmx.de>
 * @copyright  Copyright (c) 2015 Daniel Rose
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DR_ApiFix_Model_Server_Adapter_Soap extends Mage_Api_Model_Server_Adapter_Soap
{
    /**
     * Set handler class name for webservice
     *
     * @param string $handler
     * @return Mage_Api_Model_Server_Adapter_Soap
     */
    public function setHandler($handler)
    {
        $this->setData('handler', $handler);

        if ($this->wsdlConfig !== null) {
            $this->wsdlConfig->setHandler($this->getHandler());
        }

        return $this;
    }
}

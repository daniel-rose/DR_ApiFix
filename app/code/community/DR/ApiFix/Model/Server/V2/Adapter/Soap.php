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
class DR_ApiFix_Model_Server_V2_Adapter_Soap extends DR_ApiFix_Model_Server_Adapter_Soap
{
    /**
     * Get wsdl config
     *
     * @return Mage_Api_Model_Wsdl_Config
     */
    protected function _getWsdlConfig()
    {
        $wsdlConfig = Mage::getModel('api/wsdl_config');
        $wsdlConfig->setHandler($this->getHandler());

        return $wsdlConfig;
    }

    /**
     * Run webservice
     *
     * @return Mage_Api_Model_Server_Adapter_Soap
     * @throws SoapFault
     * @internal param Mage_Api_Controller_Action $controller
     */
    public function run()
    {
        $apiConfigCharset = Mage::getStoreConfig("api/config/charset");

        if ($this->getController()->getRequest()->getParam('wsdl') !== null) {
            $this->getController()->getResponse()
                ->clearHeaders()
                ->setHeader('Content-Type','text/xml; charset='.$apiConfigCharset)
                ->setBody(
                      preg_replace(
                        '/<\?xml version="([^\"]+)"([^\>]+)>/i',
                        '<?xml version="$1" encoding="'.$apiConfigCharset.'"?>',
                        $this->wsdlConfig->init()->getWsdlContent()
                    )
                );
        } else {
            try {
                $this->_instantiateServer();

                $content = str_replace(
                    '><',
                    ">\n<",
                    preg_replace(
                        '/<\?xml version="([^\"]+)"([^\>]+)>/i',
                        '<?xml version="$1" encoding="' . $apiConfigCharset . '"?>',
                        $this->_soap->handle()
                    )
                );
                $this->getController()->getResponse()
                    ->clearHeaders()
                    ->setHeader('Content-Type', 'text/xml; charset=' . $apiConfigCharset)
                    ->setHeader('Content-Length', strlen($content), true)
                    ->setBody($content);
            } catch( Zend_Soap_Server_Exception $e ) {
                $this->fault( $e->getCode(), $e->getMessage() );
            } catch( Exception $e ) {
                $this->fault( $e->getCode(), $e->getMessage() );
            }
        }

        return $this;
    }
}

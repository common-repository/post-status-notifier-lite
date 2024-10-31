<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: Buffer.php 3137084 2024-08-17 17:27:18Z worschtebrot $
 */
require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Log/Writer/Abstract.php';

class IfwPsn_Zend_Log_Writer_Buffer extends IfwPsn_Vendor_Zend_Log_Writer_Abstract
{
    /**
     * @var string
     */
    protected $_buffer;


    /**
     * Write a message to the log.
     *
     * @param  array $event  log data event
     */
    protected function _write($event)
    {
        if (!empty($this->_buffer)) {
            $this->_buffer .= PHP_EOL;
        }
        $msg = sprintf('%s %s (%s): %s', $event['timestamp'], $event['priorityName'], $event['priority'], $event['message']);
        $this->_buffer .= $msg;
    }

    public function getBuffer()
    {
        return $this->_buffer;
    }

    public function reset()
    {
        $this->_buffer = '';
    }

    /**
     * Construct a IfwPsn_Vendor_Zend_Log driver
     *
     * @param  array|IfwPsn_Vendor_Zend_Config $config
     * @return void
     */
    static public function factory($config)
    {
        // not supported
    }
}

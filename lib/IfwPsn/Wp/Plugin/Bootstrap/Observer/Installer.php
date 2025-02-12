<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) ifeelweb.de
 * @version   $Id: Installer.php 3137084 2024-08-17 17:27:18Z worschtebrot $
 * @package   
 */
require_once dirname(__FILE__) . '/Abstract.php';

class IfwPsn_Wp_Plugin_Bootstrap_Observer_Installer extends IfwPsn_Wp_Plugin_Bootstrap_Observer_Abstract
{
    /**
     * @return string
     */
    public function getId()
    {
        return 'installer';
    }

    protected function _preBootstrap()
    {
        if (!$this->_pm->getAccess()->isHeartbeat() &&
            ($this->_pm->getAccess()->isAdmin() || $this->_pm->getEnv()->isCli())) {

            require_once $this->_pm->getPathinfo()->getRootLib() . '/IfwPsn/Wp/Plugin/Installer.php';
            $this->_resource = IfwPsn_Wp_Plugin_Installer::getInstance($this->_pm);
        }
    }

}

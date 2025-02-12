<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Metabox Abstract
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: Abstract.php 2548435 2021-06-15 17:36:59Z worschtebrot $
 * @package  IfwPsn_Wp_Plugin_Admin_Menu_Metabox
 */
abstract class IfwPsn_Wp_Plugin_Metabox_Abstract
{
    /**
     * @var string
     */
    protected $_id;
    
    /**
     * @var string
     */
    protected $_title;
    
    /**
     * @var string
     */
    protected $_priority;
    
    /**
     * @var IfwPsn_Wp_Plugin_Manager
     */
    protected $_pm;

    protected $_isInit = false;
    
    
    
    /**
     * @param IfwPsn_Wp_Plugin_Manager $pm
     */
    public function __construct(IfwPsn_Wp_Plugin_Manager $pm)
    {
        $this->_pm = $pm;
        
        $this->_init();
    }

    /**
     * 
     */
    protected function _init()
    {
        if (!$this->_isInit) {
            $this->_id = $this->_initId();
            $this->_title = $this->_initTitle();
            $this->_priority = $this->_initPriority();

            $this->enqueueScripts();

            $this->init();
            $this->_isInit = true;
        }
    }
    
    /**
     * May be overwritten by subclasses
     */
    public function init()
    {}
    
    /**
     * Enqueues the required scripts to get the metaboxes working
     * Scripts get only enqueued once by WP internally
     */
    public function enqueueScripts()
    {
        IfwPsn_Wp_Proxy_Script::loadAdmin('common');
        IfwPsn_Wp_Proxy_Script::loadAdmin('wp-lists');
        IfwPsn_Wp_Proxy_Script::loadAdmin('postbox');
    }
    
    /**
     * @return the $_id
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @return the $_title
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * @return the $_priority
     */
    public function getPriority()
    {
        return $this->_priority;
    }

    /**
     * Renders the metabox contents
     * @return mixed
     */
    abstract public function render();

    /**
     * Sets the metabox id
     * @return mixed
     */
    abstract protected function _initId();
    
    /**
     * Sets the metabox title
     * @return string title of the metabox
     */
    abstract protected function _initTitle();

    /**
     * Sets the metabox priority
     * @return mixed
     */
    abstract protected function _initPriority();
}
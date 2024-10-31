<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @version   $Id: Interface.php 2044844 2019-03-05 21:18:19Z worschtebrot $
 * @package   
 */
interface IfwPsn_Wp_Model_Mapper_Interface 
{
    public static function getInstance();
    public function getSingular();
    public function getPlural();
    public function getPerPageId($prefix = '');
    public function getModelName();
}

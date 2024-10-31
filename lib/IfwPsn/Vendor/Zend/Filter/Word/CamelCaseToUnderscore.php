<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    IfwPsn_Vendor_Zend_Filter
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: CamelCaseToUnderscore.php 1312332 2015-12-19 13:29:57Z worschtebrot $
 */

/**
 * @see IfwPsn_Vendor_Zend_Filter_CamelCaseToSeparator
 */
require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Filter/Word/CamelCaseToSeparator.php';

/**
 * @category   Zend
 * @package    IfwPsn_Vendor_Zend_Filter
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IfwPsn_Vendor_Zend_Filter_Word_CamelCaseToUnderscore extends IfwPsn_Vendor_Zend_Filter_Word_CamelCaseToSeparator
{
    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct('_');
    }
}

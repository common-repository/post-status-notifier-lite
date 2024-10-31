<?php
/**
 *
 *
 * @author      Timo Reith <timo@ifeelweb.de>
 * @version     $Id: Rules.php 3137084 2024-08-17 17:27:18Z worschtebrot $
 * @copyright   Copyright (c) ifeelweb.de
 * @package     Psn_Admin
 */
class Psn_Admin_ListTable_Data_Rules extends IfwPsn_Wp_Plugin_ListTable_Data_Abstract
{

    /** (non-PHPdoc)
     * @see IfwPsn_Wp_Plugin_Admin_Menu_ListTable_Data_Interface::getItems()
     */
    public function getItems($limit, $page, $order = null, $where = null)
    {
        $offset = ($page-1) * $limit;
        if (empty($order)) {
            $order = array('name' => 'asc');
        }

        $data = IfwPsn_Wp_ORM_Model::factory('Psn_Model_Rule')->limit($limit)->offset($offset);

        $orderBy = key($order);
        $orderDir = $order[$orderBy];
        if ($orderDir == 'desc') {
            $data->order_by_desc($orderBy);
        } else {
            $data->order_by_asc($orderBy);
        }

        $this->addWhere($data, $where);

        $result = $data->find_array();

        if (Psn_Model_Rule::hasMax()) {
            $result = array_slice($result, 0, Psn_Model_Rule::getMax());
        }

        return $result;
    }

    /** (non-PHPdoc)
     * @see IfwPsn_Wp_Plugin_Admin_Menu_ListTable_Data_Interface::getTotalItems()
     */
    public function getTotalItems($where = null)
    {
        $data = IfwPsn_Wp_ORM_Model::factory('Psn_Model_Rule');

        $this->addWhere($data, $where);

        $result = $data->count();

        if (Psn_Model_Rule::hasMax()) {
            $result = Psn_Model_Rule::getMax();
        }

        return $result;
    }

    protected function addWhere($data, $where = null)
    {
        if (!empty($where)) {
            $where = esc_sql($where);

            // #208
            $format = [];
            foreach (['name', 'notification_subject', 'notification_body', 'to', 'cc', 'bcc', 'exclude_recipients', 'from', 'reply_to'] as $fieldName) {
                $format[] = '`' . $fieldName . '` LIKE "%%%1$s%%"';
            }

            $data->where_raw(sprintf(implode(' OR ', $format),
                $where
            ));
        }
    }

}

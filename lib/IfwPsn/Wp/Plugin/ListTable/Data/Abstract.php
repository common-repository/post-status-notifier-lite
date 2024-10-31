<?php

abstract class IfwPsn_Wp_Plugin_ListTable_Data_Abstract implements IfwPsn_Wp_Plugin_ListTable_Data_Interface
{
    protected $searchCols = ['name'];
    protected $_model;

    public function getTotalItems($where = null)
    {
        $data = IfwPsn_Wp_ORM_Model::factory($this->_model);

        if (method_exists($this, 'addWhere')) {
            $this->addWhere($data, $where);
        }

        return $data->count();
    }

    protected function addWhere($data, $where = null)
    {
        if (!empty($where)) {
            $where = esc_sql($where);

            // #208
            $format = [];
            foreach ($this->searchCols as $fieldName) {
                $format[] = '`' . $fieldName . '` LIKE "%%%1$s%%"';
            }

            $data->where_raw(sprintf(implode(' OR ', $format),
                $where
            ));
        }
    }
}
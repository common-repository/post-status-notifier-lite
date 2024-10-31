<?php
/**
 *
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) 2014 ifeelweb.de
 * @version   $Id: PriorityArray.php 2044844 2019-03-05 21:18:19Z worschtebrot $
 * @package
 */
class IfwPsn_Util_PriorityArray
{
    /**
     * @var array
     */
    private $elements = array();


    /**
     * @param $element
     * @param int $priority
     * @return IfwPsn_Util_PriorityArray
     */
    public function add($element, $priority = 10)
    {
        array_push($this->elements, array(
            'prio' => $priority,
            'el' => $element)
        );
        return $this;
    }

    /**
     * @return array
     */
    public function get()
    {
        $result = array();
        $priority = array();

        foreach ($this->elements as $key => $row) {
            $priority[$key]  = $row['prio'];
        }

        array_multisort($priority, SORT_ASC, $this->elements);

        foreach ($this->elements as $k => $v) {
            array_push($result, $v['el']);
        }

        return $result;
    }
}

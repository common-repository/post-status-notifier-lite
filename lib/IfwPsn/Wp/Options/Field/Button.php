<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 *
 * Options button
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: Button.php 3065031 2024-04-04 20:25:44Z worschtebrot $
 */
require_once dirname(__FILE__) . '/../Field.php';

class IfwPsn_Wp_Options_Field_Button extends IfwPsn_Wp_Options_Field
{
    protected $format = '<a class="button button-primary%s" href="%s" id="%s"%s>%s</a>';



    public function render(array $params)
    {
        /**
         * @var IfwPsn_Wp_Options
         */
        $options = $params[0];

        $id = $options->getOptionRealId($this->_id);
        $name = $options->getPageId() . '['. $id .']';


        $href = $this->_params['href'];
        $text = $this->_params['text'];

        if (isset($this->_params['id'])) {
            $id = $this->_params['id'];
        } else {
            $id = $name;
        }
        if (isset($this->_params['class'])) {
            $extraClass = ' '.$this->_params['class'];
        } else {
            $extraClass = '';
        }

        $extra = array();
        if (!empty($this->_params['data']) && is_array($this->_params['data'])) {
            foreach ($this->_params['data'] as $k => $v) {
                array_push($extra, sprintf('data-%s="%s"', htmlspecialchars($k), htmlspecialchars($v)));
            }
        }

        if (!empty($extra)) {
            $extra = implode(' ', $extra);
            $extra = ' ' . $extra;
        } else {
            $extra = '';
        }

        $output = $this->_getOutputStart($id, 'opt-type-button');

        $output .= sprintf('<p><b class="option-name">%s</b></p>', $this->_label);
        $output .= sprintf($this->format, $extraClass, $href, $id, $extra, $text);

        if (!empty($this->_params['error'])) {
            $output .= '<br><p class="error"> '  . $this->_params['error'] . '</p>';
        }
        if (!empty($this->_description)) {
            $output .= '<br><p class="description"> '  . $this->_description . '</p>';
        }



        $output .= $this->_getOutputEnd();
        echo $output;
    }

//    public function render2(array $params)
//    {
//        /**
//         * @var IfwPsn_Wp_Options
//         */
//        $options = $params[0];
//
//        $id = $options->getOptionRealId($this->_id);
//        $name = $options->getPageId() . '['. $id .']';
//
//        $format = '<a class="button button-primary%s" href="%s" id="%s"%s>%s</a>';
//        $href = $this->_params['href'];
//        $text = $this->_params['text'];
//
//        if (isset($this->_params['id'])) {
//            $id = $this->_params['id'];
//        } else {
//            $id = $name;
//        }
//        if (isset($this->_params['class'])) {
//            $extraClass = ' '.$this->_params['class'];
//        } else {
//            $extraClass = '';
//        }
//
//        $extra = array();
//        if (!empty($this->_params['data']) && is_array($this->_params['data'])) {
//            foreach ($this->_params['data'] as $k => $v) {
//                array_push($extra, sprintf('data-%s="%s"', htmlspecialchars($k), htmlspecialchars($v)));
//            }
//        }
//
//        if (!empty($extra)) {
//            $extra = implode(' ', $extra);
//            $extra = ' ' . $extra;
//        } else {
//            $extra = '';
//        }
//
//        $output = $this->_getOutputStart($id);
//        $output .= sprintf($format, $extraClass, $href, $id, $extra, $text);
//
//        if (!empty($this->_params['error'])) {
//            $output .= '<br><p class="error"> '  . $this->_params['error'] . '</p>';
//        }
//        if (!empty($this->_description)) {
//            $output .= '<br><p class="description"> '  . $this->_description . '</p>';
//        }
//
//
//
//        $output .= $this->_getOutputEnd();
//        echo $output;
//    }
}

<?
include_once(WEB_PATH . 'stock/pfproclass.php');
$GLOBALS['_form_view_class_names'][basename(__FILE__, '.php')] = 'BMWViewThorForm';

class BMWViewThorForm extends LutherDefaultThorForm {

    function on_every_time() {
        parent::on_every_time();
            $herp_element = $this->get_element_name_from_label('Herp');
            $this->change_element_type($herp_element, 'colorpicker');
    }
}
?>
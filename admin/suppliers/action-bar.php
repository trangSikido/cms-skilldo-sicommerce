<?php
Class Suppliers_Admin_Action_Bar {
    static public function registerButton($module) {
        if (Template::isClass('plugins') && InputBuilder::get('page') == 'suppliers') {
            echo '<div class="pull-left">'; do_action('action_bar_suppliers_left', $module); echo '</div>';
            echo '<div class="pull-right">'; do_action('action_bar_suppliers_right', $module); echo '</div>';
        }
    }
    static public function buttonRight($module) {
        switch (InputBuilder::get('view')) {
            case 'edit':
            case 'add':
                echo '<button name="save" class="btn-icon btn-green" form="js_suppliers_form_save">'.Admin::icon('save').' Lưu</button>';
                echo '<a href="'.Url::admin('plugins?page=suppliers').'" class="btn-icon btn-blue">'.Admin::icon('back').' Quay lại</a>';
                break;
            default:
                echo '<a href="'.Url::admin('plugins?page=suppliers&view=add').'" class="btn-icon btn-green">'.Admin::icon('add').' Thêm Mới</a>';
                break;
        }
    }
}
add_action('action_bar_before', 'Suppliers_Admin_Action_Bar::registerButton', 10);
add_action('action_bar_suppliers_right', 'Suppliers_Admin_Action_Bar::buttonRight', 10);
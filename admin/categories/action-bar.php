<?php
Class Product_Category_Admin_Action_Bar {
    static public function registerButton($module) {
        $ci =& get_instance();
        if($ci->template->class == 'products_categories') {
            echo '<div class="pull-left">'; do_action('action_bar_products_categories_left', $module);  echo '</div>';
            echo '<div class="pull-right">'; do_action('action_bar_products_categories_right', $module); echo '</div>';
        }
    }
    static public function buttonRight($module) {
        $btn = action_bar_button( $module );
        if(Template::isPage('products_categories_index')) {
            if( Auth::hasCap('product_cate_edit') ) {
                echo '<a href="'.Url::admin('products/products_categories/add').'" class="btn-icon btn-green">'.Admin::icon('add').' Thêm Mới (F3)</a>';
                echo '<button class="btn-icon btn-green js_products_category_quick_btn">'.Admin::icon('add').'Thêm nhanh (CTRL + F3)</button>';
            }
        }
        if(Template::isPage('products_categories_add')) {
            echo $btn['save'];
            echo $btn['back'];
            echo '<button class="btn-icon btn-green js_products_category_quick_btn">'.Admin::icon('add').'Thêm nhanh (CTRL + F3)</button>';
        }
        if(Template::isPage('products_categories_edit')) { echo $btn['save']; echo $btn['add']; echo $btn['back']; }
    }
}
add_action( 'action_bar_before', 'Product_Category_Admin_Action_Bar::registerButton', 10);
add_action( 'action_bar_products_categories_right', 'Product_Category_Admin_Action_Bar::buttonRight', 10);
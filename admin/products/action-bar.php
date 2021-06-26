<?php
Class Product_Admin_Action_Bar {
    static public function registerButton($module) {
        if(Template::isClass('products')) {
            echo '<div class="pull-left">'; do_action('action_bar_products_left', $module); echo '</div>';
            echo '<div class="pull-right">'; do_action('action_bar_products_right', $module); echo '</div>';
        }
    }
    static public function buttonRight ($module) {

        $btn = action_bar_button( $module );

        $urlBack = Url::admin('/products');

        $urlBack .= '?page='.((!empty(InputBuilder::get('page'))) ?InputBuilder::get('page') : 1);

        if(!empty(InputBuilder::get('category'))) $urlBack .= '&category='.InputBuilder::get('category');

        if(Template::isPage('products_index')) {
            if(InputBuilder::get('status') == 'trash' ) {
                echo $btn['undo'];
                if( Auth::hasCap('product_delete') ) { echo $btn['del']; }
                echo '<a href="'.$urlBack.'" class="btn-icon btn-blue">'.Admin::icon('back').' Quay lại</a>';
                if( Auth::hasCap('product_edit') ) { echo $btn['add']; }
            }
            else {
                if( Auth::hasCap('product_delete') ) { echo $btn['trash']; }
                if( Auth::hasCap('product_edit') ) { echo $btn['add']; }
            }
        }

        if(Template::isPage('products_add')) {
            echo $btn['save'];
            echo '<a href="'.$urlBack.'" class="btn-icon btn-blue">'.Admin::icon('back').' Quay lại</a>';
        }

        if(Template::isPage('products_edit')) {
            echo $btn['save'];
            if( Auth::hasCap('product_edit') ) { echo $btn['add']; }
            echo '<a href="'.$urlBack.'" class="btn-icon btn-blue">'.Admin::icon('back').' Quay lại</a>';
        }
    }
}
add_action('action_bar_before', 'Product_Admin_Action_Bar::registerButton', 10);
add_action('action_bar_products_right', 'Product_Admin_Action_Bar::buttonRight', 10);
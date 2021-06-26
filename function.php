<?php
include 'include/helper-product.php';
include 'include/helper-suppliers.php';
include 'include/helper-brands.php';
include 'include/helper-old-version.php';
if(!function_exists('_price_currency')) {
    /**
     * @Đơn vị tiền tệ
     */
    function _price_currency() {
        return apply_filters('_price_currency', option::get('product_currency'));
    }
}
if(!function_exists('_price_none')) {
    /**
     * @Đơn vị tiền tệ khi giá bằng 0
     */
    function _price_none() {
        return apply_filters('_price_none', __(option::get('product_price_contact'), '_price_none'));
    }
}
if(!function_exists('_form_product_categories')) {
    function _form_product_categories($param, $value = '') {
        $output     = '';
        $options    = ProductCategory::gets( array('mutilevel' => 'option') );
        $options[0] = 'chọn danh mục';
        $output     .= form_dropdown($param->field, $options, set_value($param->field, $value), ' class="form-control '.$param->class.'" id="'.$param->id.'"');
        return $output;
    }
}
if(!function_exists('_form_product_suppliers')) {
    function _form_product_suppliers($param, $value = '') {
        $output     = '';
        $options    = gets_suppliers_option();
        $output     .= form_dropdown($param->field, $options, set_value($param->field, $value), ' class="form-control '.$param->class.'" id="'.$param->id.'"');
        return $output;
    }
}
if(!function_exists('scmc_template')) {
    function scmc_template( $template_path = '' , $args = '', $return = false ) {
        return plugin_get_include(SCMC_NAME,$template_path, $args, $return);
    }
}
if(!function_exists('scmc_include')) {
    function scmc_include( $template_path = '' , $args = '', $return = false) {
        $ci =& get_instance();
        extract($ci->data);
        if (!empty($args) && is_array($args)) extract( $args );
        $path = $ci->plugin->dir.'/'.SCMC_NAME.'/'.$template_path.'.php';
        ob_start();
        include $path;
        if ($return === true) {
            $buffer = ob_get_contents();
            @ob_end_clean();
            ob_end_flush();
            return $buffer;
        }
        ob_end_flush();
    }
}
if(!function_exists('get_controllers_product_index')) {

    function get_controllers_product_index($type = 'get', $slug = '') {

        if($type == 'post') $_GET = InputBuilder::post();

        $url = Url::base(URL_PRODUCT);

        if(empty($slug)) $slug = InputBuilder::get('slug');

        $category           = ProductCategory::get(['where' => array('slug' => $slug)]);

        $status             = (int)InputBuilder::get('status');

        $page               = (int)InputBuilder::get('page');

        $product_pr_page    = (int)option::get('product_pr_page');

        $args   = [];

        $where  = [
            'trash' => 0,
            'public' => 1,
        ];

        $where = apply_filters( 'controllers_product_index_where', $where );

        if(is_array($where) && $status >= 1 && $status <= 3) {
            $where['status'.$status] =  1;
        }

        if( have_posts($category) ) {
            $url = Url::base(Url::permalink($category->slug).'?page={page}');
            $args['where_category'] = $category;
        }

        if($slug == '') {
            $url = Url::base(Url::permalink(URL_PRODUCT).'?page={page}');
            $data_url = InputBuilder::get();
            foreach ($data_url as $key => $value) {
                if($key == 'action' || $key == 'page') { unset($data_url[$key]); continue; }
                if(empty($value)) { unset($data_url[$key]); continue; }
            }
            if(have_posts($data_url)) $url .= '&'. http_build_query($data_url);
        }
        $args['where']          = $where;
        $args 					= apply_filters( 'woocommerce_controllers_index_args', $args );
        $args 					= apply_filters( 'controllers_product_index_args', $args );
        $total_rows 			= apply_filters( 'woocommerce_controllers_index_count', Product::count( $args ));
        $total_rows 			= apply_filters( 'controllers_product_index_count', $total_rows);

        if( $total_rows > 0 ) {
            $config  = array (
                'current_page'  => ($page != 0) ? $page : 1, // Trang hiện tại
                'total_rows'    => $total_rows, // Tổng số record
                'number'		=> $product_pr_page,
                'url'           => $url,
            );
            $pagination = new paging($config);
            $pagination = apply_filters( 'woocommerce_controllers_index_paging', $pagination);
            $pagination = apply_filters( 'controllers_product_index_paging', $pagination );
        }
        else $pagination = '';

        $orderby = 'cle_products.order, cle_products.created desc';

        if(InputBuilder::get('orderby') == 'price-desc') {
            $orderby = '(CASE WHEN `price_sale` = 0 THEN `price` ELSE `price_sale` END) DESC';
        }
        if(InputBuilder::get('orderby') == 'price-asc') {
            $orderby = '(CASE WHEN `price_sale` = 0 THEN `price` ELSE `price_sale` END) ASC';
        }

        if(InputBuilder::get('orderby') == 'best-selling') {
            $orderby = 'cle_products.status2 asc';
        }

        if(is_object($pagination)) {
            $args['params'] = [
                'limit'     => $product_pr_page,
                'start'     => $pagination->getoffset(),
                'orderby'   => $orderby
            ];
        }
        else {
            $args['params'] = ['orderby' => $orderby];
        }

        $args['params'] = apply_filters('woocommerce_controllers_index_params', $args['params']);
        $args['params'] = apply_filters('controllers_product_index_params', $args['params']);

        $objects = apply_filters( 'woocommerce_controllers_index_objects', Product::gets($args),$args);
        $objects = apply_filters( 'controllers_product_index_objects', $objects,$args);

        $result = [];
        $result['pagination']   = $pagination;
        $result['total']    = $total_rows;
        $result['objects']  = $objects;
        $result['category']  = $category;
        return $result;
    }
}
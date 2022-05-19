<?php
include 'include/helper-product.php';
include 'include/helper-suppliers.php';
include 'include/helper-brands.php';
include 'include/helper-old-version.php';
if(!function_exists('productItemStyle')) {
    function productItemStyle($key = '') {
        $style = [
            'border' => ['style' => 'none', 'width' => '0', 'color' => '#fff', 'radius' => '0'],
            'shadow' => ['style' => 'none', 'horizontal' => '0', 'vertical' => '0', 'blur' => '0', 'spread' => '0', 'color' => ''],
            'shadowHover' => ['style' => 'none', 'horizontal' => '0', 'vertical' => '0', 'blur' => '0', 'spread' => '0', 'color' => ''],
            'img' => ['ratio_w' => '1', 'ratio_h' => '1', 'style' => 'cover', 'effect' => 'zoom'],
            'title' => [
                'desktop' => ['show' => '1', 'color' => '#000', 'align' => 'center', 'font' => '','weight' => 'bold', 'size' => '16'],
                'mobile' => ['weight' => 'bold', 'size' => '14'],
            ],
            'price' => [
                'desktop' => ['show' => '1', 'color' => '#fe0000', 'align' => 'center', 'font' => '','weight' => 'bold', 'size' => '16'],
                'mobile' => ['weight' => 'bold', 'size' => '14'],
            ]
        ];

        #border box
        $border = Option::get('productBoxBorder');
        if(isset($border['s'])) $style['border']['style']   = $border['s'];
        if(isset($border['w'])) $style['border']['width']   = (int)$border['w'];
        if(!empty($border['c'])) $style['border']['color']  = $border['c'];
        if(!empty($border['r'])) $style['border']['radius'] = $border['r'];

        #shadow
        $shadow = Option::get('productBoxShadow');
        if(isset($shadow['s'])) $style['shadow']['style']       = $shadow['s'];
        if(isset($shadow['h'])) $style['shadow']['horizontal']  = (int)$shadow['h'];
        if(isset($shadow['v'])) $style['shadow']['vertical']    = (int)$shadow['v'];
        if(isset($shadow['b'])) $style['shadow']['blur']        = (int)$shadow['b'];
        if(isset($shadow['sp'])) $style['shadow']['spread']      = (int)$shadow['sp'];
        if(!empty($shadow['c'])) $style['shadow']['color']      = $shadow['c'];

        #shadow
        $shadow = Option::get('productBoxShadowHover');
        if(isset($shadow['s'])) $style['shadowHover']['style']       = $shadow['s'];
        if(isset($shadow['h'])) $style['shadowHover']['horizontal']  = (int)$shadow['h'];
        if(isset($shadow['v'])) $style['shadowHover']['vertical']    = (int)$shadow['v'];
        if(isset($shadow['b'])) $style['shadowHover']['blur']        = (int)$shadow['b'];
        if(isset($shadow['sp'])) $style['shadowHover']['spread']     = (int)$shadow['sp'];
        if(!empty($shadow['c'])) $style['shadowHover']['color']      = $shadow['c'];

        #img
        $img = Option::get('productImg');
        if(isset($img['ratio_w']))   $style['img']['ratio_w'] = $img['ratio_w'];
        if(isset($img['ratio_h']))  $style['img']['ratio_h']  = $img['ratio_h'];
        if(isset($img['s']))   $style['img']['style']         = $img['s'];
        if(isset($img['e'])) $style['img']['effect']          = $img['e'];

        #title product
        $title = Option::get('productTitle');
        if(isset($title['show']))   $style['title']['desktop']['show']      = $title['show'];
        if(isset($title['align']))  $style['title']['desktop']['align']     = $title['align'];
        if(isset($title['font']))   $style['title']['desktop']['font']      = $title['font'];
        if(isset($title['weight'])) $style['title']['desktop']['weight']    = $title['weight'];
        if(isset($title['size']))   $style['title']['desktop']['size']      = $title['size'];
        $title = Option::get('productTitleMobile');
        if(isset($title['weight'])) $style['title']['mobile']['weight']    = $title['weight'];
        if(isset($title['size']))   $style['title']['mobile']['size']      = $title['size'];

        #price
        $price = Option::get('productPrice');
        if(isset($price['show']))   $style['price']['desktop']['show']      = $price['show'];
        if(isset($price['align']))  $style['price']['desktop']['align']     = $price['align'];
        if(isset($price['font']))   $style['price']['desktop']['font']      = $price['font'];
        if(isset($price['weight'])) $style['price']['desktop']['weight']    = $price['weight'];
        if(isset($price['size']))   $style['price']['desktop']['size']      = $price['size'];
        $price = Option::get('productPriceMobile');
        if(isset($price['weight'])) $style['price']['mobile']['weight']    = $price['weight'];
        if(isset($price['size']))   $style['price']['mobile']['size']      = $price['size'];

        if(empty($key)) return $style;

        return Arr::get($style, $key);
    }
}
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

        $status             = (int)InputBuilder::get('status');

        $page               = (int)InputBuilder::get('page');

        $product_pr_page    = (int)option::get('product_pr_page');

        $args       = [];

        $brand      = [];

        $supplier   = [];

        $where      = ['trash' => 0, 'public' => 1];

        $category  = ProductCategory::get(['where' => array('slug' => $slug)]);

        if(!have_posts($category)) {

            $brand  = Brands::get(['where' => ['slug' => $slug]]);

            if(!have_posts($brand)) {
                $supplier = Suppliers::get(['where' => ['slug' => $slug]]);
            }
        }

        if(is_array($where) && $status >= 1 && $status <= 3) {
            $where['status'.$status] =  1;
        }

        if(have_posts($category)) {
            $url = Url::base(Url::permalink($category->slug).'?page={page}');
            $args['where_category'] = $category;
        }

        if(have_posts($brand)) {
            $url = Url::base(Url::permalink($brand->slug).'?page={page}');
            $where['brand_id'] = $brand->id;
        }

        if(have_posts($supplier)) {
            $url = Url::base(Url::permalink($supplier->slug).'?page={page}');
            $where['supplier_id'] = $supplier->id;
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

        $where = apply_filters('controllers_product_index_where', $where);

        $args['where']          = $where;

        $args 					= apply_filters('controllers_product_index_args', $args);

        $total_rows 			= apply_filters('controllers_product_index_count', Product::count($args));

        if( $total_rows > 0 ) {
            $config  = array (
                'current_page'  => ($page != 0) ? $page : 1, // Trang hiện tại
                'total_rows'    => $total_rows, // Tổng số record
                'number'		=> $product_pr_page,
                'url'           => $url,
            );
            $pagination = new paging($config);
            $pagination = apply_filters( 'controllers_product_index_paging', $pagination );
        }
        else $pagination = '';

        $orderby = 'cle_products.order, cle_products.created desc';

        $orderType = InputBuilder::get('orderby');

        if($orderType == 'price-desc') {
            $orderby = '(CASE WHEN `price_sale` = 0 THEN `price` ELSE `price_sale` END) DESC';
        }
        if($orderType == 'price-asc') {
            $orderby = '(CASE WHEN `price_sale` = 0 THEN `price` ELSE `price_sale` END) ASC';
        }
        if($orderType == 'best-selling') {
            $orderby = 'cle_products.status2 desc';
        }
        if($orderType == 'hot') {
            $orderby = 'cle_products.status3 desc';
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

        $args['params'] = apply_filters('controllers_product_index_params', $args['params']);

        $objects = apply_filters( 'controllers_product_index_objects', Product::gets($args),$args);

        $result = [];
        $result['pagination'] = $pagination;
        $result['total']      = $total_rows;
        $result['objects']    = $objects;
        $result['category']   = $category;
        $result['brand']      = $brand;
        $result['supplier']   = $supplier;
        return $result;
    }
}
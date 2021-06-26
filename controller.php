<?php
/** PRODUCT-INDEX ******************************************************************/
if (!function_exists('controllers_product_index')) {
	/**
	 * @Load thư viện cần cho slider hình ảnh trong trang chi tiết sản phẩm
	 */
	function controllers_product_index( $slug = '' ) {

	    $ci =&get_instance();

		$data = get_controllers_product_index('get', $slug);

		$ci->data['slug']       = $slug;

        $ci->data['category']   = $data['category'];

        $ci->data['pagination'] = $data['pagination'];

		$ci->data['objects']    = $data['objects'];
	}

	add_action('controllers_products_index', 'controllers_product_index', 10, 1);
}
/** PRODUCT-BRANDS ***************************************************************/
if (!function_exists('brands_frontend')) {
    function brands_frontend( $ci, $model ) {
        $slug = Url::segment('1');
        if( Language::hasMulti() && (Language::default() != Language::current() || $slug ==  Language::default()) && $slug == Language::current()) {
            $slug = Url::segment('2');
        }
        $slug = Str::clear($slug);
        $brands = Brands::get(['where' => ['slug' => $slug]]);
        $ci->data['objects'] = [];
        if(have_posts($brands)) {
            $url 	= Url::base($slug);
            $args 		= apply_filters('controllers_brands_args', ['where' => ['public' => 1, 'trash' => 0, 'brand_id' => $brands->id]]);
            $total_rows = apply_filters('controllers_brands_count', Product::count($args));
            $product_pr_page = (int)option::get('product_pr_page');
            if($total_rows > 0) {
                $ci->data['pagination'] = apply_filters('controllers_brands_paging', pagination($total_rows, $url, $product_pr_page));
            }
            else {
                $ci->data['pagination'] = '';
            }
            if(isset($ci->data['pagination']) && is_object($ci->data['pagination'])) {

                $args['params']    = array('limit' => $product_pr_page, 'start' => $ci->data['pagination']->getoffset(),'orderby' => 'products.order, products.created desc');
            }
            else {

                $args['params']     = array('orderby' => 'products.order, products.created desc');
            }
            $args['params'] 		= apply_filters('controllers_brands_params', $args['params']);
            $ci->data['objects']    = apply_filters('controllers_brands_objects', Product::gets($args), $args);
        }
        $ci->data['brands'] = $brands;
        $layout = apply_filters( 'controllers_brands_layout', 'template-full-width', 10);
        $view 	= apply_filters( 'controllers_brands_view', 'products-index', 10);
        $ci->template->set_layout($layout);
        $ci->template->set_view($view);
        $ci->template->render();
    }
}
/** PRODUCT-SUPPLIERS ***************************************************************/
if(!function_exists('suppliers_frontend')) {
	/**
	 * @Load thư viện cần cho slider hình ảnh trong trang chi tiết sản phẩm
	 */
	function suppliers_frontend( $ci, $model ) {

		$lang = $ci->uri->segment('1');

		if( ($ci->language['default'] != $ci->language['current'] || $ci->uri->segment('1') ==  $ci->language['default']) && $lang == $ci->language['current'] ) {
			
			$slug = $ci->uri->segment('2');
		}
		else {
			
			$slug = $ci->uri->segment('1');
		}

		$slug = Str::clear($slug);

		$suppliers = get_suppliers(['where' => array('slug' => $slug)]);

		$ci->data['objects'] = [];

		if(have_posts($suppliers)) {

			$model->settable('products');

			$url 	= base_url().$slug;

			$where 		= array('public' => 1, 'trash' => 0, 'supplier_id' => $suppliers->id);

			$where 		= apply_filters( 'controllers_suppliers_where', $where );

			$args 		= array('where' => $where);

			$args 		= apply_filters( 'controllers_suppliers_args', $args );

			$total_rows = apply_filters( 'controllers_suppliers_count', Product::count($args));

			$product_pr_page = (int)option::get('product_pr_page');

			if( $total_rows > 0 ) {

				$ci->data['pagination'] = apply_filters( 'controllers_suppliers_paging', pagination($total_rows, $url, $product_pr_page ) );
			}
			else {
				
				$ci->data['pagination'] = '';
			}

			if(isset($ci->data['pagination']) && is_object($ci->data['pagination'])) {

				$args['params']    = array('limit' => $product_pr_page, 'start' => $ci->data['pagination']->getoffset(),'orderby' => 'products.order, products.created desc');
			}
			else {
				
				$args['params']     = array('orderby' => 'products.order, products.created desc');
			}

			$args['params'] 		= apply_filters('controllers_suppliers_params', $args['params']);

			$ci->data['objects']    = apply_filters('controllers_suppliers_objects', Product::gets($args), $args);
		}

		$ci->data['suppliers'] = $suppliers;

		$layout = apply_filters( 'controllers_suppliers_layout', 'template-full-width');

		$view 	= apply_filters( 'controllers_suppliers_view', 'products-index');

		$ci->template->set_layout($layout);

		$ci->template->set_view($view);

		$ci->template->render();
	}
}
/** PRODUCT-DETAIL ******************************************************************/
if(!function_exists('controllers_product_detail')) {
	/**
	 * @Lấy data trang chi tiết sản phẩm
	 */
	function controllers_product_detail( $slug = '' ) {
		$ci =& get_instance();
		$model = get_model('products')->settable('products');
		/* Get sản phẩm */
		$args = [
			'where' => ['slug' => $slug, 'public' => 1, 'trash' => 0],
		];
		$ci->data['object'] 	= Product::get($args);
		if( have_posts( $ci->data['object']) ) {
			/* Get danh sách categories của sản phẩm */
			$ci->data['categories'] 	= $model->gets_relationship_join_categories($ci->data['object']->id, 'products', 'products_categories');
			if(have_posts($ci->data['categories'])) {
				$ci->data['category']  	= ProductCategory::get( $ci->data['categories'][0]->id );
			}
		}
	}

	add_action('controllers_products_detail', 'controllers_product_detail', 10, 1);
}
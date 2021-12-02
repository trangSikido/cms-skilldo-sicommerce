<?php
/** PRODUCT-INDEX ******************************************************************/
if (!function_exists('controllers_product_index')) {
	/**
	 * @Load thư viện cần cho slider hình ảnh trong trang chi tiết sản phẩm
	 */
	function controllers_product_index($slug = '') {

	    $ci =&get_instance();

		$data = call_user_func(apply_filters('controllers_product_index_data', 'get_controllers_product_index'), 'get',$slug);

		$ci->data['slug']       = $slug;

        $ci->data['category']   = $data['category'];

        $ci->data['pagination'] = $data['pagination'];

		$ci->data['objects']    = $data['objects'];

        $ci->data['brand']      = $data['brand'];

        $ci->data['supplier']   = $data['supplier'];
	}

	add_action('controllers_products_index', 'controllers_product_index', 10, 1);
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
		$args = ['where' => ['slug' => $slug, 'public' => 1, 'trash' => 0]];
		$ci->data['object'] = Product::get($args);
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
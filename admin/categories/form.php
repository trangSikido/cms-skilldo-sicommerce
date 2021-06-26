<?php
function product_category_admin_form_input($form) {

    $form['param']['parent'] 		= true;

    $form['param']['slug'] 			= 'name';

    $form['param']['redirect'] 	    = Url::admin('products/products_categories');

    $redirect = '';

    if((int)InputBuilder::get('category') != 0) $redirect .= '?category='.InputBuilder::get('category');

    $form['param']['redirect'] 	.= $redirect;

    $form['right']['category'] =  'Danh mục';

    foreach (Language::listKey() as $key) {
        if($key == Language::default()) $rules = 'trim|required'; else $rules = 'trim';
        $param = array('group' => 'info', 'lang'=> $key, 	'field' => $key.'[name]', 'label' => 'Tiêu đề', 'type'	=> 'text', 'note' 	=> 'Tiêu đề bài viết được lấy làm thẻ H1', 'rules' => $rules);
        $form['field'] = form_add_field( $form['field'], $param, 'excerpt');
    }

    $form['field']['parent_id'] =  array('group' => 'category', 'field' => 'parent_id', 	'label' => 'Danh mục cha', 'value'=>InputBuilder::get('category'), 'type' => 'select', 'rules' => 'trim');

    $form['right'] 				= form_add_group($form['right'], 'category', 'Danh mục', 'media');

    $form = form_remove_field('title', $form);

    if(Template::isMethod('index')) {

        $form = form_remove_group('seo,theme', $form);

        $form = form_remove_field('excerpt,content', $form);
    }

    $remove_group = 'media,theme';

    $remove_field = 'content,excerpt';

    template_support_action($remove_group, $remove_field, $form, 'products_categories');

    return $form;
}
add_filter('manage_products_categories_input', 'product_category_admin_form_input', 1);
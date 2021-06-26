<?php
$FormBuilder = new FormBuilder();

$product_selling_setting = option::get('product_selling');

if(empty($product_selling_setting['position']) || $product_selling_setting['position'] != 'disabled') {
    if(empty($product_selling_setting['type']) || $product_selling_setting['type'] != 'auto') {
        $FormBuilder->add('product_selling', 'popover', [
            'label' => 'Sản phẩm bán chạy',
            'module' => 'products',
            'key_type' => 'products',
            'multiple' => true,
            'image' => true,
        ], $product_selling);
    }
}

$FormBuilder->add('product_related', 'popover', [
    'label'     => 'Sản phẩm liên quan',
    'module'    => 'products',
    'key_type'  => 'products',
    'multiple'  => true,
    'image'     => true,
    'note'      => 'Nếu không chọn sản phẩm liên quan sẽ được lấy từ sản phẩm cùng danh mục.',
], $product_related);

$FormBuilder->add('post_related', 'popover', [
    'label'     => 'Tin tức liên quan',
    'module'    => 'post',
    'key_type'  => 'post',
    'multiple'  => true,
    'image'     => true,
], $post_related);

$FormBuilder = apply_filters('admin_product_field_related', $FormBuilder, $object);

$FormBuilder->html(false);
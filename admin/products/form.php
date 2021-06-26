<?php
function product_admin_form_input($form) {
    $ci =& get_instance();
    FormBuilder::updateParams('slug', 'title');
    $urlParam = '?page='.((InputBuilder::get('page') != '')?InputBuilder::get('page'):1);
    if(!empty(InputBuilder::get('category'))) $urlParam .= '&category='.InputBuilder::get('category');
    FormBuilder::updateParams('redirect', Url::admin('products'.$urlParam));
    FormBuilder::addGroup('right', 'category', 'Phân loại', 'media');
    FormBuilder::addField('category', 'code', ['label' => 'Mã sản phẩm', 'type' => 'text', 'note' => 'Nhập mã sản phẩm (SKU) nếu có.']);
    FormBuilder::addField('category', 'category_id', ['label' => 'Danh mục', 'type' => 'popover', 'module' => 'products_categories']);
    if(option::get('product_supplier') == 1 && version_compare( cms_info('version'), '3.0.0') >= 0 && version_compare( option::get('wcmc_database_version'), '1.3') >= 0 ) {
        $supplier_options = Suppliers::getsOption();
        FormBuilder::addField('category', 'supplier_id', ['label' => 'Nhà sản xuất', 'type' => 'popover', 'module' => 'supplier', 'multiple' => false, 'options' => $supplier_options]);
        unset($supplier_options[0]);
        if(empty($supplier_options)) FormBuilder::removeField('supplier_id');
    }
    if(option::get('product_brands') == 1) {
        $brand_options = Brands::getsOption();
        FormBuilder::addField('category', 'brand_id', ['label' => 'Thương hiệu', 'type' => 'popover', 'module' => 'brands', 'multiple' => false, 'options' => $brand_options]);
        unset($brand_options[0]);
        if(empty($brand_options)) FormBuilder::removeField('brand_id');
    }
    foreach ($ci->taxonomy['list_cat_detail'] as $taxonomy_key => $taxonomy_value) {
        if( $taxonomy_value['post_type'] == 'products') {
            FormBuilder::addGroup('right', 'taxonomies', 'Chuyên Mục', 'media');
            FormBuilder::addField('taxonomies', 'taxonomy_'.$taxonomy_key, ['label' => $taxonomy_value['labels']['name'], 'type' => 'popover', 'module' => 'post_categories']);
        }
    }
    FormBuilder::addGroup('right', 'price', 'Giá', 'media');
    FormBuilder::addField('price', 'price', ['value' => 0, 'label' => 'Giá', 'type' => 'text', 'rules' => 'required'], 0);
    FormBuilder::addField('price', 'price_sale', ['value' => 0, 'label' => 'Giá khuyến mãi', 'type' => 'text', 'rules' => 'required'], 0);
    FormBuilder::addField('price', 'weight', ['value' => 0, 'label' => 'Cân nặng', 'type' => 'number', 'note' => 'Đơn vị tính bằng gram', 'rules' => 'required'], 0);
    $remove_group = 'theme';
    template_support_action( $remove_group, '', $ci->data['form'], 'products');
    return $ci->data['form'];
}
add_filter('manage_products_input', 'product_admin_form_input', 1);
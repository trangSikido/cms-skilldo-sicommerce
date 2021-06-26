<?php
function brands_admin_menu($list_object) {
    $list_object['brand'] = array ( 'label' => 'Thương hiệu', 'type' => 'brands', 'data' => array());
    $list_object['brand']['data'] = [];
    $data = Brands::gets();
    if(have_posts($data)) {
        foreach ($data as $key => $datum) {
            if(!isset($datum->id)) continue;
            $list_object['brand']['data'][$datum->id] = (object)array('id' => $datum->id, 'name' => $datum->name);
        }
    }
    return $list_object;
}
if(!empty(Option::get('product_brands'))) {
    add_filter('admin_menu_list_object',  'brands_admin_menu', 10);
}
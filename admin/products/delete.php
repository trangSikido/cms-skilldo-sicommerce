<?php
//Xóa sản phẩm
function product_admin_action_delete($res, $table, $id) {
    if(is_numeric($id)) {
        $res = Product::delete($id);
    }
    else if(have_posts($id)) {
        $res = Product::deleteList($id);
    }
    return $res;
}
add_filter('delete_object_products', 'product_admin_action_delete', 1, 3 );
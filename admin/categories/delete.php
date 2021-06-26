<?php
function product_category_admin_action_delete($res, $table, $id) {
    if(is_numeric($id)) {
        $res = ProductCategory::delete($id);
    }
    else if(have_posts($id)) {
        $res = ProductCategory::deleteList($id);
    }
    return $res;
}
add_filter('delete_object_products_categories', 'product_category_admin_action_delete', 1, 3 );

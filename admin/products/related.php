<?php
Class Product_Admin_Related {
    static public function formMeta($object) {
        $product_selling =  (have_posts($object)) ? Product::getMeta($object->id, 'product_selling', true) : [];
        $product_related =  (have_posts($object)) ? Product::getMeta($object->id, 'product_related', true) : [];
        $post_related =  (have_posts($object)) ? Product::getMeta($object->id, 'post_related', true) : [];
        include SCMC_PATH.'admin/views/related/html-related.php';
    }
    static public function save($product_id, $module) {
        if($module == 'products') {
            $product_selling = InputBuilder::Post('product_selling', ['type' => 'int']);
            if(is_string($product_selling)) $product_selling = [];
            Product::updateMeta($product_id, 'product_selling', $product_selling);

            $product_related = InputBuilder::Post('product_related', ['type' => 'int']);
            if(is_string($product_related)) $product_related = [];
            Product::updateMeta($product_id, 'product_related', $product_related);

            $post_related = InputBuilder::Post('post_related', ['type' => 'int']);
            if(is_string($post_related)) $post_related = [];
            Product::updateMeta($product_id, 'post_related', $post_related);
        }
    }
}
Metabox::add('admin_product_metabox_related', 'Sản Phẩm liên quan', 'Product_Admin_Related::formMeta', ['module' => 'products']);
add_action('save_object', 'Product_Admin_Related::save', 10, 2);
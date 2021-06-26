<?php
Class Sicommerce_Cache {

    function __construct() {
        add_filter('theme_breadcrumb_id',    [$this, 'registerBreadcrumbId'], 10, 2);
        add_filter('theme_breadcrumb_data',    [$this, 'breadcrumbData'], 10, 2);
        if(Admin::is()) {
            //xóa cache khi xóa danh mục
            add_action('ajax_delete_before_success',    [$this, 'deleteCache'], 10, 2);
            add_action('ajax_delete_before_success',    [$this, 'deleteCacheBreadcrumb'], 10, 2);
            //xóa cache khi up hiển thị
            add_action('up_boolean_success',            [$this, 'deleteCache'], 10, 2);
            //xóa cache khi up thứ tự
            add_action('up_table_success',              [$this, 'deleteCache'], 10, 2);
            //xóa cache khi lưu
            add_action('save_object', [$this, 'deleteCacheSave'], 10, 2);

            //Cache Editor manager
            add_filter('cache_manager_object', [$this, 'registerCacheManager'], 1);
        }
    }

    public function registerBreadcrumbId($id, $page) {
        if( $page == 'products_index') {
            $category = get_object_current('category');
            if(have_posts($category)) $id = $category->id;
        }
        if( $page == 'products_detail') {
            $object = get_object_current('object');
            if(have_posts($object)) $id = $object->id;
        }
        return $id;
    }

    public function breadcrumbData($breadcrumb, $page) {
        if($page == 'products_index' || $page == 'products_detail') {
            $temp[] = (object)[
                'name' => __('Sản phẩm', 'theme_san_pham'),
                'slug' => 'san-pham'
            ];
            foreach ($breadcrumb as $key => $value) {
                $temp[] = $value;
            }
            $breadcrumb = $temp;
        }

        return $breadcrumb;
    }

    public function deleteCache($module, $id) {
        if( $module == 'products_categories') {
            CacheHandler::delete( 'products_', true );
        }
        if( $module == 'products') {
            CacheHandler::delete( 'products_', true );
        }
    }

    public function deleteCacheBreadcrumb($module, $id) {
        if( $module == 'products_categories') {
            if(is_array($id)) {
                foreach ($id as $item) {
                    CacheHandler::delete( 'breadcrumb_products_index_'.$item, true);
                }
            }
            else CacheHandler::delete( 'breadcrumb_products_index_'.$id, true);
            CacheHandler::delete('breadcrumb_products_detail_', true);
        }
        if( $module == 'products') {
            if(is_array($id)) {
                foreach ($id as $item) {
                    CacheHandler::delete( 'breadcrumb_products_detail_'.$item, true);
                }
            }
            else CacheHandler::delete( 'breadcrumb_products_detail_'.$id, true);
        }
    }

    public function deleteCacheSave($id, $module) {
        if($module == 'products_categories' || $module == 'products') {
            $this->deleteCache($module, $id);
            $this->deleteCacheBreadcrumb($module, $id);
        }
    }

    function registerCacheManager( $cache ) {
        $cache['product_category'] = array(
            'label'     => 'Clear product category: Xóa dữ liệu cache danh mục sản phẩm.',
            'btnlabel'  => 'Xóa cache product category',
            'color'     => 'green',
            'callback'  => 'product_category_cache_manager'
        );
        return $cache;
    }
}

if(!function_exists('product_category_cache_manager')) {
    function product_category_cache_manager() {
        CacheHandler::delete('products_categories_', true);
        CacheHandler::delete('products_category_', true);
        CacheHandler::delete('breadcrumb_products_index_', true);
    }
}
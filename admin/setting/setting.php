<?php
include_once 'ajax.php';
include_once 'setting-object.php';
include_once 'setting-index.php';
include_once 'setting-detail.php';
Class Admin_Product_Setting {
    function __construct() {
        new Product_Admin_Setting_Object();
        new Product_Admin_Setting_Index();
        new Product_Admin_Setting_Detail();
    }
    public static function tabs() {
        $setting_tabs = [
            'general' 	=> array( 'label' => 'Chung', 	 'callback' => 'Admin_Product_Setting::pageSettingGeneral', 'icon' => '<i class="fab fa-elementor"></i>'),
            'product' 	=> array( 'label' => 'Sản phẩm', 'callback' => 'Admin_Product_Setting::pageSettingProduct', 'icon' => '<i class="fal fa-box-full"></i>'),
        ];
        return apply_filters('admin_product_settings_tabs', $setting_tabs);
    }
    public static function tabProductSub() {
        $tabs['object'] 	= array( 'label' => 'Sản phẩm', 		'callback' 	=> 'Admin_Product_Setting::pageSettingSubProduct');
        $tabs['index'] 		= array( 'label' => 'Trang danh mục', 	'callback' 	=> 'Admin_Product_Setting::pageSettingSubProduct');
        $tabs['detail'] 	= array( 'label' => 'Trang chi tiết', 	'callback' 	=> 'Admin_Product_Setting::pageSettingSubProduct');
        /**
         * @since 2.2.0
         */
        return apply_filters('admin_product_settings_tabs_product_sub', $tabs);
    }
    public static function pageSetting($ci, $model) {
        $views = InputBuilder::get('view');
        $tab   = (int)InputBuilder::get('tab');
        scmc_include('admin/views/settings/html-settings');
    }
    public static function pageSettingGeneral($ci, $tab) {
        if(InputBuilder::post()) {
            $options['product_currency'] 		= InputBuilder::post('product_currency');
            $options['product_price_contact'] 	= InputBuilder::post('product_price_contact');
            foreach ($options as $key => $value) {
                option::update( $key, $value );
            }
        }
        scmc_include('admin/views/settings/html-settings-tab-general');
    }
    public static function pageSettingProduct($ci, $tab) {
        scmc_include('admin/views/settings/html-settings-tab-product');
    }
    public static function pageSettingSubProduct($ci, $section) {
        scmc_include('admin/views/settings/html-settings-tab-product-'.$section);
    }
}

new Admin_Product_Setting();
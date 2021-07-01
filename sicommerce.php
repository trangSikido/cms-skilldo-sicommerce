<?php
/**
Plugin name     : sicommerce
Plugin class    : sicommerce
Plugin uri      : http://sikido.vn
Description     : Tạo và quản lý sản phẩm thương mail của bạn.
Author          : Nguyễn Hữu Trọng
Version         : 3.4.0
*/
define('SCMC_NAME', 'sicommerce');

define('SCMC_URL', URL_ADMIN.'/plugins?page='.SCMC_NAME.'&view=' );

define('SCMC_PATH', Path::plugin(SCMC_NAME).'/');

define('WCMC_PATH', SCMC_PATH);

define('SCMC_VERSION', '3.4.0');

define('SCMC_TEMPLATE', '2.1.0');

class woocommerce {}

class sicommerce {

    private $name = 'sicommerce';

    function __construct() {
        $this->loadDependencies();
        if(!Admin::is() && Admin::isRoot()) {
            new Sicommerce_List_Hook();
        }
        if(Admin::is()) {
            new Brands_Admin();
        }
        new Sicommerce_Cache();
    }

    public function active() {
        $module  = [
            'products.php'                      => SCMC_PATH.'admin/module/products.php',
            'products-categories.php'           => SCMC_PATH.'admin/module/products-categories.php',
        ];
        foreach ($module as $file_name => $file_path) {
            $file_new  = FCPATH.APPPATH.'modules/backend_products/controllers/'.$file_name;
            $file_path = FCPATH.$file_path;
            if(file_exists($file_new)) unlink($file_new);
            if(file_exists($file_path)) {
                $handle     = file_get_contents($file_path);
                $file_new   = fopen($file_new, "w");
                fwrite($file_new, $handle);
                fclose($file_new);
            }
        }
        scmc_database_add_table();
        $options = $this->get_options();
        foreach ($options as $option_key => $option_value) {
            Option::add($option_key, $option_value);
        }
    }

    public function uninstall() {
        scmc_database_drop_table();
        /**
         * XÓA OPTION CẤU HÌNH
         */
        $options = $this->get_options();
        
        foreach ( $options as $option_key => $option_value ) {
            Option::delete( $option_key );
        }
    }

    public function get_options() {
        
        $option = array(
            'product_brands'        => 0, // version 2.1.0
            'product_supplier'      => 0, // version 2.0.5
            'product_currency'      => 'đ',
            'product_price_contact' => __('Liên hệ', 'lien-he'),
            'product_pr_page'           => 16,
            'category_row_count'        => 4,
            'category_row_count_tablet' => 3,
            'category_row_count_mobile' => 2,
            'product_title_color'       => '',
            'product_hiden_title'       => 1,
            'product_hiden_price'       => 1,
            'product_hiden_description' => 0,
            'product_price_color'       => 0, // version 1.8.3
            'product_version'           => SCMC_VERSION, // version 2.0.5
            'product_fulltext_search'   => false, // version 3.4.0
        );

        return $option;
    }

    private function loadDependencies() {
        require_once SCMC_PATH.'hook.php';
        require_once SCMC_PATH.'function.php';
        require_once SCMC_PATH.'ajax.php';
        require_once SCMC_PATH.'cache.php';
        require_once SCMC_PATH.'sidebar.php';
        if(Admin::is()) {
            require_once SCMC_PATH.'update.php';
            require_once SCMC_PATH.'admin.php';
        }
        else {
            require_once SCMC_PATH.'controller.php';
        }
        require_once SCMC_PATH.'template.php';
    }

    static public function url($key) {
        $url = [
            'setting'   => 'plugins?page=product_settings',
        ];
        return (!empty($url[$key])) ? $url[$key] : '';
    }

    static public function config($key = '') {

        if(empty($key)) return '';

        if(Str::is('product_content.*', $key)) {

            $config = Option::get('product_content');

            if(!have_posts($config)) $config = [];

            if(empty($config['category'])) {
                $config['category'] = ['enable' => false];
            }

            if(empty($config['content_top'])) {
                $config['content_top'] = ['enable' => false];
            }

            if(empty($config['content_bottom'])) {
                $config['content_bottom'] = ['enable' => false];
            }

            $key = str_replace('product_content.', '', $key);

            return Arr::get($config, $key);
        }

        if(Str::is('product_sidebar.*', $key)) {

            $config = Option::get('product_sidebar');

            if(!have_posts($config)) $config = [];

            if(empty($config['category'])) {
                $config['category'] = [ 'title' => 'Sản phẩm bán chạy', 'enable' => false, 'order'  => 10];
            }

            if(empty($config['selling'])) {
                $config['selling'] = [ 'title' => 'Sản phẩm bán chạy', 'enable' => false, 'order'  => 20];
            }

            if(empty($config['hot'])) {
                $config['hot'] = [ 'title' => 'Sản phẩm nổi bật', 'enable' => false, 'order'  => 30];
            }

            if(empty($config['sale'])) {
                $config['sale'] = [ 'title' => 'Sản phẩm khuyến mãi', 'enable' => false, 'order'  => 40];
            }

            $key = str_replace('product_sidebar.', '', $key);

            return Arr::get($config, $key);
        }

        return '';
    }
}

new sicommerce();




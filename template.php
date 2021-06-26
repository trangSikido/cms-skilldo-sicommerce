<?php
include 'include/template-index.php';
include 'include/template-detail.php';
include 'include/template-search.php';
include 'include/template-object.php';
if (!function_exists('product_assets')) {
    function product_assets() {
        Template::asset()->location('header')->add('product-detail', SCMC_PATH.'assets/css/scmc-style.css', ['minify' => true, 'path'  => ['image' => SCMC_PATH.'assets/images']]);
        Template::asset()->location('footer')->add('product-detail', SCMC_PATH.'assets/js/scmc-script.js', ['minify' => true]);
        Template::asset()->location('footer')->add('elevatezoom', SCMC_PATH.'assets/add-on/elevatezoom-master/jquery.elevateZoom-3.0.8.min.js', ['page' => ['products_detail']]);
    }
    add_action('init','product_assets', 30);
}
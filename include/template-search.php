<?php
Class Product_Page_Search {
    static public function searchData($objects, $type, $keyword) {

        $ci =& get_instance();

        if( $type == 'products' ) {

            $args = [
                'where' 	 => ['public' => 1, 'trash' => 0],
                'where_like' => ['title' => [$keyword]],
            ];

            if(InputBuilder::get('category') != null && InputBuilder::get('category') != 0) {

                $category = Str::clear(InputBuilder::get('category'));

                $args['where_category'] = $category;

                $args['join'] = true;
            }

            $args = apply_filters('product_search_args', $args);

            $objects = apply_filters('product_search_data', Product::gets($args));

            $ci->template->set_layout('template-full-width');
        }

        return $objects;
    }
    static public function searchHtml($objects, $type, $keyword) {
        if( $type == 'products' ) {
            if(have_posts($objects)) {
                if(is_object($objects)) {
                    $objects = array('objects' => array($objects) );
                }
                else {
                    $objects = array('objects' => $objects );
                }
            }
            scmc_template( 'product_search', $objects );
        }
    }
}
add_filter('get_search_data','Product_Page_Search::searchData', 10, 3 );
add_action('get_search_view','Product_Page_Search::searchHtml', 10, 3 );
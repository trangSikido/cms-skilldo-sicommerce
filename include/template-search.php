<?php
Class Product_Page_Search {
    static public function searchData($objects, $type, $keyword) {

        $ci =& get_instance();

        if( $type == 'products' ) {

            $object = Product::get(['where' => ['code' => $keyword, 'type <>' => 'trash']]);

            if(have_posts($object) && $object->type != 'products') {
                $object = Product::get(['where' => ['id' => $object->parent_id]]);
            }

            $objects = [];

            if(have_posts($object)) $objects[] = $object;

            if(!have_posts($objects)) {

                if(Option::get('product_fulltext_search', false) == true) {

                    $keywordFull = '+' . $keyword;

                    $keywordFull = str_replace(' ', ' +', $keywordFull);

                    $args = [
                        'where'     => "MATCH(title) AGAINST('" . $keywordFull . "' IN BOOLEAN MODE) > 0 AND `public` = 1 AND `trash` = 0 AND `type` = 'product'",
                        'select'    => "*, MATCH(title) AGAINST('" . $keywordFull . "' IN BOOLEAN MODE) as score",
                        'orderby'   => 'score desc'
                    ];
                }
                else {
                    $args = [
                        'where' 	 => ['public' => 1, 'trash' => 0],
                        'where_like' => ['title' => [$keyword]],
                    ];
                }

                if (InputBuilder::get('category') != null && InputBuilder::get('category') != 0) {

                    $category = removeHtmlTags(InputBuilder::get('category'));

                    $args['where_category'] = $category;

                    $args['join'] = true;
                }

                $args = apply_filters('product_search_args', $args);

                $objects = apply_filters('product_search_data', Product::gets($args));
            }

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
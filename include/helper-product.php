<?php
Class ProductCategory {
    
    static public function get($args = []) {

        $ci = &get_instance();

        if( is_numeric($args) ) $args = ['where' => ['id' => (int)$args]];

        if( !have_posts($args) ) $args = array('where' => [], 'params' => [] );

        $args = array_merge( array('where' => [], 'params' => [] ), $args );

        $cache_id = 'products_category_'.md5( serialize($args) );

        if( $ci->language['default'] != $ci->language['current'] ) {
            $cache_id = 'products_category_'.$ci->language['current'].md5( serialize($args) );
        }

        $category = CacheHandler::get($cache_id);

        if(CacheHandler::has($cache_id) === false) {

            $model = get_model('products');

            $model->settable('products_categories');

            $model->settable_category('products_categories');

            $model->settable_metabox('metadata');

            $category 	= $model->get_data($args, 'products_categories');

            $model->settable('products');

            CacheHandler::save( $cache_id, $category );
        }

        return $category;
    }

    static public function gets( $args = [] ) {

        $ci = &get_instance();

        if(!have_posts($args)) $args = array('where' => [], 'params' => []);

        $args = array_merge(array('where' => [], 'params' => [] ), $args);

        $cache_id = 'products_categories_'.md5(serialize($args));

        if(Language::default() != Language::current()) {

            $cache_id = 'products_categories_'.Language::current().md5( serialize($args) );
        }

        $categories = CacheHandler::get($cache_id);

        if(empty($categories)) {

            $model = get_model('products_categories', 'backend_products');
            $model->settable('products_categories');
            $model->settable_category('products_categories');
            $model->settable_metabox('products_categories_metadata');

            $where  = $args['where'];
            $params = $args['params'];

            if(!empty($args['product_id']) && is_numeric($args['product_id'])) {

                $model->settable('relationships');

                $where  = array(
                    'object_id'     => $args['product_id'],
                    'object_type'   => 'products',
                    'value'         => 'products_categories',
                );

                if(empty($params['select'])) {
                    $params['select'] = 'products_categories.*';
                }

                $params['table_join'][0] = array(
                    'table' => 'products_categories',
                    'where' => "products_categories.id = relationships.category_id"
                );

                if($ci->frontend == true && $model->multilang == true && $model->language != $model->language_default) {

                    $params['select'] 		                    = 'products_categories.*,'.$model->language_select;

                    $params['table_join'][1]['table']           = 'language as lg';

                    $params['table_join'][1]['where']           = 'products_categories.id = lg.object_id ';

                    $params['param_where']['lg.language'] 	    = $model->language;

                    $params['param_where']['lg.object_type'] 	= 'products_categories';
                }

                return $model->gets_join($where, $params);
            }
            else if( isset($args['tree']) ) {

                if( !isset($args['tree']['data']) || !have_posts($args['tree']['data']) ) $args['tree']['data'] = [];

                $args['tree'] = array_merge( array( 'parent_id' => 0 ), $args['tree'] );

                $where['parent_id'] = $args['tree']['parent_id'];

                $categories =  wcmc_gets_category_recurs( $args['tree']['data'], $where, $params );
            }
            else if(isset($args['mutilevel'])){
                if( is_numeric($args['mutilevel']) ) {

                    $model->settable('products_categories');

                    if( $args['mutilevel'] != 0)
                        $where_level = array_merge(array('id' => $args['mutilevel']), $where);
                    else
                        $where_level = array_merge(array('parent_id' => $args['mutilevel']), $where);

                    $category = $model->fgets_categories_where('products_categories', $where_level, $params);

                    $model->settable('products_categories');

                    $categories = $ci->multilevel_categories($category, $where, $model, $params);
                }
                else {
                    if(!class_exists('nestedset')) $ci->load->library('nestedset');
                    $nestedset_config = ['model' => 'products_categories_model', 'table' => 'products_categories', 'where' => $where];
                    if(!empty($args['module'])) $nestedset_config['module'] = $args['module'];
                    $nestedset = new nestedset($nestedset_config);
                    $categories    = $nestedset->get_dropdown_backend();
                }
            }
            else $categories = $model->gets_data($args, 'products_categories');

            $model->settable('products_categories');

            $model->settable_category('products_categories');

            CacheHandler::save( $cache_id, $categories );
        }

        return $categories;
    }

    static public function count( $args = [] ) {

        if(!have_posts($args)) $args = array('where' => [], 'params' => []);

        $args = array_merge(array('where' => [], 'params' => [] ), $args);

        $cache_id = 'products_categories_count_'.md5(serialize($args));

        if(Language::default() != Language::current()) {
            $cache_id = 'products_categories_count_'.Language::current().md5( serialize($args) );
        }

        $categories = CacheHandler::get($cache_id);

        if(empty($categories)) {

            $model = get_model('products_categories', 'backend_products')->settable('products_categories')->settable_category('products_categories');

            $model->settable_metabox('products_categories_metadata');

            $categories = $model->count_data($args, 'products_categories');

            CacheHandler::save( $cache_id, $categories );
        }

        return $categories;
    }

    static public function insert($product = []) {

        $ci =& get_instance();

        $model = get_model('products_categories', 'backend')->settable('products_categories');

        $user = Auth::user();

        if (!empty($product['id'])) {

            $id 		= (int) $product['id'];

            $update 	= true;

            $old_product = static::get(['where' => array('id' => $id)]);

            if ( ! $old_product ) return new SKD_Error( 'invalid_product_id', __( 'ID danh mục sản phẩm không chính xác.' ) );

            $user_updated = (have_posts($user) ) ? $user->id : 0;

            $user_created = $old_product->user_created;

            $slug = empty($product['slug'] ) ? $old_product->slug : Str::slug($product['slug']);

            if( $slug != $old_product->slug ) $slug = $ci->edit_slug( $slug , $id, $model );

            if(empty($product['name'])) $product['name'] = $old_product->name;

            $product['content']            = (!empty($product['content'])) ? $product['content'] : $old_product->content;

            $product['excerpt']            = (!empty($product['excerpt'])) ? $product['excerpt'] : $old_product->excerpt;

            $product['seo_title']          = (!empty($product['seo_title'])) ? $product['seo_title'] : $old_product->seo_title;

            $product['seo_description']    = (!empty($product['seo_description'])) ? $product['seo_description'] : $old_product->seo_description;

            $product['seo_keywords']       = (!empty($product['seo_keywords'])) ? $product['seo_keywords'] : $old_product->seo_keywords;

            $product['image']              = (!empty($product['image'])) ? $product['image'] : $old_product->image;

            $product['status']              = (!empty($product['status'])) ? $product['status'] : $old_product->status;

            $product['public']              = (isset($product['public']) && is_numeric($product['public']) ) ? Str::clear($product['public']) : $old_product->public;

            $product['parent_id']           = (isset($product['parent_id']) && is_numeric($product['parent_id'])) ? Str::clear($product['parent_id']) : $old_product->parent_id;

            $product['level']              = (isset($product['level']) && is_numeric($product['level'])) ? Str::clear($product['level']) : $old_product->level;

            $product['lft']              = (isset($product['lft']) && is_numeric($product['lft'])) ? Str::clear($product['lft']) : $old_product->lft;

            $product['rgt']               = (isset($product['rgt'])) ? Str::clear($product['rgt']) : $old_product->rgt;

            $product['key']                = (isset($product['key'])) ? Str::clear($product['key']) : $old_product->key;

            $product['theme_layout']      = (isset($product['theme_layout'])) ? Str::clear($product['theme_layout']) : $old_product->theme_layout;

            $product['theme_view']        = (isset($product['theme_view'])) ? Str::clear($product['theme_view']) : $old_product->theme_view;
        }
        else {

            $update = false;

            $user_updated = 0;

            $user_created = (have_posts($user)) ? $user->id : 0;

            if(empty($product['name'])) return new SKD_Error('empty_product_title', __('Không thể cập nhật danh mục sản phẩm khi tên tên danh mục trống.') );

            $slug = $ci->create_slug(Str::clear($product['name']), $model);
        }

        $name 		        = Str::clear($product['name']);

        $name 		        = trim($name);

        $content            = (isset($product['content'])) ? $product['content'] : '';

        $excerpt            = (isset($product['excerpt'])) ? $product['excerpt'] : '';

        $seo_title          = (isset($product['seo_title'])) ? Str::clear($product['seo_title']) : '';

        $seo_description    = (isset($product['seo_description'])) ? Str::clear($product['seo_description']) : '';

        $seo_keywords       = (isset($product['seo_keywords'])) ? Str::clear($product['seo_keywords']) : '';

        $image              = (isset($product['image'])) ? Str::clear($product['image']) : '';

        if(!empty($image)) $image  = process_file($image);

        if(!$update) {

            if(empty($seo_title)) $seo_title = $name;

            if(empty($seo_description)) $seo_description = Str::clear($excerpt);
        }

        $status     =  (isset($product['status'])) ? Str::clear($product['status']) : 0;

        $public     = (isset($product['public']) && is_numeric($product['public'])) ? Str::clear($product['public']) : 1;

        $parent_id  = (isset($product['parent_id']) && is_numeric($product['parent_id'])) ? Str::clear($product['parent_id']) : 0;

        $level  = (isset($product['level']) && is_numeric($product['level'])) ? Str::clear($product['level']) : 0;

        $lft  = (isset($product['lft']) && is_numeric($product['lft'])) ? Str::clear($product['lft']) : 0;

        $rgt  = (isset($product['rgt']) && is_numeric($product['rgt'])) ? Str::clear($product['rgt']) : 0;

        $key    = (isset($product['key'])) ? Str::clear($product['key']) : '';

        $theme_layout = (isset($product['theme_layout'])) ? Str::clear($product['theme_layout']) : '';

        $theme_view = (isset($product['theme_view'])) ? Str::clear($product['theme_view']) : '';

        if(empty($slug)) {
            $slug = $ci->create_slug(Str::clear($name), $model);
        }

        $data = compact('name', 'slug', 'content', 'excerpt', 'status', 'image', 'user_created', 'user_updated', 'parent_id', 'level', 'lft', 'rgt', 'key', 'seo_title',  'seo_description', 'seo_keywords', 'public', 'theme_layout', 'theme_view');

        $data = apply_filters('pre_insert_products_categories_data', $data, $product, $update ? $old_product : null, $product);

        $language = !empty($product['language']) ? $product['language'] : [];

        if ($update) {

            $model->settable('products_categories')->update_where($data, compact('id'));

            $product_id = (int) $id;

            /*=============================================================
            ROUTER
            =============================================================*/
            if(!empty($slug)) {
                $model->settable('routes');
                $router['object_type']	= 'products_categories';
                $router['object_id']	= $product_id;
                if($model->update_where( array('slug' => $slug ), $router ) == 0) {
                    $router['slug'] 		= $slug;
                    $router['directional']	= 'products_categories';
                    $router['controller']	= 'frontend_products/products/index/';
                    $model->add($router);
                }
            }
            /*=============================================================
            NESTEDSET
            =============================================================*/
            if(!class_exists('nestedset')) $ci->load->library('nestedset');
            $model->settable('products_categories');
            $nestedset = new nestedset(array('model' => 'products_categories_model', 'table' => 'products_categories'));
            $nestedset->get();
            $nestedset->recursive(0, $nestedset->set());
            $nestedset->action();
        }
        else {

            $product_id = $model->settable('products_categories')->add($data);
            /*=============================================================
            ROUTER
            =============================================================*/
            if(!empty($slug)) {
                $model->settable('routes');
                $router['slug'] 		= $slug;
                $router['object_type']	= 'products_categories';
                $router['directional']	= 'products_categories';
                $router['controller']	= 'frontend_products/products/index/';
                $router['object_id']	= $product_id;
                $model->add($router);
            }
            /*=============================================================
            NESTEDSET
            =============================================================*/
            if(!class_exists('nestedset')) $ci->load->library('nestedset');
            $model->settable('products_categories');
            $nestedset = new nestedset(array('model' => 'products_categories_model', 'table' => 'products_categories'));
            $nestedset->get();
            $nestedset->recursive(0, $nestedset->set());
            $nestedset->action();

            /*=============================================================
            LANGUAGE
            =============================================================*/
            if( have_posts($language) ) {
                $model->settable('language');
                foreach ($language as $key => $val) {
                    $lang['name'] 			= Str::clear($val['name']);
                    $lang['excerpt'] 		= $val['excerpt'];
                    $lang['content'] 		= $val['content'];
                    $lang['language'] 		= $key;
                    $lang['object_id']  	= $product_id;
                    $lang['object_type']  	= 'products_categories';
                    $model->add($lang);
                }
            }
            $model->settable('products_categories');
        }

        CacheHandler::delete('products_categories_', true);

        $model->settable('products_categories');

        return $product_id;
    }

    static public function delete( $cate_ID = 0 ) {

        $ci =& get_instance();

        $cate_ID = (int)Str::clear($cate_ID);

        if( $cate_ID == 0 ) return false;

        $model  = get_model('products_categories', 'backend_post')->settable('products_categories')->settable_category('products_categories');

        $category  = static::get( $cate_ID );

        if( have_posts($category) ) {

            $ci->data['module']   = 'products_categories';

            $listID = $model->gets_category_sub($category);

            if(!have_posts($listID)) $listID = [$cate_ID];

            if(have_posts($listID)) {

                $model->settable('relationships')->delete_where_in(['field' => 'category_id','data' => $listID,], ['object_type' => 'products', 'value' => 'products_categories']);

                $where['object_type'] = 'products_categories';

                $data['field'] = 'object_id';

                $data['data']  = $listID;

                //xóa router
                $model->settable('routes');
                $model->delete_where_in( $data, $where );

                //xóa ngôn ngữ
                $model->settable('language');
                $model->delete_where_in( $data, $where );

                //xóa gallery
                foreach ($listID as $key => $id) {
                    Gallery::deleteItemByObject($id, 'products_categories');
                    Metadata::deleteByMid('products_categories', $id);
                }

                $model->settable('products_categories');
                $data['field'] = 'id';
                $data['data']  = $listID;

                if($model->delete_where_in($data)) {

                    do_action('product_delete_success', $listID); //ver 3.0.2

                    foreach ($listID as $id) {
                        CacheHandler::delete('breadcrumb_products_index_'.$id, true);
                    }
                    CacheHandler::delete('breadcrumb_products_detail_', true);

                    //delete menu
                    $model->settable('menu');
                    $model->delete_where_in(['field' => 'object_id', 'data' => $listID], ['object_type' => 'products_categories']);
                    CacheHandler::delete('menu-', true);

                    return $listID;
                }
            }
        }

        return false;
    }

    static public function deleteList( $cate_ID = [] ) {

        $result = [];

        if(!have_posts($cate_ID)) return false;

        foreach ($cate_ID as $key => $id) {
            if( static::delete($id) != false ) $result[] = $id;
        }

        if(have_posts($result)) return $result;

        return false;
    }

    static public function getMeta( $cateID, $key = '', $single = true) {
        return Metadata::get('products_categories', $cateID, $key, $single);
    }

    static public function updateMeta($cateID, $meta_key, $meta_value) {
        return Metadata::update('products_categories', $cateID, $meta_key, $meta_value);
    }

    static public function deleteMeta($cateID, $meta_key = '', $meta_value = '') {
        return Metadata::delete('products_categories', $cateID, $meta_key, $meta_value);
    }
}

Class Product {

    static public function get($args = []) {

        $model = get_model('products')->settable('products')->settable_metabox('product_metadata');
        
        if(is_numeric($args)) $args = ['where' => ['id' => (int)$args]];

        if(!have_posts($args)) return [];

        $args = array_merge(array('where' => [], 'params' => [] ), $args);

        if(is_array($args) && (!isset($args['where']['type']) && !isset($args['where']['type <>']))) {

            $args['where']['type'] = 'product';
        }

        if( !Admin::is() && isset( $args['where'])) {

            if(!isset($args['where']['trash']) && !isset($args['where']['trash <>'])) {
                $args['where'] = array_merge(['trash' => 0] , $args['where'] );
            }
            if(!isset($args['where']['public']) && !isset($args['where']['public <>'])) {
                $args['where'] = array_merge(['public' => 1] , $args['where'] );
            }
        }

        /* 4.0.0 */
        $args = apply_filters('get_product_args', $args);

        $product = $model->get_data($args, 'products');

        return apply_filters('get_product', $product, $args );
    }

    static public function gets($args = []) {

        $model = get_model('products')->settable('products')->settable_metabox('product_metadata');

        if(is_numeric($args)) $args = ['where' => ['id' => (int)$args]];

        if(!have_posts($args)) $args = [];

        $args = array_merge(array('where' => [], 'params' => [], 'join' => false ), $args );

        if(is_array($args['where'])) {

            if( !Admin::is() && isset( $args['where'])) {

                if(!isset($args['where']['trash']) && !isset($args['where']['trash <>'])) {
                    $args['where'] = array_merge(['trash' => 0] , $args['where'] );
                }
                if(!isset($args['where']['public']) && !isset($args['where']['public <>'])) {
                    $args['where'] = array_merge(['public' => 1] , $args['where'] );
                }
            }

            if(is_array($args) && (!isset($args['where']['type']) && !isset($args['where']['type <>']))) {

                $args['where']['type'] = 'product';
            }
        }

        /* 4.0.0 */
        $args = apply_filters('gets_product_args', $args);

        $where 	= $args['where'];

        $params = $args['params'];

        $products = [];

        /**
         * @since 1.7.2
         * Thêm $args['join'] : kết hợp nhiều điều kiện lại với nhau
         */
        if($args['join'] == true) {

            $data = [];

            $params_where  = $params;

            if( isset($args['where_category']) ) $params_where['select'] = 'id';

            if(isset($args['where_in'])) 		$data['in']      = $args['where_in'];

            if(isset($args['where_not_in'])) 	$data['not_in']  = $args['where_not_in'];

            if(isset($args['where_like'])) 		$data['like']    = $args['where_like'];

            if(isset($args['where_or_like'])) 	$data['or_like'] = $args['where_or_like'];

            $products = $model->gets_where_more( $data, $where, $params_where  );

            if( isset($args['where_category']) && have_posts($products) ) {

                $data_where =  [];

                foreach ($products as $value) $data_where[] = $value->id;

                $products = [];

                $data_category = [];

                if( is_numeric($args['where_category']) ) {

                    $args['where_category'] = ProductCategory::get( $args['where_category'] );
                }

                if( have_posts($args['where_category']) ) $data_category	= $model->gets_relationship_list($model->gets_category_sub($args['where_category']), 'object_id', 'products');

                if( have_posts($data_category) ) {

                    $data['field'] = 'id';

                    $data['data'] = array_intersect( $data_category, $data_where );

                    $products =  $model->fgets_where_in( 'products' , $data, $where, $params);

                }
            }

        }
        else {
            if(isset($args['tax_query']) || isset($args['attr_query'])) {

                $products = [];

                $where = $args['where'];

                $params = $args['params'];

                $sql_table  = CLE_PREFIX.'products';

                if(isset($params['select'])) {

                    $select = Str::clear($params['select']);
                }
                else {

                    $select = '`'.$sql_table.'`.*';
                }

                $sql = 'SELECT SQL_CALC_FOUND_ROWS '.$select.' FROM `'.$sql_table.'` ';

                if( $model->multilang == true && $model->language != $model->language_default) {

                    $sql = 'SELECT SQL_CALC_FOUND_ROWS '.$select.', lg.title, lg.content, lg.excerpt, lg.language FROM `'.$sql_table.'` ';

                    $sql .= 'INNER JOIN `'.CLE_PREFIX.'language` AS lg ';
                }
                if(empty($args['tax_query']) && !empty($args['where_category'])) {

                    if(is_numeric($args['where_category'])) {
                        $args['where_category'] = ProductCategory::get( $args['where_category'] );
                    }

                    $model->settable_category('products_categories');

                    $sub = $model->gets_category_sub($args['where_category'], true);

                    $filter_taxonomy = implode(',', $sub);

                    $args['tax_query'][] = [
                        'taxonomy'  => 'products_categories',
                        'field'     => 'id',
                        'terms'     => $filter_taxonomy,
                    ];
                }

                $tax_query  = static::tax_query((isset($args['tax_query'])) ? $args['tax_query'] : []);

                $attr_query = static::attr_query((isset($args['attr_query'])) ? $args['attr_query'] : []);

                $sql .= $tax_query['join'];

                $sql .= $attr_query['join'];

                $sql .= 'WHERE 1=1';

                if( $model->multilang == true && $model->language != $model->language_default) {

                    $sql .= ' AND `lg`.`object_id` = '.$sql_table.'.id AND `lg`.`language` = \''.$model->language.'\' AND `lg`.`object_type` = \'products\'';
                }

                if(!empty($tax_query['where'])) $sql .= ' AND '.$tax_query['where'];

                if(!empty($attr_query['where'])) $sql .= ' AND '.$attr_query['where'];

                if(have_posts($where)) {

                    $sql .= ' AND (';

                    foreach ($where as $key => $value) {

                        $key = trim($key);

                        $compare = '=';

                        if(substr($key,-1) == '>') $compare = '>';

                        if(substr($key,-1) == '<') $compare = '<';

                        if(substr($key,-2) == '>=') $compare = '>=';
                        if(substr($key,-2) == '<=') $compare = '<=';

                        if(substr($key,-2) == '<>') $compare = '<>';

                        if(substr($key,-2) == '!=') $compare = '<>';

                        $key = trim(Str::clear($key));

                        $sql .= '`'.$sql_table.'`.`'.Str::clear($key).'` '.$compare.' \''.Str::clear($value).'\' AND ';
                    }

                    $sql = trim( $sql, 'AND ' );

                    $sql .= ')';
                }
                else {
                    $sql .= ' AND ('.$where.')';
                }

                if(have_posts($params)) {

                    if(isset($params['groupby'])) {

                        if(is_array($params['groupby'])) {

                            $groupby = explode(',',$params['groupby']);

                            if(have_posts($groupby)) {

                                foreach ($groupby as &$oby) {

                                    $oby_arr = explode('.',$oby);

                                    if(count($oby_arr) == 2) {

                                        $oby = '`'.CLE_PREFIX.trim(Str::clear($oby_arr[0])).'`.'.trim(Str::clear($oby_arr[1]));
                                    }
                                    else {

                                        $oby = '`'.Str::clear($oby).'`';
                                    }

                                }

                                $params['groupby'] = implode(',',$groupby);
                            }
                            else {
                                $params['groupby'] = '`'.Str::clear($params['groupby']).'`';
                            }
                        }

                        $sql .= ' GROUP BY '.Str::clear($params['groupby']);
                    }
                    else {
                        $sql .= ' GROUP BY `'.$sql_table.'`.`id`';
                    }

                    if(isset($params['orderby'])) {

                        if(!empty($args['orderby_identifiers'])) {

                            $orderby = explode(',',$params['orderby']);

                            if(have_posts($orderby)) {

                                foreach ($orderby as &$oby) {

                                    $oby_arr = explode('.',$oby);

                                    if(count($oby_arr) == 2) {

                                        $oby = '`'.CLE_PREFIX.trim(Str::clear($oby_arr[0])).'`.'.trim(Str::clear($oby_arr[1]));
                                    }
                                    else {

                                        $oby = '`'.Str::clear($oby).'`';
                                    }

                                }

                                $params['orderby'] = implode(',',$orderby);
                            }
                            else {
                                $params['orderby'] = '`'.Str::clear($params['orderby']).'`';
                            }
                        }

                        $sql .= ' ORDER BY '.Str::clear($params['orderby']);
                    }

                    if(isset($params['limit'])) {

                        $sql .= ' LIMIT ';

                        if(isset($params['start'])) {

                            $sql .= (int)$params['start'].',';
                        }

                        $sql .= (int)$params['limit'];
                    }
                }
                else {

                    $sql .= ' GROUP BY `'.$sql_table.'`.`id`';
                }

                if(!empty($args['sql'])) return $sql;

                $query = $model->query($sql);

                foreach ($query->result() as $row ) {

                    $products[] = $row;
                }
            }
            //Lấy sản phẩm theo danh mục taxonomy
            else if( isset($args['taxonomy']) ) {

                if( is_string($args['taxonomy']) ) {

                    $args['taxonomy'] = ProductCategory::get( array('where' => array('slug' => Str::clear($args['taxonomy'])) ) );
                }

                if( have_posts($args['taxonomy']) ) {

                    $taxonomy = $args['taxonomy'];

                    $model->settable_category('categories');

                    //lấy danh sach ID danh mục cần lấy
                    $listID = $model->gets_category_sub($taxonomy);

                    $data['data'] 	=  $model->gets_relationship_list( $listID, 'object_id', 'products', $taxonomy->cate_type );

                    $data['field'] 	=  'id';

                    if( isset($args['where_category']) && have_posts($data['data']) ) {

                        $model->settable_category('products_categories');

                        if(is_numeric($args['where_category'])) {
                            $args['where_category'] = ProductCategory::get( $args['where_category'] );
                        }

                        if( have_posts($args['where_category']) ) $data_category	= $model->gets_relationship_list($model->gets_category_sub($args['where_category']), 'object_id', 'products');

                        if( have_posts($data_category) ) {
                            $data['data'] = array_intersect( $data_category, $data['data'] );
                        }
                        else {
                            $data['data'] = [];
                        }
                    }

                    $model->settable_category('products_categories');

                    $products = $model->fgets_where_in( 'products' , $data, $where, $params);

                }
            }
            //Lấy sản phẩm theo danh mục
            else if( isset($args['where_category']) ) {
                /**
                 * @since 1.7.2
                 * Kiêm tra $args['where_category'] nếu là ID thì get category
                 */
                if( is_numeric($args['where_category']) ) {
                    $args['where_category'] = ProductCategory::get( $args['where_category'] );
                }
                if( have_posts($args['where_category']) ) {

                    $products = $model->gets_data( $args, 'products' );
                }
            }
            else $products = $model->gets_data( $args, 'products' );
        }

        return apply_filters('gets_product', $products, $args );
    }

    static public function tax_query($tax_query) {

        $sql = [
            'join'  => '',
            'where' => ''
        ];

        $sql_table  = CLE_PREFIX.'products';

        $taxonomyID = [];

        $relation 	= 'AND';

        if( !empty($tax_query['relation']) ) {
            $relation = $tax_query['relation'];
            unset($tax_query['relation']);
        }

        if($relation != 'AND' || $relation != 'OR') $relation = 'AND';

        foreach ($tax_query as $key => $tax) {

            $dataID = [];

            if(isset($tax['field']) && isset($tax['terms'])) {

                $field = $tax['field'];

                if(is_array($tax['terms'])) {

                    $dataID = $tax['terms'];
                }
                else {

                    if($tax['taxonomy'] == 'products_categories') {
                        $taxonomy = ProductCategory::get(['where' => array($field => Str::clear($tax['terms']))]);
                    }
                    else {
                        $taxonomy = PostCategory::get(['where' => array(
                            $field => Str::clear($tax['terms']),
                            'cate_type'   => Str::clear($tax['taxonomy']),
                        )]);
                    }

                    if(have_posts($taxonomy)) {

                        $model = get_model()->settable_category('products_categories');

                        $dataID = $model->gets_category_sub($taxonomy, true);

                        $taxonomyID['txnm'.$key] = ['data' => $dataID, 'taxonomy' => $tax['taxonomy']];
                    }
                }
            }

            $sql['join'] .= 'INNER JOIN `'.CLE_PREFIX.'relationships` AS txnm'.$key.' ON ( `'.$sql_table.'`.id = txnm'.$key.'.object_id ) ';

            if(isset($dataID) && have_posts($dataID)) {

                $taxonomyID['txnm'.$key] = ['data' => $dataID, 'taxonomy' => $tax['taxonomy']];
            }
        }

        if(have_posts($taxonomyID)) {

            $temp       = true;

            $sql_temp   = '';

            foreach ($taxonomyID as $txnmkey => $taxonomy) {

                if(!have_posts($taxonomy['data'])) continue;

                $temp = false;

                $sql_temp    .= '(';

                $sql_temp .= $txnmkey.'.`category_id` IN ('.implode(',',$taxonomy['data']).')';

                $sql_temp .= ' AND '.$txnmkey.'.`value` = \''.$taxonomy['taxonomy'].'\'';

                $sql_temp .= ') '.$relation.' ';
            }

            if($temp == false) {

                $sql['where']  .= ' AND ('.$sql_temp;

                $sql['where'] = trim( $sql['where'], ' '.$relation.' ' );

                $sql['where'] .= ')';
            }
        }

        return $sql;
    }

    static public function attr_query($attr_query) {

        $sql = [
            'join'  => '',
            'where' => ''
        ];

        $sql_table  = CLE_PREFIX.'products';

        if(have_posts($attr_query)) {
            foreach ($attr_query as $key => $attr) {
                $sql['join'] .= 'INNER JOIN `'.CLE_PREFIX.'relationships` AS attr'.$key.' ON ( `'.$sql_table.'`.id = attr'.$key.'.object_id ) ';
            }
        }

        if(have_posts($attr_query)) {
            $sql['where'] .= '(';
            foreach ($attr_query as $key => $attr) {
                $attrkey = 'attr'.$key;
                $sql['where'] .= '(';
                $sql['where'] .= $attrkey.'.`object_type` = \'attributes\' AND '.$attrkey.'.`category_id` = \'attribute_op_'.$attr['group'].'\' AND '.$attrkey.'.`value` IN ('.implode(',',$attr['attribute']).')';
                $sql['where'] .= ') AND ';
            }
            $sql['where'] = trim( $sql['where'], ' AND ' );
            $sql['where'] .= ')';
        }

        return $sql;
    }

    static public function count($args = []) {

        $model = get_model('products')->settable('products');

        if(is_numeric($args)) $args = ['where' => ['id' => (int)$args]];

        if(!have_posts($args)) $args = [];

        $args = array_merge( array('where' => [], 'params' => [] ), $args );

        if(is_array($args['where'])) {

            if( !Admin::is() && isset( $args['where'])) {

                if(!isset($args['where']['trash']) && !isset($args['where']['trash <>'])) {
                    $args['where'] = array_merge(['trash' => 0] , $args['where'] );
                }
                if(!isset($args['where']['public']) && !isset($args['where']['public <>'])) {
                    $args['where'] = array_merge(['public' => 1] , $args['where'] );
                }
            }

            if(is_array($args) && (!isset($args['where']['type']) && !isset($args['where']['type <>']))) {

                $args['where']['type'] = 'product';
            }
        }

        /* 4.0.0 */
        $args = apply_filters('count_product_args', $args);

        $where 	= $args['where'];

        $params = $args['params'];

        if(isset($args['tax_query']) || isset($args['attr_query'])) {

            $products = [];

            $where = $args['where'];

            $params = $args['params'];

            $sql_table  = CLE_PREFIX.'products';

            if(isset($params['select'])) {

                $select = Str::clear($params['select']);
            }
            else {

                $select = '`'.$sql_table.'`.*';
            }

            $sql = 'SELECT SQL_CALC_FOUND_ROWS '.$select.' FROM `'.$sql_table.'` ';

            if( $model->multilang == true && $model->language != $model->language_default) {

                $sql = 'SELECT SQL_CALC_FOUND_ROWS '.$select.', lg.title, lg.content, lg.excerpt, lg.language FROM `'.$sql_table.'` ';

                $sql .= 'INNER JOIN `'.CLE_PREFIX.'language` AS lg ';
            }
            if(empty($args['tax_query']) && !empty($args['where_category'])) {

                if(is_numeric($args['where_category'])) {
                    $args['where_category'] = ProductCategory::get( $args['where_category'] );
                }

                $model->settable_category('products_categories');

                $sub = $model->gets_category_sub($args['where_category'], true);

                $filter_taxonomy = implode(',', $sub);

                $args['tax_query'][] = [
                    'taxonomy'  => 'products_categories',
                    'field'     => 'id',
                    'terms'     => $filter_taxonomy,
                ];
            }

            $tax_query  = static::tax_query((isset($args['tax_query'])) ? $args['tax_query'] : []);

            $attr_query = static::attr_query((isset($args['attr_query'])) ? $args['attr_query'] : []);

            $sql .= $tax_query['join'];

            $sql .= $attr_query['join'];

            $sql .= 'WHERE 1=1';

            if( $model->multilang == true && $model->language != $model->language_default) {

                $sql .= ' AND `lg`.`object_id` = '.$sql_table.'.id AND `lg`.`language` = \''.$model->language.'\' AND `lg`.`object_type` = \'products\'';
            }

            if(!empty($tax_query['where'])) $sql .= ' AND '.$tax_query['where'];

            if(!empty($attr_query['where'])) $sql .= ' AND '.$attr_query['where'];

            if(have_posts($where)) {

                $sql .= ' AND (';

                foreach ($where as $key => $value) {

                    $key = trim($key);

                    $compare = '=';

                    if(substr($key,-1) == '>') $compare = '>';

                    if(substr($key,-1) == '<') $compare = '<';

                    if(substr($key,-2) == '>=') $compare = '>=';
                    if(substr($key,-2) == '<=') $compare = '<=';

                    if(substr($key,-2) == '<>') $compare = '<>';

                    if(substr($key,-2) == '!=') $compare = '<>';

                    $key = trim(Str::clear($key));

                    $sql .= '`'.$sql_table.'`.`'.Str::clear($key).'` '.$compare.' \''.Str::clear($value).'\' AND ';
                }

                $sql = trim( $sql, 'AND ' );

                $sql .= ')';
            }
            else {
                $sql .= ' AND ('.$where.')';
            }

            if(have_posts($params)) {

                if(isset($params['groupby'])) {

                    if(is_array($params['groupby'])) {

                        $groupby = explode(',',$params['groupby']);

                        if(have_posts($groupby)) {

                            foreach ($groupby as &$oby) {

                                $oby_arr = explode('.',$oby);

                                if(count($oby_arr) == 2) {

                                    $oby = '`'.CLE_PREFIX.trim(Str::clear($oby_arr[0])).'`.'.trim(Str::clear($oby_arr[1]));
                                }
                                else {

                                    $oby = '`'.Str::clear($oby).'`';
                                }

                            }

                            $params['groupby'] = implode(',',$groupby);
                        }
                        else {
                            $params['groupby'] = '`'.Str::clear($params['groupby']).'`';
                        }
                    }

                    $sql .= ' GROUP BY '.Str::clear($params['groupby']);
                }
                else {
                    $sql .= ' GROUP BY `'.$sql_table.'`.`id`';
                }

                if(isset($params['orderby'])) {

                    if(!empty($args['orderby_identifiers'])) {

                        $orderby = explode(',',$params['orderby']);

                        if(have_posts($orderby)) {

                            foreach ($orderby as &$oby) {

                                $oby_arr = explode('.',$oby);

                                if(count($oby_arr) == 2) {

                                    $oby = '`'.CLE_PREFIX.trim(Str::clear($oby_arr[0])).'`.'.trim(Str::clear($oby_arr[1]));
                                }
                                else {

                                    $oby = '`'.Str::clear($oby).'`';
                                }

                            }

                            $params['orderby'] = implode(',',$orderby);
                        }
                        else {
                            $params['orderby'] = '`'.Str::clear($params['orderby']).'`';
                        }
                    }

                    $sql .= ' ORDER BY '.Str::clear($params['orderby']);
                }

                if(isset($params['limit'])) {

                    $sql .= ' LIMIT ';

                    if(isset($params['start'])) {

                        $sql .= (int)$params['start'].',';
                    }

                    $sql .= (int)$params['limit'];
                }
            }
            else {

                $sql .= ' GROUP BY `'.$sql_table.'`.`id`';
            }

            if(!empty($args['sql'])) return $sql;

            $query = $model->query($sql);

            $query = $model->query('SELECT FOUND_ROWS() as count');

            foreach ($query->result() as $row ) {

                $products = $row;
            }

            $products = $products->count;
        }
        else if( isset($args['where_category']) ) {

            $model->settable_category('products_categories');

            if( is_numeric($args['where_category']) ) {

                $args['where_category'] = ProductCategory::get( $args['where_category'] );
            }

            if( have_posts($args['where_category']) ) {

                return $model->count_data($args, 'products' );
            }

            return [];
        }
        else $products = $model->count_data($args, 'products' );

        return apply_filters('count_product', $products, $args );
    }

    static public function insert($product = []) {

        $ci =& get_instance();

        $model = get_model()->settable('products');

        $user = Auth::user();

        if (!empty($product['id'])) {

            $id 		= (int) $product['id'];

            $update 	= true;

            $old_product = static::get(['where' => array('id' => $id, 'type <>' => 'null')]);

            if ( ! $old_product ) return new SKD_Error( 'invalid_product_id', __( 'ID sản phẩm không chính xác.' ) );

            $user_updated = ( have_posts($user) ) ? $user->id : 0;

            $user_created = $old_product->user_created;

            $slug = empty( $product['slug'] ) ? $old_product->slug : Str::slug($product['slug']);

            if( $slug != $old_product->slug ) $slug = $ci->edit_slug( $slug , $id, $model );

            if(empty($product['title'])) $product['title'] = $old_product->title;

            $product['supplier_id'] 	= (isset($product['supplier_id'])) ? (int)$product['supplier_id'] : $old_product->supplier_id;

            $product['brand_id'] 	= (isset($product['brand_id'])) ? (int)$product['brand_id'] : $old_product->brand_id;

            $product['code'] 			= (isset($product['code'])) ? Str::clear($product['code']) : $old_product->code;

            $product['type'] 			= (!empty($product['type'])) ? Str::clear($product['type']) : $old_product->type;

            $product['content']            = (!empty($product['content'])) ? $product['content'] : $old_product->content;

            $product['excerpt']            = (!empty($product['excerpt'])) ? $product['excerpt'] : $old_product->excerpt;

            $product['seo_title']          = (!empty($product['seo_title'])) ? $product['seo_title'] : $old_product->seo_title;

            $product['seo_description']    = (!empty($product['seo_description'])) ? $product['seo_description'] : $old_product->seo_description;

            $product['seo_keywords']       = (!empty($product['seo_keywords'])) ? $product['seo_keywords'] : $old_product->seo_keywords;

            $product['image']              = (!empty($product['image'])) ? $product['image'] : $old_product->image;

            $product['status']              = (!empty($product['status'])) ? $product['status'] : $old_product->status;

            $product['status1']              = (isset($product['status1'])) ? $product['status1'] : $old_product->status1;

            $product['status2']              = (isset($product['status2'])) ? $product['status2'] : $old_product->status2;

            $product['status3']              = (isset($product['status3'])) ? $product['status3'] : $old_product->status3;

            $product['public']              = (isset($product['public']) && is_numeric($product['public']) ) ? Str::clear($product['public']) : $old_product->public;

            $product['trash']               = (isset($product['trash']) && is_numeric($product['trash']) ) ? Str::clear($product['trash']) : $old_product->trash;

            $product['parent_id']           = (isset($product['parent_id']) && is_numeric($product['parent_id'])) ? Str::clear($product['parent_id']) : $old_product->parent_id;;

            $product['weight']              = (isset($product['weight']) && is_numeric($product['weight'])) ? Str::clear($product['weight']) : $old_product->weight;;

            $product['price']               = (isset($product['price'])) ? Str::clear($product['price']) : $old_product->price;;

            $product['price_sale']          = (isset($product['price_sale'])) ? Str::clear($product['price_sale']) : $old_product->price_sale;;
        }
        else {

            $product['type'] 			= (!empty($product['type'])) ? Str::clear($product['type']) : 'product';

            $update = false;

            $user_updated = 0;

            $user_created = ( have_posts($user) ) ? $user->id : 0;

            if($product['type'] == 'product') {

                if(empty($product['title'])) return new SKD_Error('empty_product_title', __('Không thể cập nhật sản phẩm khi tên tên sản phẩm trống.') );

                $slug = $ci->create_slug( Str::clear( $product['title'] ), $model);
            }
            else {

                if(empty($product['title'])) $product['title'] = '';

                $slug = '';
            }
        }

        $title 		        = Str::clear( $product['title'] );

        $pre_title 	        = apply_filters( 'pre_title', $title );

        $title 		        = trim( $pre_title );

        $type               =  Str::clear($product['type']);

        $supplier_id        = (isset($product['supplier_id'])) ? (int)$product['supplier_id'] : 0;

        $brand_id           = (isset($product['brand_id'])) ? (int)$product['brand_id'] : 0;

        $code               = (isset($product['code'])) ? Str::clear($product['code']) : 0;

        $content            = (isset($product['content'])) ? $product['content'] : '';

        $excerpt            = (isset($product['excerpt'])) ? $product['excerpt'] : '';

        $seo_title          = (isset($product['seo_title'])) ? Str::clear($product['seo_title']) : '';

        $seo_description    = (isset($product['seo_description'])) ? Str::clear($product['seo_description']) : '';

        $seo_keywords       = (isset($product['seo_keywords'])) ? Str::clear($product['seo_keywords']) : '';

        $image              = (isset($product['image'])) ? Str::clear($product['image']) : '';

        $image              = process_file($image);

        if(!$update) {

            if(empty($seo_title)) $seo_title = $title;

            if(empty($seo_description)) $seo_description = Str::clear($excerpt);
        }

        $status     =  (isset($product['status'])) ? Str::clear($product['status']) : 'public';

        $status1    =  (isset($product['status1']) && is_numeric($product['status1'])) ? Str::clear($product['status1']) : 0;

        $status2    =  (isset($product['status2']) && is_numeric($product['status2'])) ? Str::clear($product['status2']) : 0;

        $status3    =  (isset($product['status3']) && is_numeric($product['status3'])) ? Str::clear($product['status3']) : 0;

        $public     = (isset($product['public']) && is_numeric($product['public'])) ? Str::clear($product['public']) : 1;

        $trash      = (isset($product['trash']) && is_numeric($product['trash'])) ? Str::clear($product['trash']) : 0;

        $parent_id  = (isset($product['parent_id']) && is_numeric($product['parent_id'])) ? Str::clear($product['parent_id']) : 0;

        $weight     = (isset($product['weight']) && is_numeric($product['weight'])) ? Str::clear($product['weight']) : 0;

        $price      = (isset($product['price'])) ? Str::clear($product['price']) : 0;

        $price      = str_replace(',', '', trim($price));

        $price      = (int)str_replace('.', '', trim($price));

        $price_sale = (isset($product['price_sale'])) ? Str::clear($product['price_sale']) : 0;

        $price_sale      = str_replace(',', '', trim($price_sale));

        $price_sale      = (int)str_replace('.', '', trim($price_sale));

        if($type == 'product' && empty($slug)) {
            $slug = $ci->create_slug( Str::clear( $title ), $model);
        }

        $data = compact('code', 'title', 'slug', 'content', 'excerpt', 'price', 'price_sale', 'status', 'status1', 'status2', 'status3', 'image', 'user_created', 'user_updated', 'supplier_id', 'brand_id', 'type', 'parent_id', 'weight', 'seo_title',  'seo_description', 'seo_keywords', 'public', 'trash' );

        $data = apply_filters( 'pre_insert_product_data', $data, $product, $update ? $old_product : null, $product);

        if(isset($product['taxonomies'])) {

            $taxonomies     = !empty( $product['taxonomies'] ) ? $product['taxonomies'] : [];
        }

        $language 		= !empty( $product['language'] ) ? $product['language'] : [];

        if ($update) {

            $model->settable('products')->update_where( $data, compact('id'));

            $product_id = (int) $id;

            CacheHandler::delete('breadcrumb_products_detail_'.$product_id, true);

            /*=============================================================
            ROUTER
            =============================================================*/
            if(!empty($slug)) {

                $model->settable('routes');

                $router['object_type']	= 'products';

                $router['object_id']	= $product_id;

                if($model->update_where( array('slug' => $slug ), $router ) == 0) {

                    $router['slug'] 		= $slug;

                    $router['directional']	= 'products';

                    $router['controller']	= 'frontend_products/products/detail/';

                    $model->add($router);
                }
            }
            /*=============================================================
            TAXONOMY
            =============================================================*/
            if(isset($taxonomies)) {

                if( have_posts($taxonomies) ) {

                    $model->settable('relationships');

                    $temp = $model->gets_where(array('object_id' => $product_id, 'object_type' => 'products'), array('select' => 'value', 'groupby' => array('value')));

                    $taxonomy_cate_type = [];

                    foreach ($temp as $temp_key => $temp_value) {

                        $taxonomy_cate_type[$temp_value->value] = $temp_value->value;
                    }

                    $taxonomy['object_id'] 		= $product_id;

                    $taxonomy['object_type'] 	= 'products';

                    foreach ($taxonomies as $taxonomy_key => $taxonomy_value ) {

                        if(isset($taxonomy_cate_type[$taxonomy_key])) unset($taxonomy_cate_type[$taxonomy_key]);

                        $taxonomy['value'] 		= $taxonomy_key;

                        $temp_old = $model->gets_where(array('object_id' => $product_id, 'object_type' => 'products', 'value' => $taxonomy_key ), array('select' => 'id, category_id'));

                        $taxonomies_old = [];

                        if(have_posts($temp_old)) {

                            foreach ($temp_old as $temp_old_value) {

                                $taxonomies_old[$temp_old_value->category_id] = $temp_old_value->category_id;
                            }
                        }

                        //Trường hợp không có taxonomy old và có taxonomy mới
                        if(!have_posts($taxonomies_old) && have_posts($taxonomy_value)) {

                            foreach ($taxonomy_value as $taxonomy_id) {

                                $taxonomy['category_id'] = $taxonomy_id;

                                $model->add($taxonomy);
                            }
                        }
                        //Trường hợp có taxomomy old và không có taxonomy mới
                        else if(have_posts($taxonomies_old) && !have_posts($taxonomy_value)) {

                            $model->delete_where(array('object_id' => $product_id, 'object_type' => 'products', 'value' => $taxonomy_key ));
                        }
                        else {
                            foreach ($taxonomy_value as $taxonomy_id) {
                                //Đã có trong taxonomy old
                                if(in_array($taxonomy_id, $taxonomies_old) !== false) {
                                    unset($taxonomies_old[$taxonomy_id]);
                                    continue;
                                }
                                //Không có thì thêm mới
                                $taxonomy['category_id'] = $taxonomy_id;
                                $model->add($taxonomy);
                            }
                            //Còn $taxonomies_old
                            if(have_posts($taxonomies_old)) {
                                $model->delete_where_in(
                                    array('field'       => 'category_id', 'data' => $taxonomies_old),
                                    array('object_id'   => $product_id, 'object_type' => 'products', 'value' => $taxonomy_key )
                                );
                            }
                        }
                    }

                    if( have_posts($taxonomy_cate_type) ) {

                        foreach ($taxonomy_cate_type as $ta_cate_type) {

                            $model->delete_where(array('object_id' => $product_id, 'object_type' => 'products', 'value' => $ta_cate_type ));
                        }
                    }
                }
                else {

                    $model->settable('relationships');

                    $model->delete_where(array('object_id' => $product_id, 'object_type' => 'products'));
                }
            }
        }
        else {

            $product_id = $model->settable('products')->add( $data );

            /*=============================================================
            ROUTER
            =============================================================*/
            if(!empty($slug)) {

                $model->settable('routes');

                $router['slug'] 		= $slug;
                $router['object_type']	= 'products';
                $router['directional']	= 'products';
                $router['controller']	= 'frontend_products/products/detail/';
                $router['object_id']	= $product_id;

                $model->add($router);
            }
            /*=============================================================
            TAXONOMY
            =============================================================*/
            if( isset($taxonomies) && have_posts($taxonomies) ) {

                $model->settable('relationships');

                $taxonomy['object_id'] 		= $product_id;

                $taxonomy['object_type'] 	= 'products';

                foreach ($taxonomies as $taxonomy_key => $taxonomy_value ) {

                    $taxonomy['value'] 		= $taxonomy_key;

                    foreach ($taxonomy_value as $taxonomy_id) {

                        $taxonomy['category_id'] = $taxonomy_id;

                        $model->add($taxonomy);
                    }

                }
            }

            /*=============================================================
            LANGUAGE
            =============================================================*/
            if( have_posts($language) ) {

                $model->settable('language');

                foreach ($language as $key => $val) {

                    $lang['title'] 			= Str::clear($val['title']);

                    $lang['excerpt'] 		= $val['excerpt'];

                    $lang['content'] 		= $val['content'];

                    $lang['language'] 		= $key;

                    $lang['object_id']  	= $product_id;

                    $lang['object_type']  	= 'products';

                    $model->add($lang);
                }

            }

            $model->settable('products');
        }

        $model->settable('products');

        return $product_id;
    }

    static public function delete( $productID = 0, $trash = false ) {

        $ci =& get_instance();

        $productID = (int)Str::clear($productID);

        if( $productID == 0 ) return false;

        $model = get_model('products')->settable('products');

        $product  = static::get(['where' => array('id' => $productID, 'type <>' => 'trash', 'trash <>' => -1)]);

        if(have_posts($product)) {

            $ci->data['module']   = 'products';

            //nếu bỏ vào thùng rác
            if( $trash == true ) {

                /**
                 * @since 2.5.0 add action delete_product_trash
                 */
                do_action('delete_product_trash', $productID );

                if($model->update_where(array('trash' => 1), array('id' => $productID))) {

                    do_action('delete_product_trash_success', $productID );

                    return [$productID];
                }

                return false;
            }
            /**
             * @since 2.5.0 add action delete_product
             */
            do_action('delete_product', $productID );

            if($model->delete_where(['id'=> $productID])) {

                do_action('delete_product_success', $productID );

                //delete language
                $model->settable('language');
                $model->delete_where(['object_id'=> $productID, 'object_type' => 'products']);

                //delete router
                $model->settable('routes');
                $model->delete_where(['object_id'=> $productID, 'object_type' => 'products']);

                //delete gallerys
                Gallery::deleteItemByObject($productID, 'products');
                Metadata::deleteByMid('product', $productID);
                //delete menu
                $model->settable('menu');

                $model->delete_where(['object_id'=> $productID, 'object_type' => 'product']);

                //xóa liên kết
                $model->settable('relationships');

                $model->delete_where(['object_id'=> $productID, 'object_type' => 'products']);

                if($product->type != 'variations') {

                    //Xóa sản phẩm biến thể
                    $variations = Product::gets(['where' => ['parent_id' => $productID, 'type' => 'variations']]);

                    foreach ($variations as $variation) {

                        static::delete($variation->id);
                    }
                }

                CacheHandler::delete('breadcrumb_products_detail_'.$productID, true);

                return [$productID];
            }
        }

        return false;
    }

    static public function deleteList( $productID = [], $trash = false ) {

        if(have_posts($productID)) {

            $model = get_model('products')->settable('products');

            if( $trash == true ) {

                do_action('delete_product_list_trash', $productID );

                if($model->update_where_in(['field' => 'id', 'data' => $productID], ['trash' => 1])) {

                    do_action('delete_product_list_trash_success', $productID );

                    return $productID;
                }

                return false;
            }

            $products = static::gets(['where_in' => ['field' => 'id', 'data' => $productID]]);

            if($model->delete_where_in(['field' => 'id', 'data' => $productID])) {

                $where_in = ['field' => 'object_id', 'data' => $productID];

                do_action('delete_product_list_success', $productID );

                //delete language
                $model->settable('language')->delete_where_in($where_in, ['object_type' => 'products']);

                //delete router
                $model->settable('routes')->delete_where_in($where_in, ['object_type' => 'products']);

                //delete router
                foreach ($products as $key => $product) {
                    Gallery::deleteItemByObject($product->id, 'products');
                    Metadata::deleteByMid('product', $product->id);
                    CacheHandler::delete('breadcrumb_products_detail_'.$product->id, true);
                }

                //delete menu
                $model->settable('menu')->delete_where_in($where_in, ['object_type' => 'product']);

                //xóa liên kết
                $model->settable('relationships')->delete_where_in($where_in, ['object_type' => 'products']);

                //Xóa sản phẩm biến thể
                foreach ($products as $key => $product) {
                    if($product->type == 'variations') continue;
                    $variations = static::gets(['where' => ['parent_id' => $product->id, 'type' => 'variations']]);
                    foreach ($variations as $variation) {
                        static::delete($variation->id);
                    }
                }

                return $productID;
            }
        }

        return false;
    }

    static public function getMeta( $product_id, $key = '', $single = true) {
        return Metadata::get('product', $product_id, $key, $single);
    }

    static public function updateMeta($product_id, $meta_key, $meta_value) {
        return Metadata::update('product', $product_id, $meta_key, $meta_value);
    }

    static public function deleteMeta($product_id, $meta_key = '', $meta_value = '') {
        return Metadata::delete('product', $product_id, $meta_key, $meta_value);
    }
}

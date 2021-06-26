<?php
Class Brands {

    static public function get($args = []) {

        if(is_numeric($args)) $args = array( 'where' => array('id' => (int)$args ) );

        if(!have_posts($args)) $args = [];

        $args = array_merge( array('where' => [], 'params' => [] ), $args );

        $brands = get_model()->settable('brands')->settable_metabox('brands_metadata')->get_data( $args, 'brands' );

        return apply_filters('get_brands', $brands, $args);
    }

    static public function getBy( $field, $value, $params = [] ) {

        $field = Str::clear( $field );

        $value = Str::clear( $value );

        $args = array( 'where' => array( $field => $value ) );

        if(have_posts($params)) $arg['params'] = $params;

        return apply_filters('get_brands_by', static::get($args), $field, $value );
    }

    static public function gets( $args = [] ) {

        $model 	= get_model()->settable('brands')->settable_metabox('metabox');

        if(!have_posts($args)) $args = [];

        $args = array_merge(['where' => [], 'params' => []], $args );

        $brands = $model->gets_data($args, 'brands');

        return apply_filters( 'gets_brands', $brands, $args );
    }

    static public function getsBy( $field, $value, $params = [] ) {

        $field = Str::clear( $field );

        $value = Str::clear( $value );

        $args = ['where' => array( $field => $value )];

        if( have_posts($params) ) $arg['params'] = $params;

        return apply_filters( 'gets_brands_by', static::gets($args), $field, $value );
    }

    static public function count( $args = [] ) {

        if( is_numeric($args) ) $args = array( 'where' => array('id' => (int)$args ) );

        if( !have_posts($args) ) $args = [];

        $args = array_merge( array('where' => [], 'params' => [] ), $args );

        $model = get_model()->settable('brands')->settable_metabox('brands_metadata');

        $brands = $model->count_data($args, 'brands');

        return apply_filters('count_brands', $brands, $args );
    }

    static public function insert( $brands = [] ) {

        $ci =& get_instance();

        $model = get_model('home')->settable('brands');

        $user = Auth::user();

        if (!empty($brands['id'])) {

            $id             = (int)$brands['id'];

            $update         = true;

            $old_brands = static::get($id);

            if(!$old_brands) return new SKD_Error( 'invalid_brands_id', __( 'ID thương hiệu không chính xác.' ) );

            $user_updated = (have_posts($user)) ? $user->id : 0;

            $user_created = $old_brands->user_created;

            $slug = empty($brands['slug']) ? $old_brands->slug : Str::slug($brands['slug']);

            if( $slug != $old_brands->slug ) $slug = $ci->edit_slug( $slug , $id, $model );
            $brands['name']               = (!empty($brands['name'])) ? $brands['name'] : $old_brands->name;
            $brands['excerpt']            = (!empty($brands['excerpt'])) ? $brands['excerpt'] : $old_brands->excerpt;
            $brands['seo_title']          = (!empty($brands['seo_title'])) ? $brands['seo_title'] : $old_brands->seo_title;
            $brands['seo_description']    = (!empty($brands['seo_description'])) ? $brands['seo_description'] : $old_brands->seo_description;
            $brands['seo_keywords']       = (!empty($brands['seo_keywords'])) ? $brands['seo_keywords'] : $old_brands->seo_keywords;
            $brands['image']              = (!empty($brands['image'])) ? $brands['image'] : $old_brands->image;
            $brands['order']              = (isset($brands['order'])) ? $brands['order'] : $old_brands->order;
        }
        else {
            $update = false;
            $user_updated = 0;
            $user_created = (have_posts($user)) ? $user->id : 0;
        }

        $language = Language::default();

        if(!empty($brands[$language]['name'])) {
            $brands['name'] = xss_clean($brands[$language]['name']);
        }
        if(!empty($brands[$language]['excerpt'])) {
            $brands['excerpt'] = xss_clean($brands[$language]['excerpt']);
        }

        if(empty($brands['language'])) {
            foreach (Language::listKey() as $key) {
                if( $key != Language::default()) {
                    if(!empty($brands[$key]['name']))       $brands['language'][$key] = $brands[$key];
                    if(!empty($brands[$key]['excerpt']))    $brands['language'][$key] = $brands[$key];
                }
            }
        }

        if(empty($brands['name'])) return new SKD_Error('empty_brands_name', __('Không thể cập nhật nhà sản xuất khi tên để trống.') );

        $name      = Str::clear($brands['name']);

        $pre_name  = apply_filters( 'pre_brands_name', $name );

        $name      = trim( $pre_name );

        $seo_title = (isset($brands['seo_title'])) ? Str::clear($brands['seo_title']) : '';

        if(!$update) {

            $slug = $ci->create_slug(Str::clear($brands['name']), $model);

            if(empty($seo_title)) $seo_title = $name;
        }

        $order              = (isset($brands['order'])) ? (int)$brands['order'] : 0;

        $excerpt            = (isset($brands['excerpt'])) ? xss_clean($brands['excerpt']) : '';

        $seo_description    = (isset($brands['seo_description'])) ? Str::clear($brands['seo_description']) : '';

        $seo_keywords       = (isset($brands['seo_keywords'])) ? Str::clear($brands['seo_keywords']) : '';

        $image              = (isset($brands['image'])) ? Str::clear($brands['image']) : '';

        $image              = FileHandler::handlingUrl($image);

        $data = compact( 'name','slug', 'excerpt', 'seo_title', 'seo_description', 'seo_keywords', 'image', 'order', 'user_created', 'user_updated' );

        $data = apply_filters( 'pre_insert_brands_data', $data, $brands, $update ? $old_brands : null );

        $language  = !empty( $brands['language'] ) ? $brands['language'] : [];

        if ( $update ) {

            $model->settable('brands')->update_where( $data, compact( 'id' ) );

            $brands_id = (int) $id;
            /*=============================================================
            ROUTER
            =============================================================*/
            $router['object_type']  = 'brands';

            $router['object_id']    = $brands_id;

            if( $model->settable('routes')->update_where(['slug' => $slug], $router ) == 0 ) {

                $router['slug']         = $slug;

                $router['directional']  = 'brands';

                $router['controller']   = 'frontend_home/home/page/';

                $router['callback']     = 'brands_frontend';

                $model->settable('routes')->add($router);
            }

            if( have_posts($language) ) {

                foreach ($language as $key => $val) {

                    $lang['language']       = $key;

                    $lang['object_id']      = $brands_id;

                    $lang['object_type']    = 'brands';

                    if($model->settable('language')->count_where($lang)) {

                        $model->settable('language')->update_where([
                            'name'      => Str::clear($val['name']),
                            'excerpt'   => xss_clean($val['excerpt']),
                        ], $lang);
                    }
                    else {

                        $lang['name']          = Str::clear($val['name']);

                        $lang['excerpt']       = xss_clean($val['excerpt']);

                        $model->settable('language')->add($lang);
                    }
                }
            }

        } else {

            $brands_id = $model->settable('brands')->add( $data );

            /*=============================================================
            ROUTER
            =============================================================*/
            $router['slug']         = $slug;

            $router['directional']  = 'brands';

            $router['controller']   = 'frontend_home/home/page/';

            $router['callback']     = 'brands_frontend';

            $router['object_id']    = $brands_id;

            $model->settable('routes')->add($router);

            /*=============================================================
            LANGUAGE
            =============================================================*/
            if( have_posts($language) ) {

                foreach ($language as $key => $val) {

                    $lang['name']          = Str::clear($val['name']);

                    $lang['language']       = $key;

                    $lang['object_id']      = $brands_id;

                    $lang['object_type']    = 'brands';

                    $model->settable('language')->add($lang);
                }
            }
        }

        $brands_id  = apply_filters( 'after_insert_brands', $brands_id, $brands, $data, $update ? $old_brands : null  );

        return $brands_id;
    }

    static public function delete( $brandsID = 0) {

        $ci =& get_instance();

        $brandsID = (int)Str::clear($brandsID);

        if( $brandsID == 0 ) return false;

        $model = get_model('home')->settable('brands');

        $brands  = static::get( $brandsID );

        if( have_posts($brands) ) {

            $ci->data['module']   = 'brands';

            do_action('delete_brands', $brandsID );

            if($model->delete_where(['id'=> $brandsID])) {

                do_action('delete_brands_success', $brandsID );

                //delete language
                $model->settable('language')->delete_where(['object_id'=> $brandsID, 'object_type' => 'brands']);
                //delete router
                $model->settable('routes')->delete_where(['object_id'=> $brandsID, 'object_type' => 'brands']);
                //delete gallerys
                Gallery::deleteItemByObject($brandsID, 'brands');
                Metadata::deleteByMid('brands', $brandsID);
                //delete menu
                $model->settable('menu')->delete_where(['object_id'=> $brandsID, 'object_type' => 'brands']);
                //xóa liên kết
                $model->settable('relationships')->delete_where(['object_id'=> $brandsID, 'object_type' => 'brands']);

                return [$brandsID];
            }
        }

        return false;
    }

    static public function deleteList( $brandsID = []) {

        if(have_posts($brandsID)) {

            $model      = get_model('home')->settable('brands');

            $brandss = static::gets(['where_in' => ['field' => 'id', 'data' => $brandsID]]);

            if($model->delete_where_in(['field' => 'id', 'data' => $brandsID])) {

                $where_in = ['field' => 'object_id', 'data' => $brandsID];

                do_action('delete_brands_list_trash_success', $brandsID );

                //delete language
                $model->settable('language')->delete_where_in($where_in, ['object_type' => 'brands']);

                //delete router
                $model->settable('routes')->delete_where_in($where_in, ['object_type' => 'brands']);

                //delete router
                foreach ($brandss as $key => $brands) {
                    Gallery::deleteItemByObject($brands->id, 'brands');
                    Metadata::deleteByMid('brands', $brands->id);
                }

                //delete menu
                $model->settable('menu')->delete_where_in($where_in, ['object_type' => 'brands']);

                //xóa liên kết
                $model->settable('relationships')->delete_where_in($where_in, ['object_type' => 'brands']);

                return $brandsID;
            }
        }

        return false;
    }

    static public function getMeta( $brands_id, $key = '', $single = true) {
        return Metadata::get('brands', $brands_id, $key, $single);
    }

    static public function updateMeta($brands_id, $meta_key, $meta_value) {
        return Metadata::update('brands', $brands_id, $meta_key, $meta_value);
    }

    static public function deleteMeta($brands_id, $meta_key = '', $meta_value = '') {
        return Metadata::delete('brands', $brands_id, $meta_key, $meta_value);
    }

    static public function getsOption($args = []) {

        $brands = static::gets($args);

        $options = ['Chọn nhà sản xuất'];

        foreach ($brands as $key => $brand) {
            $options[$brand->id] = $brand->name;
        }
        return apply_filters( 'gets_brands_option', $options );
    }
}
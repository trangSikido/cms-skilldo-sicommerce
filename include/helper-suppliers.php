<?php
Class Suppliers {

    static public function get($args = []) {

        $model = get_model()->settable('suppliers')->settable_metabox('metabox');

        if(is_numeric($args)) $args = array( 'where' => array('id' => (int)$args));

        if(!have_posts($args)) $args = [];

        $args = array_merge( array('where' => [], 'params' => [] ), $args );

        $suppliers = $model->get_data( $args, 'suppliers' );

        return apply_filters('get_suppliers', $suppliers, $args);
    }

    static public function getBy( $field, $value, $params = [] ) {

        $field = Str::clear( $field );

        $value = Str::clear( $value );

        $args = array( 'where' => array( $field => $value));

        if(have_posts($params)) $arg['params'] = $params;

        return apply_filters('get_suppliers_by', static::get($args), $field, $value );
    }

    static public function gets( $args = [] ) {

        $model 	= get_model()->settable('suppliers')->settable_metabox('metabox');

        if(!have_posts($args)) $args = [];

        $args = array_merge(['where' => [], 'params' => []], $args );

        $suppliers = $model->gets_data($args, 'suppliers');

        return apply_filters( 'gets_suppliers', $suppliers, $args );
    }

    static public function getsBy( $field, $value, $params = [] ) {

        $field = Str::clear( $field );

        $value = Str::clear( $value );

        $args = ['where' => array( $field => $value )];

        if( have_posts($params) ) $arg['params'] = $params;

        return apply_filters( 'gets_suppliers_by', static::gets($args), $field, $value );
    }

    static public function count( $args = [] ) {

        if( is_numeric($args) ) $args = array( 'where' => array('id' => (int)$args));

        if( !have_posts($args) ) $args = [];

        $args = array_merge( array('where' => [], 'params' => [] ), $args );

        $model = get_model()->settable('suppliers')->settable_metabox('suppliers_metadata');

        $suppliers = $model->count_data($args, 'suppliers');

        return apply_filters('count_suppliers', $suppliers, $args );
    }

    static public function insert( $suppliers = [] ) {
        
        $ci =& get_instance();

        $model = get_model('home')->settable('suppliers');

        $user = Auth::user();

        if (!empty($suppliers['id']) ) {

            $id             = (int) $suppliers['id'];

            $update        = true;

            $old_suppliers = static::get($id);

            if(!$old_suppliers) return new SKD_Error( 'invalid_suppliers_id', __( 'ID bài viết không chính xác.'));

            $user_updated = (have_posts($user)) ? $user->id : 0;

            $user_created = $old_suppliers->user_created;

            $suppliers['name'] = (!empty($suppliers['name'])) ? $suppliers['name'] : $old_suppliers->name;

            $suppliers['firstname'] = (!empty($suppliers['firstname'])) ? $suppliers['firstname'] : $old_suppliers->firstname;

            $suppliers['lastname'] = (!empty($suppliers['lastname'])) ? $suppliers['lastname'] : $old_suppliers->lastname;

            $suppliers['email'] = (!empty($suppliers['email'])) ? $suppliers['email'] : $old_suppliers->email;

            $suppliers['phone'] = (!empty($suppliers['phone'])) ? $suppliers['phone'] : $old_suppliers->phone;

            $suppliers['address'] = (!empty($suppliers['address'])) ? $suppliers['address'] : $old_suppliers->address;

            $suppliers['seo_title'] = (!empty($suppliers['seo_title'])) ? $suppliers['seo_title'] : $old_suppliers->seo_title;

            $suppliers['seo_description'] = (!empty($suppliers['seo_description'])) ? $suppliers['seo_description'] : $old_suppliers->seo_description;

            $suppliers['seo_keywords'] = (!empty($suppliers['seo_keywords'])) ? $suppliers['seo_keywords'] : $old_suppliers->seo_keywords;

            $suppliers['image'] = (!empty($suppliers['image'])) ? $suppliers['image'] : $old_suppliers->image;
        }
        else {
            $update = false;

            $user_updated = 0;

            $user_created = ( have_posts($user) ) ? $user->id : 0;
        }

        if(empty($suppliers['name']) ) {
            $language = $ci->language;
            if(!empty($suppliers[$language['default']]['name'])) {
                $suppliers['name'] = $suppliers[$language['default']]['name'];
            }
        }

        if(empty($suppliers['language']) ) {
            foreach (Language::listKey() as $key) {
                if($key != Language::default()) {
                    if(!empty($suppliers[$key]['name'])) {
                        $suppliers['language'][$key] = $suppliers[$key];
                    }
                }
            }
        }

        if(!$update) {
            if (empty($suppliers['name'])) return new SKD_Error('empty_suppliers_name', __('Không thể cập nhật nhà sản xuất khi tên để trống.') );
            $slug = $ci->create_slug( Str::clear( $suppliers['name'] ), $model );
        }
        else {
            if (empty( $suppliers['name'] )) return new SKD_Error('empty_suppliers_name', __('Không thể thêm nhà sản xuất khi tên để trống.') );
            $slug = empty( $suppliers['slug']) ? $old_suppliers->slug : Str::slug($suppliers['slug']);
            if( $slug != $old_suppliers->slug ) $slug = $ci->edit_slug( $slug , $id, $model );
        }

        $name      = Str::clear( $suppliers['name'] );

        $pre_name  = apply_filters( 'pre_suppliers_name', $name );

        $name      = trim($pre_name);

        $firstname = (!empty($suppliers['firstname'])) ? Str::clear($suppliers['firstname']) : '';

        $lastname = (!empty($suppliers['lastname'])) ? Str::clear($suppliers['lastname']) : '';

        $email = (!empty($suppliers['email'])) ? Str::clear($suppliers['email']) : '';

        $phone = (!empty($suppliers['phone'])) ? Str::clear($suppliers['phone']) : '';

        $address = (!empty($suppliers['address'])) ? Str::clear($suppliers['address']) : '';

        $seo_title = (!empty($suppliers['seo_title'])) ? Str::clear($suppliers['seo_title']) : $name;

        $seo_description = (!empty($suppliers['seo_description'])) ? Str::clear($suppliers['seo_description']) : '';

        $seo_keywords   = (!empty($suppliers['seo_keywords'])) ? Str::clear($suppliers['seo_keywords']) : '';

        $image          = (!empty($suppliers['image'])) ? FileHandler::handlingUrl(Str::clear($suppliers['image'])) : '';

        $data = compact( 'name', 'slug', 'firstname', 'lastname', 'email', 'phone', 'address', 'seo_title', 'seo_description', 'seo_keywords', 'image', 'user_created', 'user_updated' );

        $data = apply_filters( 'pre_insert_suppliers_data', $data, $suppliers, $update ? (int) $id : null);

        $language = !empty($suppliers['language']) ? $suppliers['language'] : [];

        if ($update) {

            $model->settable('suppliers')->update_where( $data, compact( 'id'));

            $suppliers_id = (int) $id;
            /*=============================================================
            ROUTER
            =============================================================*/
            $model->settable('routes');

            $router['object_type']  = 'suppliers';

            $router['object_id']    = $suppliers_id;

            if($model->update_where(['slug' => $slug], $router) == 0 ) {
                $router['slug']         = $slug;
                $router['directional']  = 'suppliers';
                $router['controller']   = 'frontend_home/home/page/';
                $router['callback']     = 'suppliers_frontend';
                $model->add($router);
            }
            if(have_posts($language)) {
                $model->settable('language');
                foreach ($language as $key => $val) {

                    $lang['language']       = $key;
                    $lang['object_id']      = $suppliers_id;
                    $lang['object_type']    = 'suppliers';

                    if($model->count_where($lang)) {
                        $model->update_where(['name' => Str::clear($val['name'])], $lang);
                    }
                    else {
                        $lang['name']          = Str::clear($val['name']);
                        $model->add($lang);
                    }
                }
            }
        }
        else {
            $suppliers_id = $model->settable('suppliers')->add( $data );
            /*=============================================================
            ROUTER
            =============================================================*/
            $model->settable('routes');
            $router['slug']         = $slug;
            $router['directional']  = 'suppliers';
            $router['controller']   = 'frontend_home/home/page/';
            $router['callback']     = 'suppliers_frontend';
            $router['object_id']    = $suppliers_id;
            $model->add($router);
            /*=============================================================
            LANGUAGE
            =============================================================*/
            if( have_posts($language) ) {
                $model->settable('language');
                foreach ($language as $key => $val) {
                    $lang['name']          = Str::clear($val['name']);
                    $lang['language']       = $key;
                    $lang['object_id']      = $suppliers_id;
                    $lang['object_type']    = 'suppliers';
                    $model->add($lang);
                }
            }
        }
        $model->settable('suppliers');
        $suppliers_id  = apply_filters( 'after_insert_suppliers', $suppliers_id, $suppliers, $data, $update ? (int) $id : null  );
        return $suppliers_id;
    }

    static public function delete( $suppliersID = 0) {

        $ci =& get_instance();

        $suppliersID = (int)Str::clear($suppliersID);

        if( $suppliersID == 0 ) return false;

        $model = get_model('home')->settable('suppliers');

        $suppliers  = static::get( $suppliersID );

        if(have_posts($suppliers) ) {

            $ci->data['module']   = 'suppliers';

            do_action('delete_suppliers', $suppliersID );

            if($model->delete_where(['id'=> $suppliersID])) {
                do_action('delete_suppliers_success', $suppliersID );
                //delete language
                $model->settable('language')->delete_where(['object_id'=> $suppliersID, 'object_type' => 'suppliers']);
                //delete router
                $model->settable('routes')->delete_where(['object_id'=> $suppliersID, 'object_type' => 'suppliers']);
                //delete gallerys
                Gallery::deleteItemByObject($suppliersID, 'suppliers');
                Metadata::deleteByMid('suppliers', $suppliersID);
                //delete menu
                $model->settable('menu')->delete_where(['object_id'=> $suppliersID, 'object_type' => 'suppliers']);
                //xóa liên kết
                $model->settable('relationships')->delete_where(['object_id'=> $suppliersID, 'object_type' => 'suppliers']);
                return [$suppliersID];
            }
        }

        return false;
    }

    static public function deleteList( $suppliersID = []) {

        if(have_posts($suppliersID)) {

            $model      = get_model('home')->settable('suppliers');

            $supplierss = static::gets(['where_in' => ['field' => 'id', 'data' => $suppliersID]]);

            if($model->delete_where_in(['field' => 'id', 'data' => $suppliersID])) {

                $where_in = ['field' => 'object_id', 'data' => $suppliersID];

                do_action('delete_suppliers_list_trash_success', $suppliersID );

                //delete language
                $model->settable('language')->delete_where_in($where_in, ['object_type' => 'suppliers']);

                //delete router
                $model->settable('routes')->delete_where_in($where_in, ['object_type' => 'suppliers']);

                //delete router
                foreach ($supplierss as $key => $suppliers) {
                    Gallery::deleteItemByObject($suppliers->id, 'suppliers');
                    Metadata::deleteByMid('suppliers', $suppliers->id);
                }

                //delete menu
                $model->settable('menu')->delete_where_in($where_in, ['object_type' => 'suppliers']);

                //xóa liên kết
                $model->settable('relationships')->delete_where_in($where_in, ['object_type' => 'suppliers']);

                return $suppliersID;
            }
        }

        return false;
    }

    static public function getMeta( $suppliers_id, $key = '', $single = true) {
        return Metadata::get('suppliers', $suppliers_id, $key, $single);
    }

    static public function updateMeta($suppliers_id, $meta_key, $meta_value) {
        return Metadata::update('suppliers', $suppliers_id, $meta_key, $meta_value);
    }

    static public function deleteMeta($suppliers_id, $meta_key = '', $meta_value = '') {
        return Metadata::delete('suppliers', $suppliers_id, $meta_key, $meta_value);
    }

    static public function getsOption($args = []) {

        $suppliers = static::gets($args);

        $options = ['Chọn nhà sản xuất'];

        foreach ($suppliers as $key => $brand) {
            $options[$brand->id] = $brand->name;
        }
        return apply_filters( 'gets_suppliers_option', $options );
    }
}
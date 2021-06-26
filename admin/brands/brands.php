<?php
include_once 'action-bar.php';
include_once 'form.php';
include_once 'ajax.php';
include_once 'menu.php';
include_once 'popover.php';
include_once 'table.php';

Class Brands_Admin {

    function __construct() {
        add_filter('delete_object_brands', [$this, 'delete'], 1, 3 );
    }

    public static function page() {

        $view = InputBuilder::get('view');

        if(empty($view)) {

            $args = [
                'items' => Brands::gets(),
                'table' => 'brands',
                'model' => get_model(),
                'module'=> 'brands',
            ];

            $table_list = new skd_brands_list_table($args);

            include SCMC_PATH.'admin/views/brands/html-brands-index.php';
        }
        else if($view == 'add') {
            $form = brands_admin_form_input();
            include SCMC_PATH.'admin/views/brands/html-brands-save.php';
        }
        else if($view == 'edit') {
            $form   = brands_admin_form_input();
            $id     = (int)InputBuilder::get('id');
            $object = Brands::get($id);
            if(have_posts($object)) {
                $object->lang[Language::default()]['name'] = $object->name;
                $languages = [];
                if(Language::hasMulti()) {
                    $model = get_model('home')->settable('language');
                    $languages = $model->gets_where(array('object_id' => $object->id, 'object_type' => 'brands'));
                    foreach ($languages as $key => $lang) {
                        $object->lang[$lang->language]['name']      = $lang->name;
                        $object->lang[$lang->language]['excerpt']   = $lang->excerpt;
                    }
                }
                $form_field = $form['field'];
                foreach($form_field as $key => $field) {
                    //gán giá trị cho các field bình thường
                    if( isset($object->{$field['field']}) ) {
                        $form_field[$key]['value'] = $object->{$field['field']};
                    }
                    //gán giá trị cho các field đa ngôn ngữ
                    else if( isset($field['lang']) ) {
                        $temp = str_replace($field['lang'].'[', '',$field['field']);
                        $temp = str_replace(']', '',$temp);
                        if( have_posts($languages) ) {
                            foreach ($languages as $k => $value) {
                                if($field['lang'] == $value->language ) {
                                    if(isset($value->$temp))  {
                                        $form_field[$key]['value'] = $value->$temp;
                                        break;
                                    }
                                }
                                else if(isset($object->$temp)) {
                                    $form_field[$key]['value'] = $object->$temp;
                                }
                            }
                        } else if(isset($object->$temp)) {
                            $form_field[$key]['value'] = $object->$temp;
                        }
                    }
                }
                $form['field'] = $form_field;
                include SCMC_PATH.'admin/views/brands/html-brands-save.php';
            }
        }
    }

    public static function delete($res, $table, $id) {
        if(is_numeric($id)) {
            $res = Brands::delete($id);
        }
        else if(have_posts($id)) {
            $res = Brands::deleteList($id);
        }
        return $res;
    }
}
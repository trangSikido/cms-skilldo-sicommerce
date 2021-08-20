<?php defined('BASEPATH') OR exit('No direct script access allowed');

class products extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model($this->data['module'].'_model');
        $this->data['dropdown']['category_id'] = ProductCategory::gets(array('mutilevel' => 'option'));
        if(have_posts($this->data['dropdown']['category_id'])) {
            unset($this->data['dropdown']['category_id'][0]);
        }

        foreach ($this->taxonomy['list_cat_detail'] as $taxonomy_key => $taxonomy_value) {
            if( $taxonomy_value['post_type'] == 'products' ) {
                $this->data['dropdown']['taxonomy['.$taxonomy_key.']'] = PostCategory::gets( array('mutilevel' => $taxonomy_key) );
                unset($this->data['dropdown']['taxonomy['.$taxonomy_key.']'][0]);
            }
        }
    }

    public function index() {
        $model      = $this->data['module'].'_model';
        $keyword    = InputBuilder::get('keyword');
        $category_id= (InputBuilder::get('category')!=null)?(int)InputBuilder::get('category'):0;
        $collection = (int)InputBuilder::get('collection');
        $trash      = (InputBuilder::get('status') == 'trash')?1:0;
        $args       = [
            'where' => array('trash' => $trash)
        ];
        if($category_id != 0) {
            $args['where_category'] = $category_id;
        }

        if(!empty($collection) && $collection >= 1 && $collection <= 3) {
            $args['where']['status'.$collection] = 1;
        }
        /*===================================================
        TÍNH TỔNG SỐ DỮ LIỆU SẼ CÓ
        ====================================================*/
        $total_rows = 0;
        /* search keyword */
        if(!empty($keyword)) {
            $args['where_like'] = array( 'title' => array($keyword));
        }

        $args = apply_filters('admin_product_controllers_index_args_count', $args);

        $total_rows = Product::count($args);
        /*===================================================
        PHÂN TRANG
        ====================================================*/
        $url        = Url::admin().$this->data['module'].'?page={page}';
        if(!empty($keyword))    $url .= '&keyword='.$keyword;
        if($trash == 1)         $url .= '&status=trash';
        if($category_id != 0)   $url .= '&category='.$category_id;
        if($collection != 0)    $url .= '&collection='.$collection;
        $url = apply_filters('admin_product_controllers_pagination_url', $url);
        $this->data['pagination'] = pagination($total_rows, $url, option::get('admin_pg_page',10));
        /*===================================================
        LẤY DỮ LIỆU
        ====================================================*/
        $params = array(
            'limit'  => option::get('admin_pg_page',10),
            'start'  => $this->data['pagination']->getoffset(),
            'orderby'=> 'order, created desc',
        );

        $args['params'] = $params;

        $args = apply_filters('admin_product_controllers_index_args', $args);

        $this->data['objects'] = Product::gets($args);

        if(have_posts($this->data['objects'])) {

            $cart_version = Option::get('product_version');

            if(empty($cart_version)) {
                $cart_version = Option::get('wcmc_version');
            }

            foreach ($this->data['objects'] as $key => $val) {

                if( version_compare($cart_version, '2.0.5') <= 0) {
                    $this->data['objects'][$key]->categories = ProductCategory::gets(['product_id' => $val->id, 'params' => array('select' => 'name, slug')]);
                }
                else {
                    $this->data['objects'][$key]->categories = $this->$model->gets_relationship_join_categories($val->id, 'products', 'products_categories');
                }
            }
        }

        $this->data['trash']    =  Product::count(array('where' => ['trash' => 1]));

        $this->data['public']   =  Product::count(array('where' => ['public' => 1, 'trash' => 0]));

        $this->data['total']    =  $total_rows;

        /* tạo table */
        $args = array(
            'items' => $this->data['objects'],
            'table' => $this->$model->gettable(),
            'model' => $this->$model,
            'module'=> $this->data['module'],
        );

        $class_table = 'skd_product_list_table';

        $this->data['table_list'] = new $class_table($args);
        /* hiển thị*/
        $this->template->render();
    }

    public function add() {
        $model     = $this->data['module'].'_model';
        if(Auth::hasCap('product_edit') ) {
            $this->$model->settable('products');
            $this->getFields();
            $this->formAction();
            $this->template->render('products-save');
        }
        else $this->template->error('404');
    }

    public function edit($slug = '') {
        $model = $this->data['module'].'_model';
        if(Auth::hasCap('product_edit')) {

            $this->data['object'] = Product::get( array( 'where' => array('slug' => $slug) ) ); // lấy dữ liệu page

            if( have_posts($this->data['object']) ) {

                $this->$model->settable('relationships');

                $categories = $this->$model->gets_where(array('object_id' => $this->data['object']->id, 'object_type' => 'products'), array('select' => 'object_id, category_id, value'));

                foreach ($categories as $key => $val) {

                    if( $val->value == null ||  $val->value == 'products_categories' ) {

                        $this->data['object']->category_id[] = $val->category_id;
                    }
                    else $this->data['object']->{'taxonomy['.$val->value.']'}[] = $val->category_id;
                }

                $this->$model->settable('products');
                $this->getFields();
                $this->formAction($this->data['object']);
                $this->setValueFields($this->data['object']);

                $this->template->render('products-save');
            }
            else {
                $this->template->set_message(notice('danger', 'Bạn đang muốn sửa một thứ không tồn tại. Có thể nó đã bị xóa?'));

                $this->template->error('error-404');
            }
        }
        else $this->template->error('404');
    }
}
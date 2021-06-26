<?php defined('BASEPATH') OR exit('No direct script access allowed');

class products_categories extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->data['group']    = 'products';
        $this->load->model($this->data['module'].'_model');
        $this->data['dropdown']['parent_id'] = ProductCategory::gets( array('mutilevel' => 'option') );
        $this->data['dropdown']['parent_id'][0] = 'Chọn danh mục cha';
    }

    public function index() {

        $model = $this->data['module'].'_model';

        $this->getFields();

        $this->formAction();

        $keyword    = InputBuilder::get('keyword');

        $category_id = (!empty(InputBuilder::get('category'))) ? (int)InputBuilder::get('category') : 0;

        $args['where'] = [];

        $args['params'] = array('orderby' => 'order, created desc');

        if(!empty($keyword)) {
            $args['where_like'] = array( 'name' => array($keyword));
        }
        else {
            $args['tree'] = array( 'parent_id' => $category_id );
        }

        $this->data['objects'] =  ProductCategory::gets( $args );

        $this->data['total']   =  ProductCategory::count( $args['where'] );

        /* tạo table */
        $args = array(
            'items' => $this->data['objects'],
            'table' => $this->$model->gettable(),
            'model' => $this->$model,
            'module'=> $this->data['module'],
        );

        $class_table = 'skd_product_category_list_table';

        $this->data['table_list'] = new $class_table($args);

        $this->template->render();
    }

    public function add() {

        $model = $this->data['module'].'_model';

        if( Auth::hasCap('product_cate_edit') ) {
            $this->getFields();
            $this->formAction();
            $this->template->render('products_categories-save');
        }
        else $this->template->error('404');
    }

    public function edit($slug = '') {

        $model = $this->data['module'].'_model';

        if( Auth::hasCap('product_cate_edit') ) {

            $this->data['object'] = ProductCategory::get(array('slug' => $slug)); // lấy dữ liệu page

            if(have_posts($this->data['object'])) {
                $this->getFields();
                $this->formAction($this->data['object']);
                $this->setValueFields($this->data['object']);
                $this->template->render('products_categories-save');
            }
            else {
                $this->template->set_message(notice('danger', 'Bạn đang muốn sửa một thứ không tồn tại. Có thể nó đã bị xóa?'));
                $this->template->render('error-404');
            }
        }
        else $this->template->error('404');
    }
}
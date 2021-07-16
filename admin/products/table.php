<?php
//product
class skd_product_list_table extends skd_object_list_table {

    function get_columns() {

        $this->_column_headers = [
            'cb'        => 'cb',
            'image'     => 'Hình',
            'title'     => 'Tiêu Đề',
            'categories'=> 'Chuyên mục',
            'price'     => 'Giá',
            'price_sale'=> 'Giá khuyến mãi',
            'public'    => 'Hiển thị',
            'collection'=> 'Nhóm',
            'order'     => 'Thứ tự',
            'action'    => 'Hành Động',
        ];

        $this->_column_headers = apply_filters( "manage_product_columns", $this->_column_headers );

        return $this->_column_headers;
    }

    function column_default( $item, $column_name ) {
        do_action( 'manage_product_custom_column', $column_name, $item );
    }

    function column_title($item) {
        ?>
        <h3><?= $item->title;?></h3>
        <?php do_action('admin_product_table_column_title', $item);?>
        <div class="action-hide">
            <span>ID : <?= $item->id;?></span> |
            <a href="<?= Url::permalink($item->slug);?>" target="_blank" data-toggle="tooltip" data-placement="top" title="Xem"><i class="fa fa-eye"></i></a>
        </div>
        <?php
    }

    function column_categories($item) {
        $str = '';
        foreach ($item->categories as $key => $value) {
            $str .= sprintf('<a href="%s">%s</a>, ', URL_ADMIN.'/products/products_categories/edit/'.$value->slug, $value->name);
        }
        echo trim($str,', ');
    }

    function column_price($item, $column_name, $module, $table) {
        $str = number_format($item->price);
        echo '<a href="#" data-pk="'.$item->id.'" data-name="price" class="js_products_price__update" >'.$str.'</a>';
    }

    function column_price_sale($item, $column_name, $module, $table) {
        $str = number_format($item->price_sale);
        echo '<a href="#" data-pk="'.$item->id.'" data-name="price_sale" class="js_products_price__update" >'.$str.'</a>';
    }

    function column_collection($item, $column_name) {

        $source = [
            ['value' => 'status1', 'text' => 'Yêu thích'],
            ['value' => 'status2', 'text' => 'Bán chạy'],
            ['value' => 'status3', 'text' => 'Nổi bật'],
        ];

        $value  = [];

        $text   = '';

        if($item->status1 == 1) {
            $value[] = 'status1'; $text .= '<span class="label label-success">Yêu thích</span>';
        }
        if($item->status2 == 1) {
            $value[] = 'status2'; $text .= ', <span class="label label-primary">Bán chạy</span>';
        }
        if($item->status3 == 1) {
            $value[] = 'status3'; $text .= ', <span class="label label-danger">Nổi bật</span>';
        }
        $text = trim($text, ', ');
        $source = htmlentities(json_encode($source));
        $value  = htmlentities(json_encode($value));
        echo '<a href="#" data-source="'.$source.'" data-value="'.$value.'" data-pk="'.$item->id.'" class="edittable-product-collections" >'.$text.'</a>';
    }

    function column_order($item, $column_name, $module, $table) {
        echo '<a href="#" data-pk="'.$item->id.'" data-name="order" data-table="'.$table.'" class="edittable-dl-text" >'.$item->order.'</a>';
    }

    function _column_action($item, $column_name, $module, $table, $class) {

        $url = Url::adminModule();

        $status = InputBuilder::get('status');

        $url_type = '?page='.((InputBuilder::get('page') != '')?InputBuilder::get('page'):1);

        if(!empty(InputBuilder::get('category'))) $url_type .= '&category='.InputBuilder::get('category');

        $class .= ' text-center';

        echo '<td class="'.$class.'">';
        if($status == 'trash') {
            echo '<button class="btn-blue btn js_product_btn__undo" data-table="products" data-id="'.$item->id.'">'.Admin::icon('undo').'</a>';
            if( Auth::hasCap('product_delete') ) {
                echo '<button class="btn-red btn delete" data-id="'.$item->id.'" data-table="'.$table.'">'.Admin::icon('delete').'</button>';
            }
        } else {
            echo '<a href="'.$url.'edit/'.$item->slug.$url_type.'" class="btn-blue btn">'.Admin::icon('edit').'</a>';
            if( Auth::hasCap('product_delete') ) {
                echo '<button class="btn-red btn trash" data-id="'.$item->id.'" data-table="'.$table.'">'.Admin::icon('delete').'</button>';
            }
        }
        echo "</td>";
    }

    function search_right() {
        $url     = Url::adminModule();
        $input   = [];
        $input[] = ['field' => 'form_open', 'type'  => 'html', 'html'  => '<form class="search-box" action="'.$url.'">',];
        $input[] = [
            'field' => 'keyword', 'type'  => 'text',
            'value' => InputBuilder::Get('keyword'),
            'after' => '<div class="form-group-search">', 'before' => '</div>',
            'placeholder'   => 'Từ khóa...'
        ];
        $input[] = [
            'field' => 'collection', 'type'  => 'select',
            'options' => ['Tất cả', 'Yêu thích','Bán chạy', 'Nổi bật'],
            'value' => InputBuilder::Get('collection'),
            'after' => '<div class="form-group-search">', 'before' => '</div>',
            'placeholder'   => 'Từ khóa...'
        ];
        $input[] = [
            'field' => 'category',
            'type'  => 'product_categories',
            'value' => InputBuilder::Get('category'),
            'after' => '<div class="form-group-search">',
            'before' => '</div>', 'args' => array('placeholder' => 'Từ khóa...')
        ];
        $input[] = ['field' => 'submit', 'type'  => 'html', 'html'  => '<button type="submit" class="btn" style="margin-bottom: 3px;padding: 10px 10px;">'.Admin::icon('search').'</button>',];
        $input[] = ['field' => 'form_close', 'type'  => 'html', 'html'  => '</form>'];
        $input   = apply_filters('admin_table_product_form_search', $input);
        $FormBuilder = new FormBuilder();
        $FormBuilder->add($input);
        $FormBuilder->html(false);
    }
}
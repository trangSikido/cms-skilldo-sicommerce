<?php
class skd_product_category_list_table extends skd_object_list_table {

    function get_columns() {
        $this->_column_headers = [
            'cb'        => 'cb',
            'title'     => 'Tiêu Đề',
            'status'    => 'Nổi bật',
            'order'     => 'Thứ Tự',
            'public'    => 'Hiển Thị',
            'action'    => 'Hành Động',
        ];
        $this->_column_headers = apply_filters( "manage_product_category_columns", $this->_column_headers );
        return $this->_column_headers;
    }

    function column_default( $item, $column_name ) {
        do_action( 'manage_product_category_custom_column', $column_name, $item );
    }

    function column_title($item) {
        ?>
        <div class="img pull-left" style="padding-right:10px;">
            <?php echo Template::img($item->image, '', ['style' => 'height:30px;']);?>
        </div>
        <?php do_action('admin_product_category_table_column_title', $item);?>
        <div class="title pull-left">
            <h3><?= str_repeat('|-----', (($item->level > 0)?($item->level - 1):0)).$item->name;?></h3>
            <div class="action-hide">
                <span>ID : <?= $item->id;?></span> |
                <a href="<?= Url::permalink($item->slug);?>" target="_blank" data-toggle="tooltip" data-placement="top" title="Xem"><i class="fa fa-eye"></i></a>
            </div>
        </div>
        <?php
    }

    function column_status($item, $column_name, $module, $table) {
        echo '<input type="checkbox" class="icheck up-boolean" data-row="'.$column_name.'" data-model="'.$table.'"  data-id="'.$item->id.'"'.(($item->$column_name == 1)?'checked':'').'/>';
    }

    function column_order($item, $column_name, $module, $table) {
        echo '<p><a href="#" data-pk="'.$item->id.'" data-name="order" data-table="'.$table.'" class="edittable-dl-text" >'.$item->order.'</a></p>';
    }

    function _column_action($item, $column_name, $module, $table, $class) {
        $url    = Url::adminModule();
        $cate   = (!empty(Admin::getCateType())) ? '?cate_type='.Admin::getCateType() : '';
        $class .= ' text-center';
        echo '<td class="'.$class.'">';
        if(Auth::hasCap('product_cate_delete')) {
            echo '<button class="btn-red btn delete" data-id="'.$item->id.'" data-table="'.$table.'">'.Admin::icon('delete').'</button>';
        }
        if(Auth::hasCap('product_cate_edit')) {
            echo '<a href="'.$url.'edit/'.$item->slug.$cate.'" class="btn-blue btn">'.Admin::icon('edit').'</a>';
        }
        echo "</td>";
    }

    function search_left() {}

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
            'field' => 'category',
            'type'  => 'product_categories',
            'value' => InputBuilder::Get('category'),
            'after' => '<div class="form-group-search">',
            'before' => '</div>', 'args' => array('placeholder' => 'Từ khóa...')
        ];
        $input[] = ['field' => 'submit', 'type'  => 'html', 'html'  => '<button type="submit" class="btn" style="margin-bottom: 3px;padding: 10px 10px;">'.Admin::icon('search').'</button>',];
        $input[] = ['field' => 'form_close', 'type'  => 'html', 'html'  => '</form>'];
        $input   = apply_filters('admin_table_product_category_form_search', $input);
        $FormBuilder = new FormBuilder();
        $FormBuilder->add($input);
        $FormBuilder->html(false);
    }
}
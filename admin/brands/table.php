<?php
class skd_brands_list_table extends skd_object_list_table {

    function get_columns() {
        $this->_column_headers = [];
        $this->_column_headers['cb']       = 'cb';
        $this->_column_headers['image']    = 'Hình';
        $this->_column_headers['name']     = 'Thương hiệu';
        $this->_column_headers['order']    = 'Thứ tự';
        $this->_column_headers['action']   = 'Hành động';
        return apply_filters( "manage_brands_columns", $this->_column_headers );
    }

    function column_default($item, $column_name) {
        do_action( 'manage_brands_custom_column', $column_name, $item );
    }

    function column_image($item) {
        echo '<img src="'.Template::imgLink($item->image).'" alt="" style="width:50px;" loading="lazy">';
    }

    function column_name($item) {
        ?>
        <div class="title pull-left">
            <h3><?= $item->name;?></h3>
            <?php do_action('admin_brands_table_column_title', $item);?>
            <div class="action-hide">
                <span>ID : <?= $item->id;?></span> |
                <a href="<?= Url::permalink($item->slug);?>" target="_blank" data-toggle="tooltip" data-placement="top" title="Xem"><i class="fa fa-eye"></i></a>
            </div>
        </div>
        <?php
    }

    function column_order($item, $column_name, $module, $table) {
        echo '<a href="#" data-pk="'.$item->id.'" data-name="order" data-table="'.$table.'" class="edittable-dl-text" >'.$item->order.'</a>';
    }

    function _column_action($item, $column_name, $module, $table, $class) {
        echo '<td class="'.$class.' text-center">';
        echo '<a href="'.Url::admin('plugins?page=brands&view=edit&id='.$item->id).'" class="btn-blue btn">'.Admin::icon('edit').'</a>';
        echo '<button class="btn btn-red js_btn_confirm" data-trash="disable" data-action="delete" data-ajax="Cms_Ajax_Action::delete" data-id="'.$item->id.'" data-module="Brands" data-heading="Xóa Dữ liệu" data-description="Bạn chắc chắn muốn xóa thương hiệu <b>'.html_escape($item->name).'</b> ?">'.Admin::icon('delete').'</button>';
        echo "</td>";
    }

    function search_left() {
        ?>
        <button class="btn btn-red js_btn_confirm" style="display: none;" data-action="delete"
                data-ajax="Cms_Ajax_Action::delete" data-trash="disable" data-module="Brands"
                data-heading="Xóa Dữ liệu" data-description="Bạn chắc chắn muốn xóa trường dữ liệu này?"
                data-toggle="tooltip" data-placement="top"
                title="Xóa"><?php echo Admin::icon('delete'); ?></button>
        <?php
    }

    function search_right(){}
}
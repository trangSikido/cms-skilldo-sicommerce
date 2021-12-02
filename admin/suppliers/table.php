<?php
//suppliers
class skd_suppliers_list_table extends skd_object_list_table {

    function get_columns() {
        $this->_column_headers = [];
        $this->_column_headers['cb']        = 'cb';
        $this->_column_headers['name']      = 'Tên nhà sản xuất';
        $this->_column_headers['email']     = 'Email';
        $this->_column_headers['address']   = 'Địa chỉ';
        $this->_column_headers['action']   = 'Hành động';
        return apply_filters( "manage_suppliers_columns", $this->_column_headers );
    }

    function column_default( $item, $column_name ) {
       do_action( 'manage_suppliers_custom_column', $column_name, $item );
    }

    function column_name($item, $column_name, $module, $table) {
        echo $item->name;
    }

    function column_email($item, $column_name, $module, $table) {
        echo $item->email;
    }

    function column_address($item, $column_name, $module, $table) {
        echo $item->address;
    }

    function column_order($item, $column_name, $module, $table) {
        echo '<a href="#" data-pk="'.$item->id.'" data-name="order" data-table="'.$table.'" class="edittable-dl-text" >'.$item->order.'</a>';
    }

    function _column_action($item, $column_name, $module, $table, $class) {
        $class .= ' text-center';
        echo '<td class="'.$class.'">';
        echo '<a href="'.Url::admin('plugins?page=suppliers&view=edit&id='.$item->id).'" class="btn-blue btn">'.Admin::icon('edit').'</a>';
        echo '<button class="btn btn-red js_btn_confirm" data-trash="disable" data-action="delete" data-ajax="Cms_Ajax_Action::delete" data-id="'.$item->id.'" data-module="Suppliers" data-heading="Xóa Dữ liệu" data-description="Bạn chắc chắn muốn xóa thương hiệu <b>'.html_escape($item->name).'</b> ?">'.Admin::icon('delete').'</button>';
        echo "</td>";
    }

    function search_left() {
        ?>
        <button class="btn btn-red js_btn_confirm" style="display: none;" data-action="delete"
                data-ajax="Cms_Ajax_Action::delete" data-trash="disable" data-module="Suppliers"
                data-heading="Xóa Dữ liệu" data-description="Bạn chắc chắn muốn xóa trường dữ liệu này?"
                data-toggle="tooltip" data-placement="top" title="Xóa"><?php echo Admin::icon('delete'); ?></button>
        <?php
    }

    function search_right(){}
}
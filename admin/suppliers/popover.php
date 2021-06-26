<?php
function popover_supplier_search($object, $keyword) {
    $object = Suppliers::gets([
        'where_like' => ['name' => array($keyword)]
    ]);
    return $object;
}
add_filter('input_popover_supplier_search', 'popover_supplier_search', 10, 2);
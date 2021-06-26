<?php
Class Brands_Admin_Popover {
    static public function search($object, $keyword) {
        $object = Brands::gets([
            'where_like' => ['name' => array($keyword)]
        ]);
        return $object;
    }
    static public function value($value, $data) {
        $brands = [];
        if(!empty($data['value'])) {
            if(is_array($data['value'])) {
                foreach ($data['options'] as $op_id => $op_value ) {
                    if(in_array($op_id, $data['value']) === true) {
                        $value[$op_id] = ['label' => $op_value];
                        unset($data['value'][array_search($op_id, $data['value'])]);
                    }
                }
                $brands = Brands::gets(['where_in' => array('field' => 'id', 'data' => $data['value']), 'params' => array('select' => 'id, name, image')]);
            }
            else {
                $brands = Brands::gets(['where_in' => array('field' => 'id', 'data' => [$data['value']]), 'params' => array('select' => 'id, name, image')]);
            }
        }
        if(isset($data['image']) && $data['image'] == true) {
            foreach ($brands as $branch) {
                $value[$branch->id] = [
                    'label' => $branch->name,
                    'image' => $branch->image
                ];
            }
        }
        else {
            foreach ($brands as $product) {
                $value[$product->id] = ['label' => $product->name];
            }
        }
        return $value;
    }
}
add_filter('input_popover_brands_search', 'Brands_Admin_Popover::search', 10, 2);
add_filter('input_popover_brands_value', 'Brands_Admin_Popover::value', 10, 2);
<?php if(!empty($object->brand_id)) {
    $brand = Brands::get(['where' => ['id' => $object->brand_id], 'params' => ['select'=> 'name']]);
    echo '<p class="product-detail-brand">'.$brand->name.'</p>';
}
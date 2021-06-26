<?php
Class Product_Admin_Ajax {
    static public function collectionSave( $ci, $model ) {

        $result['status']  = 'error';

        $result['message'] = __('Lưu dữ liệu không thành công');

        if(InputBuilder::post()) {

            $collections = InputBuilder::post('value');

            $id          = (int)InputBuilder::post('pk');

            $product = Product::get($id);

            if(have_posts($product)) {

                $product_update = ['id' => $id, 'status1' => 0, 'status2' => 0, 'status3' => 0];

                if(have_posts($collections)) {
                    foreach ($collections as $collection) {
                        $collection = removeHtmlTags($collection);
                        if ($collection == 'status1') $product_update['status1'] = 1;
                        if ($collection == 'status2') $product_update['status2'] = 1;
                        if ($collection == 'status3') $product_update['status3'] = 1;
                    }
                }

                $error = insert_product($product_update);

                if(is_skd_error($error)) {
                    $result['status']  = 'error';
                    foreach ($error->errors as $key => $er) {
                        $result['message'] = $er;
                    }
                }
                else {
                    $result['status']  = 'success';
                    $result['message'] = __('Lưu dữ liệu thành công.');
                }
            }
        }

        echo json_encode($result);

        return true;
    }
}
Ajax::admin('Product_Admin_Ajax::collectionSave');
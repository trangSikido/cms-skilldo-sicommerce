<?php
Class Brands_Admin_Ajax {
    static public function save($ci, $model) {

        $result['status']  = 'error';

        $result['message'] = __('Lưu dữ liệu không thành công');

        if(InputBuilder::post()) {

            $data = InputBuilder::post();

            $data   = apply_filters('save_brands_before', $data);

            $error  = apply_filters('check_save_brands_before', true, $data);

            if (is_skd_error($error)) {

                $result['status']  = 'error';

                foreach ($error->errors as $key => $er) {
                    $result['message'] = $er;
                }

                echo json_encode($result);

                return false;
            }

            $error = Brands::insert($data);

            if(is_skd_error($error) ) {

                $result['status']  = 'error';

                foreach ($error->errors as $key => $er) {
                    $result['message'] = $er;
                }
            }
            else {

                $result['status']  = 'success';

                $result['message'] = __('Lưu dữ liệu thành công.');

                $result   = apply_filters('save_brands_success', $result, $error);
            }
        }

        echo json_encode($result);

        return true;
    }
}
Ajax::admin('Brands_Admin_Ajax::save');
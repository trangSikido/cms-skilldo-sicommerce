<?php
Class Suppliers_Admin_Ajax {

    static public function save( $ci, $model ) {

        $result['status']  = 'error';

        $result['message'] = __('Lưu dữ liệu không thành công');

        if( InputBuilder::post() ) {

            $data = InputBuilder::post();

            $data   = apply_filters('save_suppliers_before', $data);

            $error  = apply_filters('check_save_suppliers_before', true, $data);

            if (is_skd_error($error)) {

                $result['status']  = 'error';

                foreach ($error->errors as $key => $er) {
                    $result['message'] = $er;
                }

                echo json_encode($result);

                return false;
            }

            $error = insert_suppliers($data);

            if ( is_skd_error($error) ) {

                $result['status']  = 'error';

                foreach ($error->errors as $key => $er) {

                    $result['message'] = $er;
                }
            }
            else {

                $result['status']  = 'success';

                $result['message'] = __('Lưu dữ liệu thành công.');

                $result   = apply_filters('save_suppliers_success', $result, $error);

            }
        }

        echo json_encode($result);
    }
}
Ajax::admin('Suppliers_Admin_Ajax::save');
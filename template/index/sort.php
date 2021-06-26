<div class="hidden">
    <input type="text" id="slug" value="<?php echo (empty($slug)) ? URL_PRODUCT: $slug;?>">
    <?php if(Language::hasMulti()) {?>
    <input type="text" id="language" value="<?php echo Language::current();?>">
    <?php } ?>
    <?php do_action('page_products_index_form_hidden');?>
</div>
<div class="product-sort-bar">
    <div class="product-sort-left js_product_pagination">
        <?php if(isset($pagination) && is_object($pagination) && method_exists( $pagination, 'html' ) ) echo $pagination->html();?>
    </div>
    <div class="product-sort-right">
        <label for="">Sắp xếp</label>
        <select name="orderby" id="orderby" class="form-control">
            <option value="">Mới nhất</option>
            <option value="price-asc" <?php echo (InputBuilder::get('orderby') == 'price-asc' ) ? 'selected' : '';?>>Giá từ thấp đến cao</option>
            <option value="price-desc" <?php echo (InputBuilder::get('orderby') == 'price-desc' ) ? 'selected' : '';?>>Giá từ cao đến thắp</option>
            <option value="best-selling" <?php echo (InputBuilder::get('orderby') == 'best-selling' ) ? 'selected' : '';?>>Bán chạy</option>
        </select>
    </div>
</div>
<div class="clearfix"></div>
<style>
    .product-sort-bar {
        position: relative;
        overflow: hidden; padding: 15px 0;
    }
    .product-sort-bar .product-sort-right {
        float:right; width: 50%; text-align: right;
    }
    .product-sort-bar .product-sort-right .form-control label{
        display: inline-block;
    }
    .product-sort-bar .product-sort-right .form-control {
        display: inline-block; width: 200px;
        border-radius: 5px;
        height: 40px;
        box-shadow: none;
        border: 0;
    }
    .product-sort-bar .product-sort-left {
        float:left; width: 50%; text-align: left;
    }
    .product-sort-bar .product-sort-left .pagination {
        margin: 0;
    }
    @media(max-width: 768px) {
        .product-sort-bar .product-sort-right {
            float:right; width: 100%; text-align: right;
        }
        .product-sort-bar .product-sort-left {
            float:left; width: 100%; text-align: left; display: none;
        }
    }
</style>
<script>
    $(function () {

        let page = 1;

        $(document).on('click', '.page-product-index .pagination .pagination-item', function () {
            page = $(this).attr('data-page-number');
            $('#js_product_index_form__load').trigger('submit');
            return false;
        });

        $(document).on('change', '#js_product_index_form__load #orderby', function () {
            $('#js_product_index_form__load').trigger('submit');
            return false;
        });

        $('#js_product_index_form__load').submit(function () {

            let loading = $(this).find('.loading');

            let slug = $('#slug').val();

            let data        = $(this).serializeJSON();

            let url = domain;

            if(typeof $('#language').val() != 'undefined') {

                let language = $('#language').val();

                url += language + '/';
            }

            url += slug + '?page='+page;

            $.map(data, function (val, i) {
                if(val.length === 0) {
                    delete data[i];
                }
                else if(Array.isArray(val)) {
                    $.map(val, function (val2, i2) {
                        if(val2.length === 0) {
                            delete data[i][i2];
                        }
                        else {
                            data[i][i2] = val.join(',');
                        }
                    });
                }
                else if(typeof val === 'object') {
                    $.map(val, function (val2, i2) {
                        if(val2.length === 0) {
                            delete data[i][i2];
                        }
                        else {
                            data[i][i2] = val.join(',');
                        }
                    });
                }
            });

            let param = $.param(data);



            if(param.length !== 0) {
                param =  param.replace(/%2C/g, ",");
                url += '&' + param;
            }

            data.page   = page;

            data.action = 'ajax_product_controller';

            data.slug   = slug;

            window.history.pushState("data",'', url);

            loading.show();

            $.post(ajax, data, function(data) {}, 'json').done(function( response ) {

                loading.hide();

                if(response.status === 'success') {

                    $('#js_product_list__item').html(response.list);

                    $('.js_product_pagination').html(response.pagination);

                    $('html,body').animate({
                        scrollTop: $("#js_product_list__item").offset().top - 200
                    }, 'slow');
                }
                else {
                    window.location.href = url;
                }
            });

            return false;
        });
    });

    function insertParam(key, value) {

        key = encodeURIComponent(key);

        value = encodeURIComponent(value);

        // kvp looks like ['key1=value1', 'key2=value2', ...]
        var kvp = document.location.search.substr(1).split('&');

        let i=0;

        for(; i<kvp.length; i++){
            if (kvp[i].startsWith(key + '=')) {
                let pair = kvp[i].split('=');
                pair[1] = value;
                kvp[i] = pair.join('=');
                break;
            }
        }

        if(i >= kvp.length){
            kvp[kvp.length] = [key,value].join('=');
        }

        // can return this or...
        let params = kvp.join('&');

        return params;
    }
</script>
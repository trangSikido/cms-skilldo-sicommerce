<div class="hidden">
    <input type="text" id="slug" value="<?php echo (empty($slug)) ? URL_PRODUCT: $slug;?>">
    <input type="text" name="orderby" id="sort" value="<?php echo (empty($_GET['orderby'])) ? 'new': InputBuilder::Get('orderby');?>">
    <?php if(Language::hasMulti()) {?>
        <input type="text" id="language" value="<?php echo Language::current();?>">
    <?php } ?>
    <?php do_action('page_products_index_form_hidden');?>
</div>
<div class="product-sort-bar">
    <div class="product-sort-box">
        <label for=""><?php echo __('Sắp xếp theo','product_index_sort');?></label>
        <div class="product-sort-element">
            <a class="btn js_product_sort_btn <?php echo (empty(InputBuilder::Get('orderby')) || InputBuilder::Get('orderby') == 'new') ? 'active': '';?>" data-sort="new">Mới nhất</a>
            <a class="btn js_product_sort_btn <?php echo (InputBuilder::Get('orderby') == 'hot') ? 'active': '';?>" data-sort="hot">Nổi bật</a>
            <a class="btn js_product_sort_btn <?php echo (InputBuilder::Get('orderby') == 'best-selling') ? 'active': '';?>" data-sort="best-selling">Bán chạy</a>
            <a class="btn js_product_sort_btn <?php echo (InputBuilder::Get('orderby') == 'price-asc') ? 'active': '';?>" data-sort="price-asc">Giá thấp</a>
            <a class="btn js_product_sort_btn <?php echo (InputBuilder::Get('orderby') == 'price-desc') ? 'active': '';?>" data-sort="price-desc">Giá cao</a>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<style>
    .product-sort-bar {
        position: relative;
        overflow: hidden; padding: 0 0 15px 0;
    }
    .product-sort-bar .product-sort-box {
        text-align: right; display: flex; align-items: center; gap:10px; border-bottom: 1px solid #f4f4f4;
    }
    .product-sort-bar .product-sort-box .product-sort-element {
        display: flex;
    }
    .product-sort-bar .product-sort-box .product-sort-element .btn {
        display: block;
        padding:10px 15px;
        border-radius: 0!important;
        border: none;
        border-bottom: 3px solid transparent;
        margin: 0 10px -1px;
        font-weight: 500;
    }
    .product-sort-bar .product-sort-box .product-sort-element .btn:hover,  .product-sort-bar .product-sort-box .product-sort-element .btn.active {
        color: var(--theme-color);
        border-bottom: 3px solid var(--theme-color) !important;
    }
    @media(max-width: 600px) {
        .product-sort-bar .product-sort-box label {
            display: none;
        }
        .product-sort-bar .product-sort-box .product-sort-element .btn {
            padding:5px 5px; margin:0 5px -1px;
            font-size: 13px; font-weight: 400;
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

        $(document).on('click touch', '#js_product_index_form__load .js_product_sort_btn', function () {
            let orderby = $(this).data('sort');
            $('#sort').val(orderby);
            $('#js_product_index_form__load .js_product_sort_btn').removeClass('active');
            $(this).addClass('active');
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
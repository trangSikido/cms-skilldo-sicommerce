<?php
$product_supplier 		= (int)option::get('product_supplier');
$product_brands 		= (int)option::get('product_brands');
$product_currency 		= option::get('product_currency');
$product_price_contact 	= option::get('product_price_contact');
?>
<div class="box">
	<div class="box-content">
		<div class="col-md-12">
            <div class="row">
                <div class="col-md-2">
                    <?php  $input = array('field' => 'product_supplier', 'type' => 'switch', 'label' => 'Nhà sản xuất', 'note' => 'Sử dụng nhà sản xuất'); ?>
                    <?php echo _form($input, $product_supplier);?>
                </div>
                <div class="col-md-2">
                    <?php  $input = array('field' => 'product_brands', 'type' => 'switch', 'label' => 'Thương hiệu', 'note' => 'Sử dụng thương hiệu'); ?>
                    <?php echo _form($input, $product_brands);?>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Tiền tệ</label>
                    <input name="product_currency" type="text" class="form-control" value="<?php echo $product_currency;?>">
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Giá liên hệ</label>
                    <p>Thay thế cho giá sản phẩm khi bằng 0</p>
                    <input name="product_price_contact" type="text" class="form-control" value="<?php echo $product_price_contact;?>">
                </div>
            </div>

		</div>
	</div>
</div>

<script type="text/javascript">
	$(function() {
		$('#mainform').submit(function() {
			$('.loading').show();
			let data  = $(this).serializeJSON();
			data.action =  'Product_Admin_Setting_Ajax::save';
			$.post(ajax, data, function() {}, 'json').done(function(response) {
				$('.loading').hide();
	  			show_message(response.message, response.status);
			});
			return false;
		});
	});
</script>
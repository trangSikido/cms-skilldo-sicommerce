<?php
$product_hiden_title      	= option::get('product_hiden_title');
$product_hiden_price      	= option::get('product_hiden_price');
$product_hiden_description  = option::get('product_hiden_description');
$product_title_color       = option::get('product_title_color');
$product_price_color       = option::get('product_price_color');
?>
<div class="box">
	<div class="header"> <h2>Box Sản Phẩm</h2> </div>
	<div class="box-content">
		<div class="col-md-12">
			<div class="row">
				<div class="col-md-3">
					<label for="">Tên sản phẩm</label>
					<p style="color:#999;margin:5px 0 5px 0;">Cấu hình tên hiển thị sản phẩm</p>
				</div>
				<div class="col-md-9">
                    <div class="row">
                        <div class="col-md-3">
                            <?php  $input = array('field' => 'product_hiden_title', 'type'	=> 'switch', 'label' => 'Ẩn / Hiện'); echo _form($input, $product_hiden_title);?>
                        </div>
                        <div class="col-md-9">
                            <?php  $input = array('field' => 'product_title_color', 'type'	=> 'color', 'label' => 'Màu Tên',); ?>
                            <?php echo _form($input, $product_title_color);?>
                        </div>
                    </div>
				</div>
			</div>
			<hr/>

			<div class="row">
				<div class="col-md-3">
					<label for="">Giá sản phẩm</label>
					<p style="color:#999;margin:5px 0 5px 0;">Cấu hình hiển thị giá sản phẩm</p>
				</div>
				<div class="col-md-9">
                    <div class="row">
                        <div class="col-md-3">
                            <?php  $input = array('field' => 'product_hiden_price', 'type'	=> 'switch', 'label' => 'Ẩn / Hiện'); ?>
                            <?php echo _form($input, $product_hiden_price);?>
                        </div>
                        <div class="col-md-9">
                            <?php  $input = array('field' => 'product_price_color', 'type'	=> 'color', 'label' => 'Màu Giá',); ?>
                            <?php echo _form($input, $product_price_color);?>
                        </div>
                    </div>
				</div>
			</div>
			<hr/>

			<div class="row">
				<div class="col-md-3">
					<label for="">Mô tả sản phẩm</label>
					<p style="color:#999;margin:5px 0 5px 0;">Cấu hình tên hiển thị sản phẩm</p>
				</div>
				<div class="col-md-9">
					<?php  $input = array('field' => 'product_hiden_description', 'type'	=> 'switch', 'label' => 'Ẩn / Hiện'); ?>
					<?php echo _form($input, $product_hiden_description);?>
				</div>
			</div>

		</div>
	</div>
</div>
<style type="text/css">
	.product { margin-bottom: 10px; }
	.product .img img { width: 100%; }
	.product .title { text-transform: uppercase; font-size: 10px; margin-bottom: 5px; }
	.product.ih-item.square { width: 100%; height: auto; }
</style>

<script type="text/javascript">
$(function() {

	let item = $('.woocommecre-review-object');

	let form = $('#mainform');

	let productObjectHandler = function() {
		this.onLoad();
	};

	productObjectHandler.prototype.onLoad = function (e) {
		let data = $( ':input', form).serializeJSON();
	};

	new productObjectHandler();
})
</script>
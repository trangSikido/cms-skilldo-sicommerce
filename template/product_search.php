<?php
	$category_row_count        = option::get('category_row_count');
	$category_row_count_tablet = option::get('category_row_count_tablet');
	$category_row_count_mobile = option::get('category_row_count_mobile');

	$col['lg'] = 12/$category_row_count;
	$col['md'] = 12/$category_row_count;
	$col['sm'] = 12/$category_row_count_tablet;
	$col['xs'] = 12/$category_row_count_mobile;

	$col = 'col-xs-'.$col['xs'].' col-sm-'.$col['sm'].' col-md-'.$col['md'].' col-lg-'.$col['lg'].'';
?>

<div class="product-slider-horizontal" style="margin-top: 10px;">
	<div class="list-item-product">
	<?php foreach ($objects as $key => $val): ?>
		<div class="<?php echo $col;?>">
		<?php echo scmc_template('loop/item_product', array('val' =>$val));?>
		</div>
	<?php endforeach ?>
	</div>
</div>
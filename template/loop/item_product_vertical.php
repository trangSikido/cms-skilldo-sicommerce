<div class="item">
	<div class="img">
		<a href="<?= Url::permalink($val->slug);?>">
			<?php Template::img($val->image);?>
		</a>
	</div>
	<div class="info">
		<h3><a href="<?= Url::permalink($val->slug);?>" title="<?= $val->title;?>"><?= $val->title;?></a></h3>
		<p class="price view_price">
			<?php if(!empty($val->price_sale)) { ?>
				<span class="price-new"><?= number_format($val->price_sale);?><?php echo _price_currency();?></span>
				<del class="price-old"><?= number_format($val->price);?><?php echo _price_currency();?></del>
	      	<?php } else if($val->price == 0) { ?>
	        	<span class="price-new"><?php echo _price_none();?></span>
	      	<?php } else {?>
	        	<span class="price-new"><?= number_format($val->price);?><?php echo _price_currency();?></span>
	      	<?php } ?>
		</p>
	</div>
</div>
<div class="img">
	<picture>
	    <source media="(min-width: 900px)" srcset="<?= Template::imgLink($val->image, $image_type);?>">
	    <source media="(max-width: 480px)" srcset="<?= Template::imgLink($val->image, $image_type);?>">
	    <?php Template::img($val->image,$val->title, ['type' => $image_type]);?>
	</picture>
</div>
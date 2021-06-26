<div class="widget widget_product">
    <div class="sidebar-title"><h4 class="header"><?php echo $heading;?></h4></div>
    <div class="sidebar-content box-content product-slider-vertical">
        <?php foreach ($products as $key => $val): ?>
            <?php scmc_template('loop/item_product_vertical', ['val' => $val]);?>
        <?php endforeach ?>
    </div>
</div>
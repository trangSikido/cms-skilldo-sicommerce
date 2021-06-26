<div class="ui-layout">
    <div class="col-md-12">
        <div class="ui-title-bar__group">
            <h1 class="ui-title-bar__title">Thương hiệu</h1>
            <div class="ui-title-bar__action">
                <?php do_action('admin_brands_action_bar_heading');?>
            </div>
        </div>
        <div class="box">
            <!-- .box-content -->
            <div class="box-content">
                <!-- search box -->
                <!-- /search box -->
                <form method="post" id="form-action" class="table-responsive">
                    <?php $table_list->display();?>
                </form>
                <!-- paging -->
                <div class="paging">
                    <div class="pull-right"><?= (isset($pagination))?$pagination->html():'';?></div>
                </div>
                <!-- paging -->
            </div>
            <!-- /.box-content -->
        </div>
    </div>
</div>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="<?php echo admin_url('rooms/room'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('new room'); ?></a>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        <?php render_datatable(
                            array(
                                _l('id'),
                                _l('Room Name'),
                                _l('Specifications'),
                                _l('Ceiling Height'),
                                _l('Foyer Area'),
                                _l('Balcony Area'),
                                _l('options')
                            ),'room-fields'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function(){
        initDataTable('.table-room-fields', window.location.href, [6], [6]);
    });
</script>
</body>
</html>

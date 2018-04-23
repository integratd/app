<?php echo form_open('suppliers/item'); ?>
<div class="row">
    <div class="col-md-6">
        <div class="panel_s">
            <div class="panel-heading text-uppercase">
                <?php echo _l('invoice_item_add_heading'); ?>
            </div>
            <div class="panel-body">
                <div class="row">
                        <div class="col-md-12">
                        <div class="alert alert-warning affect-warning hide">
                            <?php echo _l('changing_items_affect_warning'); ?>
                        </div>
                            <div class="form-group">
                                <label for="description"><?php echo _l('invoice_item_add_edit_description'); ?></label>
                                <input type="text" class="form-control" name="description" id="description">
                                <?php echo form_error('description'); ?>
                            </div>
                            <div class="form-group">
                                <label for="stockinhand"><?php echo _l('stock_in_hand'); ?></label>
                                <input type="text" class="form-control" name="stockinhand" id="stockinhand">
                                <?php echo form_error('stockinhand'); ?>
                            </div>
                            <div class="form-group">
                                <label for="long_description"><?php echo _l('invoice_item_long_description'); ?></label>
                                <input type="text" class="form-control" name="long_description" id="long_description">
                            </div>
                            <div class="form-group">
                                <label for="rate" class="control-label">
                                    <?php echo _l('invoice_item_add_edit_rate_currency',$base_currency->name . ' <small>('._l('base_currency_string').')</small>'); ?></label>
                                <input type="number" id="rate" name="rate" class="form-control" value="">
                                <?php echo form_error('rate'); ?>
                            </div>
                        <?php
                        foreach($currencies as $currency){
                            if($currency['isdefault'] == 0 && total_rows('tblclients',array('default_currency'=>$currency['id'])) > 0){ ?>
                                <div class="form-group">
                                    <label for="rate_currency_<?php echo $currency['id']; ?>" class="control-label">
                                        <?php echo _l('invoice_item_add_edit_rate_currency',$currency['name']); ?></label>
                                    <input type="number" id="rate_currency_<?php echo $currency['id']; ?>" name="rate_currency_<?php echo $currency['id']; ?>" class="form-control" value="">
                                </div>
                            <?php   }
                        }
                        ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="tax"><?php echo _l('tax_1'); ?></label>
                                    <select class="selectpicker display-block" data-width="100%" name="tax" data-none-selected-text="<?php echo _l('no_tax'); ?>">
                                        <option value=""></option>
                                        <?php foreach($taxes as $tax){ ?>
                                            <option value="<?php echo $tax['id']; ?>" data-subtext="<?php echo $tax['name']; ?>"><?php echo $tax['taxrate']; ?>%</option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="tax2"><?php echo _l('tax_2'); ?></label>
                                    <select class="selectpicker display-block" disabled data-width="100%" name="tax2" data-none-selected-text="<?php echo _l('no_tax'); ?>">
                                        <option value=""></option>
                                        <?php foreach($taxes as $tax){ ?>
                                            <option value="<?php echo $tax['id']; ?>" data-subtext="<?php echo $tax['name']; ?>"><?php echo $tax['taxrate']; ?>%</option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix mbot15"></div>
                        <?php echo render_input('unit','unit'); ?>
                        <div id="custom_fields_items">
                            <?php echo render_custom_fields('items'); ?>
                        </div>
                        <?php echo render_select('group_id',$items_groups,array('id','name'),'item_group'); ?>
                        <?php echo render_select('package_id[]',$items_packages,array('id','name'),'item_package','',array('multiple'=>true),array(),'','item_packages',false); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 text-center mtop20">
        <button type="submit" class="btn btn-info" data-form="#open-new-ticket-form" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>"><?php echo _l('submit'); ?></button>
    </div>
</div>
<?php echo form_close(); ?>








































<script>
    // Maybe in modal? Eq convert to invoice or convert proposal to estimate/invoice
    if(typeof(jQuery) != 'undefined'){
        init_item_js();
    } else {
      window.addEventListener('load', function () {
        init_item_js();
     });
  }

// Items add/edit
function manage_invoice_items(form) {
    var data = $(form).serialize();

    var url = form.action;
    $.post(url, data).done(function (response) {
        response = JSON.parse(response);
        if (response.success == true) {
            var item_select = $('#item_select');
            if ($("body").find('.accounting-template').length > 0) {
                var group = item_select.find('[data-group-id="' + response.item.group_id + '"]');
                var _option = '<option data-subtext="' + response.item.long_description + '" value="' + response.item.itemid + '">(' + accounting.formatNumber(response.item.rate) + ') ' + response.item.description + '</option>';
                if (!item_select.hasClass('ajax-search')) {
                    if (group.length == 0) {
                        _option = '<optgroup label="' + (response.item.group_name == null ? '' : response.item.group_name) + '" data-group-id="' + response.item.group_id + '">' + _option + '</optgroup>';
                        if (item_select.find('[data-group-id="0"]').length == 0) {
                            item_select.find('option:first-child').after(_option);
                        } else {
                            item_select.find('[data-group-id="0"]').after(_option);
                        }
                    } else {
                        group.prepend(_option);
                    }
                }
                if (!item_select.hasClass('ajax-search')) {
                    item_select.selectpicker('refresh');
                } else {

                    item_select.contents().filter(function () {
                        return !$(this).is('.newitem') && $(this).is('.newitem-divider');
                    }).remove();

                    var clonedItemsAjaxSearchSelect = item_select.clone();
                    item_select.selectpicker('destroy').remove();
                    item_select = clonedItemsAjaxSearchSelect;
                    $("body").find('.items-wrapper').append(clonedItemsAjaxSearchSelect);
                    init_ajax_search('items', '#item_select.ajax-search', undefined, admin_url + 'items/search');
                }

                add_item_to_preview(response.item.itemid);
            } else {
                // Is general items view
                $('.table-invoice-items').DataTable().ajax.reload(null, false);
            }
            alert_float('success', response.message);
        }
        $('#sales_item_modal').modal('hide');
    }).fail(function (data) {
        alert_float('danger', data.responseText);
    });
    return false;
}
function init_item_js() {
     // Add item to preview from the dropdown for invoices estimates
    $("body").on('change', 'select[name="item_select"]', function () {
        var itemid = $(this).selectpicker('val');
        if (itemid != '' && itemid !== 'newitem') {
            add_item_to_preview(itemid);
        } else if (itemid == 'newitem') {
            // New item
            $('#sales_item_modal').modal('show');
        }
    });


    // Items modal show action
    $("body").on('show.bs.modal', '#sales_item_modal', function (event) {

        $('.affect-warning').addClass('hide');

        var $itemModal = $('#sales_item_modal');
        $('input[name="itemid"]').val('');
        $itemModal.find('input').not('input[type="hidden"]').val('');
        $itemModal.find('textarea').val('');
        $itemModal.find('select').selectpicker('val', '').selectpicker('refresh');
        $('select[name="tax2"]').selectpicker('val', '').change();
        $('select[name="tax"]').selectpicker('val', '').change();
        $itemModal.find('.add-title').removeClass('hide');
        $itemModal.find('.edit-title').addClass('hide');

        var id = $(event.relatedTarget).data('id');
        // If id found get the text from the datatable
        if (typeof (id) !== 'undefined') {

            $('.affect-warning').removeClass('hide');
            $('input[name="itemid"]').val(id);

            requestGetJSON('invoice_items/get_item_by_id/' + id).done(function (response) {
                $itemModal.find('input[name="description"]').val(response.description);
                $itemModal.find('textarea[name="long_description"]').val(response.long_description.replace(/(<|<)br\s*\/*(>|>)/g, " "));
                $itemModal.find('input[name="rate"]').val(response.rate);
                $itemModal.find('input[name="unit"]').val(response.unit);
                $('select[name="tax"]').selectpicker('val', response.taxid).change();
                $('select[name="tax2"]').selectpicker('val', response.taxid_2).change();
                $itemModal.find('#group_id').selectpicker('val', response.group_id);
                $itemModal.find('.item_packages').selectpicker('val', response.item_packages);
                $.each(response, function (column, value) {
                    if (column.indexOf('rate_currency_') > -1) {
                        $itemModal.find('input[name="' + column + '"]').val(value);
                    }
                });

                $('#custom_fields_items').html(response.custom_fields_html);

                init_selectpicker();
                init_color_pickers();
                init_datepicker();

                $itemModal.find('.add-title').addClass('hide');
                $itemModal.find('.edit-title').removeClass('hide');
                validate_item_form();
            });

        }
    });

    $("body").on("hidden.bs.modal", '#sales_item_modal', function (event) {
        $('#item_select').selectpicker('val', '');
    });

   validate_item_form();
}
function validate_item_form(){
    // Set validation for invoice item form
    _validate_form($('#invoice_item_form'), {
        description: 'required',
        rate: {
            required: true
        }
    }, manage_invoice_items);
}
</script>
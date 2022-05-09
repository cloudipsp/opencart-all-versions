<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form" data-toggle="tooltip" title="<?php echo $button_save; ?>"
                        class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i
                            class="fa fa-reply"></i></a></div>
            <img src="view/image/payment/oplata.png" alt="Fondy" style="height:25px; margin-top:-5px;"/>
            <ul class="breadcrumb">
                <?php foreach($breadcrumbs as $_key => $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i><?php echo $text_edit; ?></h3>
                <div class="pull-right text-muted">ver.<?php echo $extension_version; ?></div>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form"
                      class="form-horizontal">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
                        <li><a href="#tab-order-status" data-toggle="tab"><?php echo $tab_order_status; ?></a></li>
                        <li><a href="#tab-style" id="nav-tab-style" class="hidden" data-toggle="tab"><?php echo $tab_style; ?></a></li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-general">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-status">
                                    <span data-toggle="tooltip"
                                          title="<?php echo $tooltip_entry_status; ?>"><?php echo $entry_status; ?></span>
                                </label>
                                <div class="col-sm-10">
                                    <select name="oplata_status" id="input-status" class="form-control">
                                        <?php if ($oplata_status) { ?>
                                        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                        <option value="0"><?php echo $text_disabled; ?></option>
                                        <?php } else { ?>
                                        <option value="1"><?php echo $text_enabled; ?></option>
                                        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-environment">
                                    <span data-toggle="tooltip"
                                          title="<?php echo $tooltip_entry_environment; ?>"><?php echo $entry_env; ?></span>
                                </label>
                                <div class="col-sm-10">
                                    <select name="oplata_environment" class="form-control"
                                            id="input-environment">
                                        <option value="live" <?php echo (($oplata_environment == 'live') ? ('selected') : ('')); ?>>
                                        Live
                                        </option>
                                        <option value="test" <?php echo (($oplata_environment == 'test') ? ('selected') : ('')); ?>>
                                        Test
                                        </option>
                                    </select>
                                    <small id="input-environment-help-text"
                                           class="form-text text-muted <?php echo (($oplata_environment != 'test') ? (' hidden') : ('')); ?>"
                                    ><?php echo $help_entry_environment; ?></small>
                                </div>
                            </div>

                            <div class="form-group required">
                                <label class="col-sm-2 control-label" for="input-merchant">
                                    <span data-toggle="tooltip"
                                          title="<?php echo $tooltip_entry_merchant; ?>"><?php echo $entry_merchant; ?></span>
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" name="oplata_merchant"
                                           value="<?php echo $oplata_merchant; ?>"
                                           id="input-merchant" class="form-control" required/>
                                    <?php if ($error_merchant) { ?>
                                    <span class="error"><?php echo $error_merchant; ?></span>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="form-group required">
                                <label class="col-sm-2 control-label" for="input-secretkey">
                                    <span data-toggle="tooltip"
                                          title="<?php echo $tooltip_entry_secretkey; ?>"><?php echo $entry_secretkey; ?></span>
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" name="oplata_secretkey"
                                           value="<?php echo $oplata_secretkey; ?>"
                                           id="input-secretkey" class="form-control" required/>
                                    <?php if ($error_secretkey) { ?>
                                    <span class="error"><?php echo $error_secretkey; ?></span>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-process-payment-type">
                                    <span data-toggle="tooltip"
                                          title="<?php echo $tooltip_entry_process_payment_type; ?>"><?php echo $entry_process_payment_type; ?></span>
                                </label>
                                <div class="col-sm-10">
                                    <select name="oplata_process_payment_type" id="input-process-payment-type"
                                            class="form-control">
                                        <?php foreach($process_payment_types as $key => $pp_type) { ?>
                                        <option value="<?php echo $key; ?>"
                                        <?php echo (($oplata_process_payment_type == $key) ? ('selected') : ('')); ?>
                                        ><?php echo $pp_type; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-payment-type">
                                    <span data-toggle="tooltip"
                                          title="<?php echo $tooltip_entry_payment_type; ?>"><?php echo $entry_payment_type; ?></span>
                                </label>
                                <div class="col-sm-10">
                                    <select name="oplata_type" id="input-payment-type" class="form-control">
                                        <option value="common" <?php echo (($oplata_type == 'common') ? (' selected') : ('')); ?>><?php echo $entry_common_type; ?></option>
                                        <option value="preauth" <?php echo (($oplata_type == 'preauth') ? (' selected') : ('')); ?>><?php echo $entry_preauth_type; ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-geo-zone"><?php echo $entry_geo_zone; ?></label>
                                <div class="col-sm-10">
                                    <select name="oplata_geo_zone_id" id="input-geo-zone" class="form-control">
                                        <option value="0"><?php echo $text_all_zones; ?></option>
                                        <?php foreach($geo_zones as $_key => $geo_zone) { ?>
                                        <option value="<?php echo $geo_zone['geo_zone_id']; ?>"
                                        <?php echo (($geo_zone['geo_zone_id'] == $oplata_geo_zone_id) ? ('selected') : ('')); ?>
                                        ><?php echo $geo_zone['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label"
                                       for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                                <div class="col-sm-10">
                                    <input type="text" name="oplata_sort_order"
                                           value="<?php echo $oplata_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>"
                                           id="input-sort-order" class="form-control"/>
                                </div>
                            </div>
                        </div><!-- /#-tab-general -->

                        <div class="tab-pane" id="tab-order-status">
                            <div class="form-group">
                                <label class="col-sm-2 control-label"
                                       for="input-order-status"><?php echo $entry_order_success_status; ?></label>
                                <div class="col-sm-10">
                                    <select name="oplata_order_success_status_id" id="input-order-status"
                                            class="form-control">
                                        <?php foreach($order_statuses as $_key => $order_status) { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"
                                        <?php echo (($order_status['order_status_id'] == $oplata_order_success_status_id) ? ('selected') : ('')); ?>
                                        ><?php echo $order_status['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label"
                                       for="input-order-status"><?php echo $entry_order_cancelled_status; ?></label>
                                <div class="col-sm-10">
                                    <select name="oplata_order_cancelled_status_id" id="input-order-status"
                                            class="form-control">
                                        <?php foreach($order_statuses as $_key => $order_status) { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"
                                        <?php echo (($order_status['order_status_id'] == $oplata_order_cancelled_status_id) ? ('selected') : ('')); ?>
                                        ><?php echo $order_status['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label"
                                       for="input-order-status"><?php echo $entry_order_process_status; ?></label>
                                <div class="col-sm-10">
                                    <select name="oplata_order_process_status_id" id="input-order-status"
                                            class="form-control">
                                        <?php foreach($order_statuses as $_key => $order_status) { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"
                                        <?php echo (($order_status['order_status_id'] == $oplata_order_process_status_id) ? ('selected') : ('')); ?>
                                        ><?php echo $order_status['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label"
                                       for="input-order-reverse-status"><?php echo $entry_order_reverse_status; ?></label>
                                <div class="col-sm-10">
                                    <select name="oplata_order_reverse_status_id"
                                            id="input-order-reverse-status" class="form-control ">
                                        <?php foreach($order_statuses as $_key => $order_status) { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"
                                        <?php echo (($order_status['order_status_id'] == $oplata_order_reverse_status_id) ? ('selected') : ('')); ?>
                                        ><?php echo $order_status['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div><!-- /#tab-order-status -->

                        <div class="tab-pane" id="tab-style">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-style-type"><?php echo $entry_style_theme; ?></label>
                                <div class="col-sm-10">
                                    <select name="oplata_style_type" id="input-style-type" class="form-control">
                                        <option value="light" <?php echo (($oplata_style_type == 'light') ? (' selected') : ('')); ?>><?php echo $entry_light; ?></option>
                                        <option value="dark" <?php echo (($oplata_style_type == 'dark') ? (' selected') : ('')); ?>><?php echo $entry_dark; ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-style-preset"><?php echo $entry_style_preset; ?></label>
                                <div class="col-sm-10">
                                    <select name="oplata_style_preset" id="input-style-preset" class="form-control">
                                        <?php foreach($style_presets as $key => $style_preset) { ?>
                                        <option value="<?php echo $key; ?>"
                                        <?php echo (($oplata_style_preset == $key) ? ('selected') : ('')); ?>
                                        ><?php echo $style_preset; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div><!-- /#tab-style -->
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(() => {
        let $inputProcessPaymentType = $('#input-process-payment-type');
        let $navTabStyle = $('#nav-tab-style');

        if ($inputProcessPaymentType.val() === 'built_in_checkout')
            $navTabStyle.removeClass("hidden");

        $inputProcessPaymentType.on('change', function () {
            if (this.value === 'built_in_checkout'){
                $navTabStyle.removeClass("hidden");
            } else $navTabStyle.addClass("hidden");
        });


        $('#input-environment').on('change', function () {
            let $helpTextBlock = $('#input-environment-help-text');

            if (this.value === 'test') {
                $helpTextBlock.removeClass("hidden");
            } else $helpTextBlock.addClass("hidden");
        })
    })
</script>
<?php echo $footer; ?>
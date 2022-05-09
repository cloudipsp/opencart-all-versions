<h2><?php echo $text_payment_details; ?></h2>
<div class="alert alert-success" id="oplata_transaction_msg" style="display: none"><i class="fa fa-check-circle"></i>&nbsp;test</div>
<table class="table table-striped table-bordered table-hover">
    <tr>
        <td><?php echo $text_fondy_order_id; ?></td>
        <td><?php echo $order['id']; ?></td>
    </tr>
    <tr>
    <tr>
        <td><?php echo $text_payment_id; ?></td>
        <td>
            <span data-toggle="tooltip"
                  title="<?php echo $tooltip_field_payment_id; ?>"
            ><?php echo $order['payment_id']; ?></span>
        </td>
    </tr>
    <tr>
        <td><?php echo $text_order_total; ?></td>
        <td><?php echo $order['formatted_total']; ?>&nbsp;<?php echo $order['currency_code']; ?></td>
    </tr>
    <tr>
        <td><?php echo $text_masked_card; ?></td>
        <td><?php echo $order['masked_card']; ?></td>
    </tr>
    <tr>
        <td><?php echo $text_order_status; ?></td>
        <td><?php echo $order['status']; ?></td>
    </tr>
    <tr>
        <td><?php echo $text_order_last_tran_type; ?></td>
        <td><?php echo $order['last_tran_type']; ?></td>
    </tr>

    <?php if ($order['status'] == 'approved') { ?>
    <?php if (($order['preauth'] == 'Y' && $order['last_tran_type'] == 'purchase')) { ?>
    <tr>
        <td><?php echo $text_charge_the_amount; ?></td>
        <td>
            <div class="input-group col-md-5">
                        <span class="input-group-btn">
                            <button id="btn_capture" class="btn btn-success">
                                <i class="fa fa-check"></i>&nbsp;<?php echo $btn_capture; ?>
                            </button>
                        </span>

                <input type="number" id="input_amount"
                       class="form-control pull-right text-right"
                       min="0.00" step="0.01" max="<?php echo $order['formatted_total']; ?>"
                       value="<?php echo $order['formatted_total']; ?>"
                       placeholder="<?php echo $text_total; ?>"
                >

                <span class="input-group-btn">
                            <button id="btn_reverse" class="button btn btn-primary"
                                    data-toggle="tooltip" title="<?php echo $tooltip_btn_preauth_reverse; ?>"
                            ><i class="fa fa-undo"></i>&nbsp;<?php echo $btn_preauth_reverse; ?></button>
                        </span>
            </div>
        </td>
    </tr>
    <?php } elseif ($order['total']) { ?>
    <tr>
        <td><?php echo $text_refund; ?></td>
        <td>
            <div class="input-group col-md-4">
                <input type="number" id="input_amount"
                       class="form-control pull-right text-right"
                       min="0.00" step="0.01" max="<?php echo $order['formatted_total']; ?>"
                       value="<?php echo $order['formatted_total']; ?>"
                       placeholder="<?php echo $text_total; ?>"
                >

                <span class="input-group-btn">
                        <button class="button btn btn-primary" id="btn_reverse">
                            <i class="fa fa-undo"></i>&nbsp;<?php echo $btn_reverse; ?>
                        </button>
                    </span>
            </div>
        </td>
    </tr>
    <?php } ?>
    <?php } ?>
</table>

<script>
    let $btnCapture = $('#btn_capture'),
        $btnReverse = $("#btn_reverse"),
        $inputAmount = $("#input_amount"),
        spinnerClass = 'fa fa-spinner fa-spin';

    $btnCapture.on('click', function () {
        let amountInputVal = $('#input_amount').val(),
            confirmText = $btnCapture.text().trim() + ` ${amountInputVal} <?php echo $order['currency_code']; ?>?`;

        if (confirm(confirmText)) {
            let $buttonIcon = $btnCapture.find('i'),
                iconButtonClass = $buttonIcon.attr("class");

            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: {'amount': amountInputVal},
                url: '<?php echo $capture_url; ?>',
                beforeSend: function () {
                    $btnCapture.attr('disabled', 'disabled');
                    $buttonIcon.removeClass().addClass(spinnerClass);
                },
                success: function (data) {
                    if ('success_message' in data) {
                        alert(data.success_message);
                    }
                    updatePaymentDetailsTable();
                },
                error: function (jqXHR) {
                    if ('error_message' in jqXHR.responseJSON) {
                        alert(jqXHR.responseJSON.error_message);
                    }
                    console.log(jqXHR);
                    this.done();
                },
                done: function () {
                    $btnCapture.removeAttr('disabled');
                    $buttonIcon.removeClass().addClass(iconButtonClass);
                }
            });
        }
    });

    $btnReverse.on('click', function () {
        if (confirm($btnReverse.text().trim() + '?')) {
            let $buttonIcon = $btnReverse.find('i'),
                iconButtonClass = $buttonIcon.attr("class");

            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: {'amount': $('#input_amount').val()},
                url: '<?php echo $reverse_url; ?>',
                beforeSend: function () {
                    $btnReverse.attr('disabled', 'disabled');
                    $buttonIcon.removeClass().addClass(spinnerClass);
                },
                success: function (data) {
                    if ('success_message' in data) {
                        alert(data.success_message);
                    }
                    updatePaymentDetailsTable();
                },
                error: function (jqXHR) {
                    if ('error_message' in jqXHR.responseJSON) {
                        alert(jqXHR.responseJSON.error_message);
                    }
                    console.log(jqXHR);
                    this.done();
                },
                done: function () {
                    $btnReverse.removeAttr('disabled');
                    $buttonIcon.removeClass().addClass(iconButtonClass);
                },
            });
        }
    });

    const updatePaymentDetailsTable = () => {
        $.ajax({
            url: '<?php echo $upd_payment_detail_table_url; ?>',
            success: function (data) {
                $('#tab-oplata').html(data);
                $('#tab-action').html(data);
            }
        });
    };
</script>
<div style="margin-left:auto;max-width: 340px;display: flex;flex-flow: row wrap;justify-content: space-between;" class="flex-container">
    <div style="margin: auto;margin-left: 0;" class="flex-item">
        <img width="65" src="catalog/view/image/payment/banks/csob.svg" alt="1" class="img-responsive"/>
    </div>
    <div style="margin: auto;margin-left: 0;" class="flex-item">
        <img width="65" src="catalog/view/image/payment/banks/mbank.svg" alt="1" class="img-responsive"/>
    </div>
    <div style="margin: auto;margin-left: 0;" class="flex-item">
        <img width="65" src="catalog/view/image/payment/banks/otp.svg" alt="1" class="img-responsive"/>
    </div>
    <div style="margin: auto;margin-left: 0;" class="flex-item">
        <img width="65" src="catalog/view/image/payment/banks/pabk.svg" alt="1" class="img-responsive"/>
    </div>
</div>
<div style="margin-left:auto;margin-top: 10px; max-width: 340px;display: flex;flex-flow: row wrap;justify-content: space-between;"
     class="flex-container">
    <div style="margin: auto;margin-left: 0;" class="flex-item">
        <img width="65" src="catalog/view/image/payment/banks/prima.svg" alt="1" class="img-responsive"/>
    </div>
    <div style="margin: auto;margin-left: 0;" class="flex-item">
        <img width="65" src="catalog/view/image/payment/banks/sberbank.svg" alt="1" class="img-responsive"/>
    </div>
    <div style="margin: auto;margin-left: 0;" class="flex-item">
        <img width="65" src="catalog/view/image/payment/banks/slsp.svg" alt="1" class="img-responsive"/>
    </div>
    <div style="margin: auto;margin-left: 0;" class="flex-item">
        <img width="65" src="catalog/view/image/payment/banks/vub.svg" alt="1" class="img-responsive"/>
    </div>
</div>
<form action="<?php echo $fondy_bank_args['url']; ?>" method="post">
  <input type="hidden" name="merchant_id" value="<?php echo $fondy_bank_args['merchant_id']; ?>">
  <input type="hidden" name="order_id" value="<?php echo  $fondy_bank_args['order_id']; ?>">
  <input type="hidden" name="order_desc" value="<?php echo  $fondy_bank_args['order_desc']; ?>">
  <input type="hidden" name="amount" value="<?php echo $fondy_bank_args['amount']; ?>">
  <input type="hidden" name="currency" value="<?php echo $fondy_bank_args['currency']; ?>">
  <input type="hidden" name="response_url" value="<?php echo $fondy_bank_args['response_url']; ?>">
  <input type="hidden" name="default_payment_system" value="<?php echo $fondy_bank_args['default_payment_system']; ?>">
  <input type="hidden" name="server_callback_url" value="<?php echo $fondy_bank_args['server_callback_url']; ?>">
  <input type="hidden" name="sender_email" value="<?php echo $fondy_bank_args['sender_email']; ?>">
  <input type="hidden" name="lang" value="<?php echo $fondy_bank_args['lang']; ?>">
  <input type="hidden" name="signature" value="<?php echo $fondy_bank_args['signature']; ?>">
  <div class="buttons">
    <div class="pull-right">
      <input type="submit" value="<?php echo $button_confirm; ?>" class="btn btn-primary" />
    </div>
  </div>
</form>

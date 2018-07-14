<?php if($fondy_bank_data['result'] === false) {?>
<div class="alert alert-warning">
			<?php echo $fondy_bank_data['message']; ?>
        <button type="button" class="close" data-dismiss="alert">×</button>
</div>
<?php }else{?>
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
<div class="buttons">
        <div class="pull-right">
            <a href="<?php echo $fondy_bank_data['url']; ?>" class="btn btn-primary"><?php echo $button_confirm; ?></a>
        </div>
</div>
<?php }?>
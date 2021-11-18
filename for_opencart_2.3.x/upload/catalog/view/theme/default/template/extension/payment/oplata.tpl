<?php if (isset($error_message)): ?>
<div class="alert alert-warning">
    <?php echo $error_message; ?>
    <button type="button" class="close" data-dismiss="alert">×</button>
</div>
<?php elseif (!empty($fondy_options)): ?>
<link rel="stylesheet" href="https://pay.fondy.eu/latest/checkout-vue/checkout.css">
<div id="checkout-container"></div>
<script>
    const initFondyWidget = () => fondy("#checkout-container", <?php echo $fondy_options; ?>);

    if (!document.getElementById('fondy_script')) {
        let fondyScript = document.createElement('script');
        fondyScript.src = 'https://pay.fondy.eu/latest/checkout-vue/checkout.js';
        fondyScript.id = 'fondy_script'
        fondyScript.onload = initFondyWidget;
        document.head.appendChild(fondyScript);
    } else initFondyWidget();
</script>
<?php else: ?>
<div class="buttons">
    <div class="pull-right">
        <a href="<?php echo $checkout_url; ?>" class="btn btn-primary"><?php echo $button_confirm; ?></a>
    </div>
</div>
<?php endif; ?>

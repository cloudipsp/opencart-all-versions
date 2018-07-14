<?php if ($fondy['result'] == false){ echo $fondy['message']; die;}?>
<div style="display: none" id="checkout">
    <div id="checkout_wrapper" ></div>
</div>       
<script type="text/javascript">
var checkoutStyles = {
<?php echo $styles; ?>
	};

    function checkoutInit(url) {
        $ipsp('checkout').scope(function() {
            //this.setModal(true);
			this.setCssStyle(checkoutStyles);
            this.setCheckoutWrapper('#checkout_wrapper');
            this.addCallback(__DEFAULTCALLBACK__);
            this.action('decline',function(data,type){
                console.log(data);
            });
            this.action('show', function(data) {
                $('#checkout_loader').remove();
                $('#checkout').show();
            });
            this.action('hide', function(data) {
                $('#checkout').hide();
            });
            this.action('resize', function(data) {
                $('#checkout_wrapper').height(data.height);
            });
            this.loadUrl(url);
        });
    };
</script>
<div class="buttons">
        <div class="pull-right">
            <a onclick="checkoutInit('<?php echo $fondy['url'] ?>');" class="btn btn-primary"><?php echo $button_confirm; ?></a>
        </div>
</div>
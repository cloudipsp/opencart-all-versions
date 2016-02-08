
<script src="catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js" type="text/javascript"></script>
<link href="catalog/view/javascript/jquery/magnific/magnific-popup.css" type="text/css" rel="stylesheet" media="screen">
<style>
#checkout_wrapper {
position: relative;
background: #FFF;
padding: 20px;
width:auto;
max-width: 500px;
margin: 20px auto;
}
</style>

<div class="buttons">

    <div class="pull-right"><a onclick="callcbox();"  id="payment" class="btn btn-primary"><span><?php echo $button_confirm; ?></span></a> </div>
</div>
<div style="display: none" id="checkout">
    <div id="checkout_wrapper" ></div>
</div>       
<script type="text/javascript">
    function checkoutInit(url) {
        $ipsp('checkout').scope(function() {
            //this.setModal(true);
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
                $('#checkout_wrapper').width(480).height(data.height);
            });
            this.loadUrl(url);
        });
    };
    var button = $ipsp.get("button");
    button.setMerchantId(<?php echo $oplata_args['merchant_id']; ?>);
    button.setAmount(<?php echo $oplata_args['amount']; ?>, '<?php echo $oplata_args['currency']; ?>', true);
    button.setHost('api.fondy.eu');
    button.addParam('order_desc','<?php echo $oplata_args['order_desc']; ?>');
    button.addParam('order_id','<?php echo $oplata_args['order_id']; ?>');
    button.addParam('lang','<?php echo $oplata_args['lang']; ?>');
    button.addParam('server_callback_url','<?php echo $oplata_args['server_callback_url']; ?>');
    button.addParam('sender_email','<?php echo $oplata_args['sender_email']; ?>');
    button.setResponseUrl('<?php echo $oplata_args['response_url'] ?>');
        checkoutInit(button.getUrl());
</script>
<script>
function callcbox() {
    $.magnificPopup.open({
        items: {
            preloader: true,

            src: $('#checkout_wrapper'),
            type: 'inline'
        }
    });
}
</script>
<script>
    $(document).ready(function(){
        $.magnificPopup.open({
            preloader: true,

            items: {
                src: $('#checkout_wrapper'),
                type: 'inline'
            },

        });
    });
</script>
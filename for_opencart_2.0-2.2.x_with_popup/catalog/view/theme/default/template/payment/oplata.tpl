
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
<?php if ($fondy['result'] == false) {echo $fondy['message']; die;}?>
<div style="display: none" id="checkout">
    <div id="checkout_wrapper" ></div>
</div>       
<script type="text/javascript">
    function checkoutInit(url) {
        $ipsp('checkout').scope(function() {
           
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
   
</script>
<script>
function callcbox() {
	checkoutInit('<?php echo $fondy['url'] ?>');
    $.magnificPopup.open({
        items: {
            preloader: true,

            src: $('#checkout_wrapper'),
            type: 'inline'
        }
    });
}
</script>
<div class="buttons">
        <div class="pull-right">
            <a onclick="callcbox()" class="btn btn-primary"><?php echo $button_confirm; ?></a>
        </div>
</div>
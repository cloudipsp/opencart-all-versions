<?php if (isset($this->session->data ['oplata_error'])) { ?>
<div class="warning"><?php echo $this->session->data ['oplata_error']; ?><img src="catalog/view/theme/default/image/close.png" alt="" class="close"></div>
<?php }; unset($this->session->data['oplata_error']); ?>
<div class="buttons">
    <div class="right"><a onclick="callcbox();"  id="payment" class="button"><span><?php echo $button_confirm; ?></span></a> </div>
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
        checkoutInit('<?php echo $fondy['url'] ?>');
</script>

<script>
function callcbox() {
        $.colorbox({inline:true, scrolling:false, innerWidth:480,innerHeight:641, href:"#checkout_wrapper"});
}
</script>
<?php if ($this->request->get['route'] == 'checkout/confirm') { ?>
<script>
    $(document).ready(function(){
        $.colorbox({inline:true, scrolling:false, innerWidth:480,innerHeight:641, href:"#checkout_wrapper"});
    });
</script>
<?php } ?>
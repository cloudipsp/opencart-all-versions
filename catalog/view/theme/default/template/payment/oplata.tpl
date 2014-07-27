<form action="<?php echo $action ?>" method="post" id="payments">
    <?php
    foreach ($oplata_args as $k => $v) {
        echo "<input type=\"hidden\" name=\"{$k}\" value=\"{$v}\" />";
    }
    ?>
</form>
<div class="buttons">
    <div class="right"><a id="payment" class="button"><span><?php echo $button_confirm; ?></span></a> </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $("#payment").click(function(event){
            $.ajax({
                type: 'GET',
                url: 'index.php?route=payment/oplata/confirm',
                success: function () {
                    $('#payments').submit();
                }
            });
            return false;
        });
    });
</script>
<?php if($fondy_data['result'] === false) {?>
<div class="alert alert-warning">
			<?php echo $fondy_data['message']; ?>
        <button type="button" class="close" data-dismiss="alert">×</button>
</div>
<?php }else{?>
<div class="buttons">
        <div class="pull-right">
            <a href="<?php echo $fondy_data['url']; ?>" class="btn btn-primary"><?php echo $button_confirm; ?></a>
        </div>
</div>
<?php }?>
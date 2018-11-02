<?php defined('SYSTEM_INIT') or die('Invalid Usage'); ?>
<div class="payment-page">
  <div class="cc-payment">
    <div class="payment-from">
			<?php
				if (!isset($error))
				{
					echo  $frm->getFormHtml();
				}
				else
				{
			?>
					<div class="alert alert-danger"><?php echo $error?><div>
            <?php 
				}
			?>
            <div id="ajax_message"></div>
    </div>
  </div>
</div>
<script type="text/javascript">
    $(function(){
		setTimeout(function(){ $('form[name="frmPayuIndia"]').submit() }, 2000);
	})
</script>
<script type="text/javascript">
<?php if($status == 1){ ?>
	window.opener.socialRedirect('<?php echo $url; ?>');
<?php }else{ ?>
	window.opener.socialError('<?php echo $msg; ?>');
<?php } ?>
</script>
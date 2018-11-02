<ul class="centered_nav">
	<li><a href="<?php echo FatUtility::generateUrl("traveler","edit",array($user_id))?>" <?php if($action == "edit"){?> class="active" <?php } ?>>Edit</a></li>
	<?php if($canEdit){ ?>
		<li><a href="<?php echo FatUtility::generateUrl("traveler","password",array($user_id))?>" <?php if($action == "password"){?> class="active" <?php } ?>>Password</a></li>
	<?php } ?>
                	<?php if($canViewBankAccount){ ?>
	<li><a href="<?php echo FatUtility::generateUrl("traveler","bankAccount",array($user_id))?>" <?php if($action == "bankAccount"){?> class="active" <?php } ?>>Bank Account</a></li>
	<?php } ?>
	<?php if($canViewMessage){ ?>
	<li><a href="<?php echo FatUtility::generateUrl("traveler","message",array($user_id))?>" <?php if($action == "message"){?> class="active" <?php } ?>>Messages</a></li>
	<?php } ?>

</ul>
<br/>
<script type="text/javascript">
$(document).ready(function(){
	getProfile(user_id);	
});
</script>


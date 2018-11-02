<ul class="centered_nav">
	<li><a href="<?php echo FatUtility::generateUrl("host","edit",array($user_id))?>" <?php if($action == "edit"){?> class="active" <?php } ?>>Edit</a></li>
	<?php if($canEdit){ ?>
	<li><a href="<?php echo FatUtility::generateUrl("host","password",array($user_id))?>" <?php if($action == "password"){?> class="active" <?php } ?>>Password</a></li>
	<?php } ?>
	<?php if($canViewMessage){ ?>
	<li><a href="<?php echo FatUtility::generateUrl("host","message",array($user_id))?>" <?php if($action == "message"){?> class="active" <?php } ?>>Messages</a></li>
	<?php } ?>

	<?php if($canViewBankAccount){ ?>
	<li><a href="<?php echo FatUtility::generateUrl("host","bankAccount",array($user_id))?>" <?php if($action == "bankAccount"){?> class="active" <?php } ?>>Bank Account</a></li>
	<?php } ?>
	<?php if($canViewWallet){ ?>
	<li><a href="<?php echo FatUtility::generateUrl("host","transactions",array($user_id))?>" <?php if($action == "transactions"){?> class="active" <?php } ?>>Transactions</a></li>
	<?php } ?>
	
</ul>
<script>
$(document).ready(function(){
	getProfile(user_id);	
})

</script>


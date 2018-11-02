<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
//Info::test($comments);
?>
<div class="areabody">
<div class="row">	
	<div class="col-sm-6">
	<div class="repeatedrow">
		<h3><i class="ion-bag icon"></i> Bank Detail</h3>
                <div class="rowbody">
                <?php if(!empty($data)){?>
		
			<div class="listview">
				<dl class="list">
					<dt>Bank Name</dt>
					<dd><?php echo $data['bankaccount_bank_name']?></dd>
				</dl>
				<dl class="list">
					<dt>Branch Name</dt>
					<dd><?php echo $data['bankaccount_branch']; ?></dd>
				</dl>
				<dl class="list">
					<dt>Account No</dt>
					<dd><?php echo $data['bankaccount_account_no']?></dd>
				</dl>
				<dl class="list">
					<dt>Account Name</dt>
					<dd><?php echo $data['bankaccount_account_name']?></dd>
				</dl>
				<dl class="list">
					<dt>Account Address</dt>
					<dd><?php echo $data['bankaccount_account_address']?></dd>
				</dl>
				<dl class="list">
					<dt>Account Ifc Code</dt>
					<dd><?php echo $data['bankaccount_ifsc_code']?></dd>
				</dl>
				
				
				
			</div>
                <?php }else{?>
                    <div class="listview">
				<dl class="list">
					<dt>Not Defined</dt>
					
				</dl>
                    </div>
                    
                    <?php }?>
		</div>    
	</div>
	</div>

	
	
</div>
</div>
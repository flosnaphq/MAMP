<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
//Info::test($comments);
?>
<div class="sortbar">
	<aside class="grid_1">
		 <a class="themebtn btn-default btn-sm" onclick="addNewComment('<?php echo $cancel_id;?>');return false;" href="javascript:;">
                               Add New
          </a> 
		 
	</aside>  
	
</div>
<?php if(!empty($comments)){ ?>
<ul class="medialist">
	
		<?php foreach($comments as $comment){ 
			if(empty($comment['user_id'])){
				$comment['user_firstname'] = 'Admin';
				$comment['user_lastname'] = '';
				$comment['user_email'] = '';
				$user_type = 'Admin';
			}
			else{
				$user_type = Info::getUserTypeByKey($comment['user_type']);
			}
			
		?>
			<li>
				<span class="grid first">
					<figure class="  avtar bgm-<?php echo MyHelper::backgroundColor($comment['user_firstname'][0])?>"><?php echo substr($comment['user_firstname'],0,1)?></figure>
					
				</span>    
				<div class="grid second">
					<div class="desc">
						<span class="name"><?php echo $comment['user_firstname'].' '.$comment['user_lastname'].' ('.$user_type.')';?> <span class="lightxt"><span></span><?php echo $comment['user_email']?><span></span></span> </span>
						<div><?php echo nl2br($comment['comment_comment']); ?></div>
					</div>
				</div>    
				<span class="grid third">
					<span class="date"><i class="icon ion-ios-clock-outline"></i> <?php echo FatDate::format($comment['comment_datetime'],true);?></span>
				</span>
			   
			</li>
		<?php } ?>
</ul>
<?php }else{ ?>
	<span>No Record Found</span>
<?php } ?>
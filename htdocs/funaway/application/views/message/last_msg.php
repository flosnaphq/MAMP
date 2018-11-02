<?php
defined('SYSTEM_INIT') or die('Invalid Usage');

if(!empty($records)){
	foreach($records as $record){ 
		$last_message_id = $record['message_id'];
		if(!empty($record['user_id'])){
			if($record['user_id'] == User::getLoggedUserId()){
				$user_name = Info::t_lang('ME');
			}
			else{
				$user_name = @$record['user_firstname'].' '.@$record['user_lastname'];
			}
			
		}
		else{
			$user_name ='Admin';
		}
		$li_class = $record['message_user_id']!= User::getLoggedUserId()?'person--first':'person--second';
				$media_class = $record['message_user_id'] != User::getLoggedUserId()?'media--left':'media--right';
		?>
		<li class="person <?php echo $li_class; ?>" >
			<div class="media person__media">
				<div class="media__figure <?php echo $media_class; ?>">
				   <a class="person__image">
						<img src="<?php echo FatUtility::generateUrl('image','user',array($record['message_user_id'],220,220))?>" alt="" class="image">
					</a>
					
				</div>
				<div class="media__body">
					<div class="person__comment">
						<ul class="list list--vertical comment__list">
							<li class="comment">
								<?php echo nl2br($record['message_text']);?>
								<br>
								<em> <?php echo Info::t_lang('BY').' '.$user_name; ?></em>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</li>
		<?php 
		} 
}
 
 
 ?>
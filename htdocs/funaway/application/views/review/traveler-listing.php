<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
if(!empty($arr_listing)){
?>
<table class="table table--fixed table--bordered table--responsive messages-table">
		<tbody>
            <?php foreach($arr_listing as $review){ ?>
                <tr class="message">
                    <td class="message__details message__details--traveler" data-label="Details">
                        <div class="media">
                            <div class="media__figure media--left">
                                <a href="<?php echo FatUtility::generateUrl('activity','detail',array($review['activity_id']))?>"><img src="<?php echo FatUtility::generateUrl('image','activity',array($review['activity_image_id'],100,100))?>"/></a>
                            </div>
                            <div class="media__body">
                                <h6 class="message__heading" style="line-height:1.2">
                                    <a href="<?php echo FatUtility::generateUrl('activity','detail',array($review['activity_id']))?>"><?php echo $review['activity_name'];?></a>
                                </h6>
                                
                                <span class="regular-text"><small><?php echo date('M d Y',strtotime($review[Reviews::DB_TBL_PREFIX.'date']))?> </small></span>
                                <?php echo Info::rating($review[Reviews::DB_TBL_PREFIX.'rating'])?>
                                <p class="regular-text">
                                <?php echo $review[Reviews::DB_TBL_PREFIX.'content'];?>
                                </p>
                            </div>
                        </div>
                        
                    </td><td></td>
                    
                </tr>
        
            <?php if(isset($messages[$review['review_id']])){
                foreach($messages[$review['review_id']] as $message){
                    if($message['reviewmsg_user_id']){
                    ?>
                        <tr class="message replied">
                            <td class="message__details" data-label="Details">
                                <h6 class="message__heading"><?php if($message['user_id']) echo $message['user_full_name']; elseif($message['admin_id']) echo $message['admin_name']; echo ' '. Info::t_lang('REPLIED');?> </h6>
                                
                                <p class="regular-text"><small><?php echo date('M d Y H:i A',strtotime($message['reviewmsg_added_on']))?> </small></p>
                                
                                <p class="regular-text">
                                <?php echo $message['reviewmsg_message'];?>
                                </p>
                            </td><td></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
        
		<?php } ?>
	  
	</tbody>
</table>				
                                
                               
    <?php                         
        if($totalPage>1){
	?>
	
	
	<nav class="pagination text--center">
        <ul class="list list--horizontal no--margin-bottom visible-on--desktop">      
        <?php
        echo FatUtility::getPageString('<li><a href="javascript:void(0);" onclick="listing(xxpagexx,'.$activity_id.');">xxpagexx</a></li>', 
        $totalPage, $page, $lnkcurrent = '<li class="selected"><a href="javascript:void(0);" >xxpagexx</a></li>', '  ', 5, 
        '<li class="more"> <a href="javascript:void(0);" onclick="listing(xxpagexx,'.$activity_id.');"><span class="ink animate" style="height: 35px; width: 35px; top: 0.5px; left: 4px;"></span></a></li>', 
        ' <li class="more"><a href="javascript:void(0);" onclick="listing(xxpagexx,'.$activity_id.');"><span class="ink animate" style="height: 35px; width: 35px; top: 0.5px; left: 4px;"></span></a></li>', 
        '<li class="prev"> <a href="javascript:void(0);" onclick="listing(xxpagexx,'.$activity_id.');"></a></li>', 
        '<li class="next"> <a href="javascript:void(0);" onclick="listing(xxpagexx,'.$activity_id.');"></a></li>');
        ?>
        </ul>
    </nav>
	
	<?php
        }
    }else{
        echo Helper::noRecord(Info::t_lang('NO_REVIEWS'));
    }	
?>
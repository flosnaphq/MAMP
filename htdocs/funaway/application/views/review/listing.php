<?php
if(!empty($arr_listing)){
?>
<table class="table table--inner">
    <?php foreach($arr_listing as $review){ ?>
    <tr>
        <td class="no--padding">
            <table class='table table--fixed table--bordered table--responsive messages-table'>
                <tbody>

                    <tr class="message">
                        <td class="message__details" data-label="Details">
                            <h6 class="message__heading"><?php if($review['abreport_user_id']) echo $review['user_name']; else echo $review['review_user_name']?></h6>
                            <p class="regular-text"><small><?php echo date('M d Y',strtotime($review[Reviews::DB_TBL_PREFIX.'date']))?> </small></p>
                            <?php echo Info::rating($review[Reviews::DB_TBL_PREFIX.'rating'])?>
                            <p class="regular-text">
                            <?php echo $review[Reviews::DB_TBL_PREFIX.'content'];?>
                            </p>
                        </td>
                        <td class="message__actions" data-label="Action" style="vertical-align:top">
                            <nav class="buttons__group" role="navigation">
                                <?php
                                
                                if($review['abreport_taken_care'] == null){
                                    ?>
                                    <a href="#abuse-review" onclick= "markAsInappropriate(<?php echo $review['review_id']?>)" class="abuse-review button button--small button--fill button--dark"><?php echo Info::t_lang('MARK_AS_SPAM')?></a>
                                        
                                    <?php
                                }
                                elseif($review['abreport_taken_care'] == 0){
                                        ?>
                                        <span class="stats_pending button button--icon button--label button--orange"><?php echo Info::t_lang('PENDING')?> <span><svg class="icon icon--stop"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-stop"></use></svg></span></span>
                                        <?php
                                }
                                elseif($review['abreport_taken_care'] == 1){
                                    ?>
                                    <span class="stats_lbl button button--icon button--label button--green"><?php echo Info::t_lang('INAPPROPRIATE_APPROVED')?> <span><svg class="icon icon--check"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-check"></use></svg></span></span>
                                    <?php
                                }
                                elseif($review['abreport_taken_care'] == 2){
                                    ?>
                                    <span class="stats_decline button button--icon button--label button--red"><?php echo Info::t_lang('DECLINE')?> <span><svg class="icon icon--cross"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-cross"></use></svg></span></span>
                                    <?php
                                }
                                else{
                                    ?>
                                        <a href="#abuse-review" onclick= "markAsInappropriate(<?php echo $review['review_id']?>)" class="abuse-review button button--small button--fill button--dark"><?php echo Info::t_lang('MARK_AS_SPAM')?></a>
                                        
                                    <?php
                                }
                                
                                if($review['hasHostReplied'] < 1){
                                    ?>
                                    
                                    <a href="#abuse-review" onclick= "replyToReview(<?php echo $review['review_id']?>);" class="abuse-review button button--small button--fill button--dark"><?php echo Info::t_lang('REPLY_TO_REVIEW')?></a>
                                    
                                    <?php
                                }
                                ?>
                            </nav>
                        </td>
                    </tr>
                    <?php if(isset($messages[$review['review_id']])){
                        foreach($messages[$review['review_id']] as $message){
                            if($message['reviewmsg_user_id']){
                            ?>
                                <tr class="message replied">
                                    <td class="message__details" data-label="Details">
                                        <h6 class="message__heading"><?php if($message['user_id']) echo $message['user_full_name']; elseif($message['admin_id']) echo $message['admin_name']; echo ' '. Info::t_lang('REPLIED');?></h6>
                                        <p class="regular-text"><small><?php echo date('M d Y',strtotime($message['reviewmsg_added_on']))?> </small></p>
                                        
                                        <p class="regular-text">
                                        <?php echo $message['reviewmsg_message'];?>
                                        </p>
                                    </td><td>&nbsp;</td>
                                </tr>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                      
                </tbody>
            </table>
        </td>
    </tr>
<?php } ?>
    </table>

<?php                         
   if($totalPage>1){
?>
<nav class="pagination text--center">
    <ul class="list list--horizontal no--margin-bottom">      
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
}
else{
	echo Helper::noRecord(Info::t_lang('NO_REVIEWS'));
}
?>
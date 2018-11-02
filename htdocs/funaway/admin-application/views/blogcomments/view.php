<?php defined('SYSTEM_INIT') or die('Invalid Usage'); ?> 

<div class="fixed_container">
    <div class="row">
        <div class="col-sm-12">
            <h1>Manage Blog Comment</h1>
            <section class="section">
                <div class="sectionhead"><h4>Comment Details </h4>
					<a href="<?php echo FatUtility::generateUrl('blogcomments'); ?>" class="themebtn btn-default btn-sm"><i class="ion-chevron-left icon margin-right"></i>Back to Listing</a>
				</div>
                <div class="sectionbody space">
                    <table class="table_form_vertical">
                        <tbody>
                            <tr>
                                <td><strong>Name:</strong> <?php echo ucfirst($comment_data['comment_author_name']); ?></td>
                                <td><strong>Email:</strong> <?php echo $comment_data['comment_author_email']; ?></td>
                                <td><strong>IP Address:</strong> <?php echo $comment_data['comment_ip']; ?></td>
							</tr>
                            <tr>
                                <td><strong>Status:</strong> <?php
                                if ($comment_data['comment_status'] == 1) {
                                    echo 'Approved';
                                } elseif ($comment_data['comment_status'] == 2) {
                                    echo 'Deleted';
                                } else {
                                    echo 'Pending';
                                }
                                ?> </td>

                                <td><strong>Date:</strong> <?php echo FatDate::datePickerFormat($comment_data['comment_date_time']); ?></td>

                                <td ><strong>User Agent:</strong> <?php echo $comment_data['comment_user_agent']; ?></td>
                            </tr>
                            <tr>	
                                <td><strong>Comment:</strong></td>
                                <td style="text-align:justify"><?php echo $comment_data['comment_content']; ?></td>
                            </tr>	
                        </tbody>
                    </table> 
					
                </div>
            </section>
        </div>	
    </div>
</div>

<?php 
	if ($canEdit === true) {  
		$frmComment->setFormTagAttribute('id', 'frmBlogComments');
		$frmComment->setFormTagAttribute('class', 'web_form');
		$frmComment->setFormTagAttribute('onsubmit', 'updateStatus(this, blogComment); return(false);' );		
		$frmComment->setFormTagAttribute("action",FatUtility::generateUrl("Blogcomments","updateStatus"));
		$frmComment->setValidatorJsObjectName('blogComment');
		//$frmComment->developerTags['fld_default_col'] = 12;
		$comment_status= $frmComment->getField('comment_status');
		$comment_status->requirements()->setRequired();
		$comment_status->setRequiredStarWith(2);
	 
?>							

<div class="fixed_container">
	<div class="row">
		<div class="col-sm-12">             
			<section class="section">
				<div class="sectionhead"><h4>Edit Comment Status</h4></div>
				<div class="sectionbody space">
					<?php echo $frmComment->getFormHtml(); ?>
				</div>
			</section>
		</div>	
	</div>
</div>	
<?php } ?>

 
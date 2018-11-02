<?php defined('SYSTEM_INIT') or die('Invalid Usage'); ?> 
<div class="fixed_container">
    <div class="row">
        <div class="col-sm-12">  
            <h1>Manage Blog Contribution</h1>
            <section class="section">
                <div class="sectionhead"><h4>Contribution Details </h4></div>
                <div class="sectionbody space">
                    <table class="table_form_vertical">
                        <tbody>
                            <tr>
                                <td><strong>First Name:</strong> <?php echo ucfirst($contribution_data['contribution_author_first_name']); ?></td>
                                <td><strong>Last Name:</strong> <?php echo ucfirst($contribution_data['contribution_author_last_name']); ?></td>
                                <td><strong>Email:</strong> <?php echo $contribution_data['contribution_author_email']; ?></td>
							</tr>
                            <tr>
                                <td><strong>Phone:</strong> <?php echo $contribution_data['contribution_author_phone']; ?></td>
                                <td><strong>Added Date:</strong> <?php echo FatDate::datePickerFormat($contribution_data['contribution_date_time']); ?></td>
                                <td><strong>Status:</strong><?php
                                    if ($contribution_data['contribution_status'] == 1) {
                                        echo 'Approved';
                                    } elseif ($contribution_data['contribution_status'] == 2) {
                                        echo 'Posted/Published';
                                    } elseif ($contribution_data['contribution_status'] == 3) {
                                        echo 'Rejected';
                                    } else {
                                        echo 'Pending';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php if (!empty($contribution_data['contribution_file_name'])) { ?>
							<tr>	
								<td><strong>File:</strong> <a class="a_class" href="<?php echo FatUtility::generateUrl('blogcontributions', 'download', array( $contribution_data['contribution_id'] ) ); ?>"><?php echo $contribution_data['contribution_file_display_name']; ?> <input type="button" class="btn_cursor" name="download" id="download" value="Download"/></a></td>
								<td></td>
								<td></td>
							</tr>     
							<?php
								}
							?>
							<tr>	
								<td>
								
								</td>
								<td></td>
								<td>
								
								
								</td>
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
										
		$frmContributionStatusUpdate->setFormTagAttribute('id', 'frmBlogContributions');
		$frmContributionStatusUpdate->setFormTagAttribute('class', 'web_form');
		$frmContributionStatusUpdate->setFormTagAttribute('onsubmit', 'updateStatus(this, blogContriValidator); return(false);' );		
		$frmContributionStatusUpdate->setFormTagAttribute("action",FatUtility::generateUrl("Blogcontributions","updateStatus"));
		$frmContributionStatusUpdate->setValidatorJsObjectName('blogContriValidator');
		
		$contribution_status= $frmContributionStatusUpdate->getField('contribution_status');
		$contribution_status->requirements()->setRequired();
		$contribution_status->setRequiredStarWith(2);
		 
?>

<div class="fixed_container">
	<div class="row">
		<div class="col-sm-12">             
			<section class="section">
				<div class="sectionhead"><h4>Edit Contribution Status</h4></div>
				<div class="sectionbody space">
					<?php echo $frmContributionStatusUpdate->getFormHtml(); ?>
				</div>
			</section>
		</div>	
	</div>
</div>	
<?php } ?>

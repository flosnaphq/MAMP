<?php defined('SYSTEM_INIT') or die('Invalid Usage'); ?>
<?php
	$frmContributions->setFormTagAttribute('id', 'frmSearch');
	$frmContributions->setFormTagAttribute('class', 'web_form');
	$frmContributions->setFormTagAttribute('onsubmit', 'searchContributions(this); return(false);' );		 
	$frmContributions->setFormTagAttribute("action",FatUtility::generateUrl("Blogcontributions","listContributions"));  
         
	$contribution_author_first_name= $frmContributions->getField('contribution_author_first_name');
	$contribution_author_first_name->addFieldTagAttribute('id', 'contribution_author_first_name');
	
	$contribution_status= $frmContributions->getField('contribution_status');
	$contribution_status->addFieldTagAttribute('id', 'contribution_status');
	 
	$btn_submit= $frmContributions->getField('btn_submit');
	$btn_submit->addFieldTagAttribute('id', 'btn_submit');
	$btn_submit->addFieldTagAttribute('onclick', 'document.frmSearch.page.value = 1;'); 
	$cancelBtn= $frmContributions->getField('cancel_search');	 
	$cancelBtn->addFieldTagAttribute('class', 'cancel_form');	
?>

<div class="fixed_container" >
    <div class="row">
        <div class="col-sm-12">  
            <h1>Blog Contributions</h1>  
            <section class="section searchform_filter">
                <div class="sectionhead">
                    <h4>Search Blog Contributions</h4>
                </div>
                <div style="display:none;" class="sectionbody  togglewrap">
                    <?php echo $frmContributions->getFormTag(); ?>
					<table class="table_form_vertical">
						<tbody>
							<tr>
								<td width="20%">First Name<br><?php echo $frmContributions->getFieldHtml('contribution_author_first_name'); ?></td>
								<td width="20%">Contribution Status<br><?php echo $frmContributions->getFieldHtml('contribution_status'); ?></td>
								<td width="20%"><br>
									<?php 
										echo $frmContributions->getFieldHtml('btn_submit'); 
										echo $frmContributions->getFieldHtml('cancel_search');
										echo $frmContributions->getFieldHtml('page'); 
									?>
													 
								</td>
							</tr>
						</tbody>
					</table>
					</form><?php echo $frmContributions->getExternalJs(); ?>
                </div>
            </section>
        </div>
        <div class="col-sm-12"> 
            <section class="section">
                <div class="sectionhead">
                    <h4>Blog Contributions List</h4>
                </div>
                <div id="contributionsList"></div>
            </section>
        </div>
	</div>
</div>

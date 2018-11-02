<?php defined('SYSTEM_INIT') or die('Invalid Usage'); ?>
<?php
	$frmComments->setFormTagAttribute('id', 'frmSearch');
	$frmComments->setFormTagAttribute('class', 'web_form');
	$frmComments->setFormTagAttribute('onsubmit', 'searchComments(this); return(false);' );		 
	$frmComments->setFormTagAttribute("action",FatUtility::generateUrl("Blogcomments","listComments"));  
         
	$comment_author_name= $frmComments->getField('comment_author_name');
	$comment_author_name->addFieldTagAttribute('id', 'comment_author_name');
	
	$comment_status= $frmComments->getField('comment_status');
	$comment_status->addFieldTagAttribute('id', 'comment_status');
	 
	$btn_submit= $frmComments->getField('btn_submit');
	$btn_submit->addFieldTagAttribute('id', 'btn_submit');
	$btn_submit->addFieldTagAttribute('onclick', 'document.frmSearch.page.value = 1;');
	$cancelBtn= $frmComments->getField('cancel_search');	 
	$cancelBtn->addFieldTagAttribute('class', 'cancel_form');	
?>

<div class="fixed_container" >
    <div class="row">
        <div class="col-sm-12">  
            <h1>Blog Comments</h1> 
            <section class="section searchform_filter">
                <div class="sectionhead">
                    <h4>Search Blog Comments</h4>
                </div>
                <div style="display:none;" class="sectionbody  togglewrap">
                    <?php echo $frmComments->getFormTag(); ?>
					<table class="table_form_vertical">
						<tbody>
						<tr>
							<td width="20%">Comment Author Name<br><?php echo $frmComments->getFieldHtml('comment_author_name'); ?></td>
							<td width="20%">Comment Status<br><?php echo $frmComments->getFieldHtml('comment_status'); ?></td>
							<td width="20%"><br>
							<?php 
								echo $frmComments->getFieldHtml('btn_submit'); 
								echo $frmComments->getFieldHtml('cancel_search'); 
								echo $frmComments->getFieldHtml('page'); 
							?>												 
							</td>
						</tr>
						</tbody>
					</table>
					</form><?php echo $frmComments->getExternalJs(); ?>
                </div>
            </section>
        </div>
        <div class="col-sm-12">  
            <section class="section">
                <div class="sectionhead">
                    <h4>Blog Comments List</h4>                     
                </div>
                <div class="sectionbody" id="post-type-list"></div>
            </section>
        </div>
	</div>
</div>

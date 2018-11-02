
<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<section class="section">
<div class="sectionhead">
<h4>Add/Update </h4><a html="javascript:;" onclick = "closeForm()" class="close-form"><i class="ion-close-round"></i></a>
</div>
<div class="sectionbody space">
						
												<?php 
							$frm->setValidatorJsObjectName ( 'formValidator' );
							$frm->setRequiredStarWith(FORM::FORM_REQUIRED_STAR_POSITION_NONE);
							$frm->setFormTagAttribute ( 'class', 'web_form' );
							$frm->developerTags['fld_default_col'] = 12;
							$frm->setFormTagAttribute ( 'action', FatUtility::generateUrl("blocks","setup") );
							$frm->setFormTagAttribute ( 'onsubmit', 'submitForm(formValidator,"action_form"); return(false);' );
							echo  $frm->getFormHtml();
						?>	
</div>

</section>						


	
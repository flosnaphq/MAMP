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
							$frm->setFormTagAttribute ( 'onsubmit', 'submitForm(formValidator); return(false);' );
							$frm->setFormTagAttribute ( 'class', 'web_form' );
							$frm->developerTags['fld_default_col'] = 6;
							$frm->setFormTagAttribute ( 'action', FatUtility::generateUrl("locations","region-action") );
							$frm->setRequiredStarWith(Form::FORM_REQUIRED_STAR_WITH_NONE);
							echo  $frm->getFormHtml();
						?>	
</div>
</section>						
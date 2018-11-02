<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section">
	<div class="sectionhead">
		<h4>Add/Update </h4>
		<a html="javascript:;" onclick = "closeForm()" class="close-form">
			<i class="ion-close-round"></i>
		</a>
	</div>
	<div class="sectionbody space">
		<?php 
			$frm->setValidatorJsObjectName ( 'formValidator' );
			$frm->setRequiredStarWith(FORM::FORM_REQUIRED_STAR_POSITION_NONE);
			$frm->setFormTagAttribute ( 'class', 'web_form' );
			$frm->developerTags['fld_default_col'] = 6;
			$frm->setFormTagAttribute ( 'action', FatUtility::generateUrl("testimonials","setup") );
			$frm->setFormTagAttribute ( 'onsubmit', 'submitForm(formValidator,"action_form"); return(false);' );
			$frm->getField('profile_pic')->developerTags['col'] = 4;
			$frm->getField('image')->developerTags['col'] = 4;
			$frm->getField(Testimonial::DB_TBL_PREFIX.'name')->developerTags['col'] = 4;
			
			$frmFld = $frm->getField(Testimonial::DB_TBL_PREFIX.'content');
			$frmFld->setFieldTagAttribute('onKeyup', 'limitCharacters(this)');
			$frmFld->setFieldTagAttribute('maxlength', '200');
			$frmFld->developerTags['col'] = 12;
			
			$frm->getField(Testimonial::DB_TBL_PREFIX.'status')->developerTags['col'] = 3;
			echo  $frm->getFormHtml();
		?>	
	</div>
</section>
<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setValidatorJsObjectName ( 'formValidator' );
$frm->setFormTagAttribute ( 'class', 'web_form' );
$frm->developerTags['fld_default_col'] = 12;
$frm->setFormTagAttribute ( 'action', FatUtility::generateUrl("configurations","setupMaintenanceSettings") );
$frm->setRequiredStarWith(Form::FORM_REQUIRED_STAR_WITH_NONE);

$fld = $frm->getField('CONF_MAINTENANCE');
$fld->developerTags['col'] = 4;

$frm->getField('CONF_MAINTENANCE_TEXT')->setFieldTagAttribute('id','conf_maintenance_text');

?>

	<div class="fixed_container">
		<div class="row">
		  <div class="col-sm-12">  
			<h1>Maintenance Mode Settings</h1>   
			<section class="section">
				<div class="sectionhead">
					<h4>Maintenance Mode Settings</h4>
				</div>
				<div class="sectionbody space">
					<div id="form">
						<?php echo $frm->getFormHtml();?>
					</div>		
				</div>
				</section>  
			 </div> 
		</div>
	</div>

        								
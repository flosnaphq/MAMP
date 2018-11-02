<?php defined('SYSTEM_INIT') or die('Invalid Usage'); ?>
<?php
	$frmAdd->setFormTagAttribute('id', 'frmCategory');
	$frmAdd->setFormTagAttribute('class', 'web_form');
	$frmAdd->setFormTagAttribute('onsubmit', 'submitCategory(this, categoryValidator); return(false);' );		
	$frmAdd->setFormTagAttribute("action",FatUtility::generateUrl("Blogcategories","submitCategory"));
	$frmAdd->setValidatorJsObjectName('categoryValidator');
	
	$category_title= $frmAdd->getField('category_title');
	$category_title->addFieldTagAttribute('id', 'category_title');
	$category_title->addFieldTagAttribute('onblur', 'setSeoName(this, category_seo_name)');		 
	$category_title->setRequiredStarWith('none');
	
	$category_seo_name= $frmAdd->getField('category_seo_name');
	$category_seo_name->addFieldTagAttribute('id', 'category_seo_name');	 
	$category_seo_name->setRequiredStarWith('none');
	
	$category_description= $frmAdd->getField('category_description');
	$category_description->addFieldTagAttribute('id', 'category_description');	 
	 
	$category_id= $frmAdd->getField('category_id');
	$category_id->addFieldTagAttribute('id', 'category_id');
	
	$bmeta_id= $frmAdd->getField('bmeta_id');
	$bmeta_id->addFieldTagAttribute('id', 'bmeta_id');
	
	$btn_submit= $frmAdd->getField('btn_submit');
	$btn_submit->addFieldTagAttribute('id', 'btn_submit');	 
	  
	$fld = $frmAdd->addHtml('', '', 'Note: Meta Others are HTML meta tags, e.g &lt;meta name="example" content="example" /&gt;. We are not validating these tags, please take care of this.');
	
	$bmeta_others= $frmAdd->getField('bmeta_others');
	$bmeta_others->attachField($fld);
	
	$cancelBtn= $frmAdd->getField('cancelBtn');	 
	$cancelBtn->addFieldTagAttribute('onclick', 'cancelCategory();');	 
	 
?>

<div class="fixed_container">
    <div class="row">
        <div class="col-sm-12"> 
            <h1>Manage Blog Category</h1>
            <section class="section">
                <div class="sectionhead"><h4>Blog Category Form </h4></div>
                <div class="sectionbody space">
                    <?php echo $frmAdd->getFormTag(); ?>   
					<div class="formhorizontal">
                        <div class="field_control horizontal">
                            <label class="field_label">Category Title<span class="spn_must_field">*</span></label>
                            <div class="field_cover"><?php echo $frmAdd->getFieldHtml('category_title'); ?></div>
                        </div>
						<div class="field_control horizontal">
                            <label class="field_label">Category SEO Name<span class="spn_must_field">*</span></label>
                            <div class="field_cover"><?php echo $frmAdd->getFieldHtml('category_seo_name'); ?></div>
                        </div> 
						<div class="field_control horizontal">
                            <label class="field_label">Category Description</label>
                            <div class="field_cover"><?php echo $frmAdd->getFieldHtml('category_description'); ?></div>
                        </div>
						<div class="field_control horizontal">
                            <label class="field_label">Category Status</label>
                            <div class="field_cover"><?php echo $frmAdd->getFieldHtml('category_status'); ?></div>
                        </div>
						<?php if(!isset($fieldParent)) {?>
						<div class="field_control horizontal">
                            <label class="field_label">Category Parent</label>
                            <div class="field_cover"><?php echo $frmAdd->getFieldHtml('category_parent'); ?></div>
                        </div>
						<?php } ?>
						<div class="field_control horizontal">
                            <label class="field_label">Meta Title</label>
                            <div class="field_cover"><?php echo $frmAdd->getFieldHtml('bmeta_title'); ?></div>
                        </div>
						<div class="field_control horizontal">
                            <label class="field_label">Meta Keywords</label>
                            <div class="field_cover"><?php echo $frmAdd->getFieldHtml('bmeta_keywords'); ?></div>
                        </div>
						<div class="field_control horizontal">
                            <label class="field_label">Meta Description</label>
                            <div class="field_cover"><?php echo $frmAdd->getFieldHtml('bmeta_description'); ?></div>
                        </div>
						<div class="field_control horizontal">
                            <label class="field_label">Meta Others</label>
                            <div class="field_cover"><?php echo $frmAdd->getFieldHtml('bmeta_others'); ?></div>
                        </div>
						<div class="field_control horizontal">
                            <label class="field_label"></label>
                            <div class="field_cover"><?php echo $frmAdd->getFieldHtml('btn_submit'); ?>
							<?php echo $frmAdd->getFieldHtml('cancelBtn'); ?></div>
                        </div>
					</div>					 
					<?php echo $frmAdd->getFieldHtml('category_id'); ?>
					<?php echo $frmAdd->getFieldHtml('bmeta_id'); ?>
					</form><?php echo $frmAdd->getExternalJS(); ?>
                </div>
            </section>
        </div>	
    </div>
</div>

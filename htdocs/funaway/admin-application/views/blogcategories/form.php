<?php 
	defined('SYSTEM_INIT') or die('Invalid Usage');
	if($canEdit === true){
?>

<div class="fixed_container">
    <div class="row">
		<div class="col-sm-12">  
            <h1><?php echo Info::t_lang('MANAGE_BLOG_CATEGORY'); ?></h1>
        </div>
        <div class="col-sm-12">            
            <section class="section">
                <div class="sectionhead"><h5><?php echo Info::t_lang('BLOG_CATEGORY_FORM') ?></h5></div>
                <div class="sectionbody space">
                    <?php
						$form->setFormTagAttribute( 'id', 'categoryForm' );
						$form->setFormTagAttribute( 'class', 'web_form' );
						$form->setFormTagAttribute( 'action', FatUtility::generateUrl( "Blogcategories", "setup", array(FatUtility::convertToType($categoryId,FatUtility::VAR_INT)) ) );
						
						$idField = $form->getField( BlogCategories::DB_TBL_PREFIX.'id' );
						$idField->addFieldTagAttribute('id','category_id');
						
						$metaIdField = $form->getField( BlogCategories::DB_CHILD_TBL_PREFIX.'id' );
						$metaIdField->addFieldTagAttribute('id','bmeta_id');
						
						$statusFld = $form->getField(BlogCategories::DB_TBL_PREFIX.'status');
						$statusFld->addFieldTagAttribute('id', BlogCategories::DB_TBL_PREFIX.'status');
						
						$category_title= $form->getField('category_title');
						$category_title->addFieldTagAttribute('id', 'category_title');
						$category_title->addFieldTagAttribute('autofocus', 'on');
						$category_title->addFieldTagAttribute('onblur', 'setSeoName(this, category_seo_name)');
						
						$category_seo_name= $form->getField('category_seo_name');
						$category_seo_name->addFieldTagAttribute('id', 'category_seo_name');	

						$submitFld = $form->getField('btn_submit');
						$submitFld->addFieldTagAttribute('id', 'btn_submit');								
						$closeFld = $form->getField( 'btn_cancel' );
						$closeFld->addFieldTagAttribute( 'id', 'btn_cancel' );
						$closeFld->addFieldTagAttribute( 'onclick', 'window.location="'.FatUtility::generateUrl('Blogcategories').'"' );
						$form->setValidatorJsObjectName ( 'BlogCategoriesValidator' );
						
						echo $form->getFormTag();
						echo $form->getFieldHtml(BlogCategories::DB_TBL_PREFIX.'id');
						echo $form->getFieldHtml(BlogCategories::DB_CHILD_TBL_PREFIX.'id');
						?>
						<div class="row col-sm-12">							 
							<div class="field-set">
								<label><?php echo Info::t_lang('CATEGORY_TITLE').' ' ?></label>
									<span class="mandatory">*</span>
								<?php echo $form->getFieldHtml(BlogCategories::DB_TBL_PREFIX.'title'); ?>
							</div>							 
						</div>
						<div class="row col-sm-12">							 
							<div class="field-set">
								<label><?php echo Info::t_lang('CATEGORY_SEO_NAME').' ' ?></label>
									<span class="mandatory">*</span>
								<?php echo $form->getFieldHtml(BlogCategories::DB_TBL_PREFIX.'seo_name'); ?>
							</div>							 
						</div>
						<div class="row col-sm-12">							 
							<div class="field-set">
								<label><?php echo Info::t_lang('CATEGORY_DESCRIPTION').' ' ?></label>
									
								<?php echo $form->getFieldHtml(BlogCategories::DB_TBL_PREFIX.'description'); ?>
							</div>							 
						</div>
						<div class="row col-sm-5">							 
							<div class="field-set">
								<label><?php echo Info::t_lang('CATEGORY_STATUS').' ' ?></label>
									<span class="mandatory">*</span>
								<?php echo $form->getFieldHtml(BlogCategories::DB_TBL_PREFIX.'status'); ?>
							</div>
						</div>
						<div class='col-md-1'>
						</div>
						<?php if(empty(FatUtility::convertToType($categoryId,FatUtility::VAR_INT))){ ?>
						<div class="row col-sm-5">							 
							<div class="field-set">
								<label><?php echo Info::t_lang('CATEGORY_PARENT').' ' ?></label>
									
								<?php echo $form->getFieldHtml(BlogCategories::DB_TBL_PREFIX.'parent'); ?>
							</div>							 
						</div>
						<?php } ?>
						<div class="row col-sm-12">							 
							<div class="field-set">
								<label><?php echo Info::t_lang('META_TITLE').' ' ?></label>
									
								<?php echo $form->getFieldHtml(BlogCategories::DB_CHILD_TBL_PREFIX.'title'); ?>
							</div>							 
						</div>
						<div class="row col-sm-12">							 
							<div class="field-set">
								<label><?php echo Info::t_lang('META_KEYWORDS').' ' ?></label>
									
								<?php echo $form->getFieldHtml(BlogCategories::DB_CHILD_TBL_PREFIX.'keywords'); ?>
							</div>							 
						</div>
						<div class="row col-sm-12">							 
							<div class="field-set">
								<label><?php echo Info::t_lang('META_DESCRIPTION').' ' ?></label>
								<?php echo $form->getFieldHtml(BlogCategories::DB_CHILD_TBL_PREFIX.'description'); ?>
							</div>							 
						</div>
						<div class="row col-sm-12">							 
							<div class="field-set">
								<label><?php echo Info::t_lang('META_OTHERS').' ' ?></label>
									
								<?php echo $form->getFieldHtml(BlogCategories::DB_CHILD_TBL_PREFIX.'others'); ?>
							</div>
							<div class='col-xs-12 field-set'>
								<?php echo $form->getFieldHtml('note'); ?>
							</div>
						</div>						
						
						<br>
						<div class="row">
							<div class="col-md-12">
								<div class="field-set">
									<?php echo $form->getFieldHtml('btn_submit'); ?>
									<?php echo $form->getFieldHtml('btn_cancel'); ?>
								</div>
							</div>
						</div>
					</form>
					<?php echo $form->getExternalJS(); ?>
                </div>
            </section>
        </div>	
    </div>
    </div>

<?php }
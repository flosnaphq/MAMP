<?php 
	defined('SYSTEM_INIT') or die('Invalid Usage');
	include_once('functions.php');
	if($canEdit === true){
		if(!empty($tree['0'])){
			$treeView=$tree['0'];
			$checkBoxesList= '<ul class="verticalcheck_list">';
			foreach($treeView as $node)
			{
				$checked='';		
				if(!empty($relatedCategories))
				{
					foreach($relatedCategories as $category)
					{
						if($node[BlogCategories::DB_TBL_PREFIX.'id']==$category)
						{
							$checked='checked="checked"';
						}
					}
				}
				printNode($node,$checkBoxesList,$checked,$relatedCategories); // function defined in file functions.php
			}
			$checkBoxesList.= '</ul>';
		}
?>

<div class="fixed_container">
    <div class="row">
		<div class="col-sm-12">  
            <h1><?php echo Info::t_lang('MANAGE_BLOG_POST'); ?></h1>
        </div>
        <div class="col-sm-12">            
            <section class="section">
                <div class="sectionhead"><h5><?php echo Info::t_lang('BLOG_POST_FORM') ?></h5></div>
                <div class="sectionbody space">
					<?php
					$form->setFormTagAttribute( 'id', 'blogPostForm' );
					$form->setFormTagAttribute( 'class', 'web_form' );
					// $form->setFormTagAttribute( 'onsubmit', 'return submitPost(this , objValidator);' );
					$form->setFormTagAttribute( 'action', FatUtility::generateUrl( "BlogPosts", "setup", array(FatUtility::convertToType($postId,FatUtility::VAR_INT)) ) );
					$idField = $form->getField( BlogPosts::DB_TBL_PREFIX.'id' );
					$idField->addFieldTagAttribute('id','post_id');
					$metaIdField = $form->getField( BlogPosts::DB_CHILD_TBL_PREFIX.'id' );
					$metaIdField->addFieldTagAttribute('id','bmeta_id');
					$statusFld = $form->getField(BlogPosts::DB_TBL_PREFIX.'status');
					$statusFld->addFieldTagAttribute('id', BlogPosts::DB_TBL_PREFIX.'status');
					$seoFld= $form->getField('post_seo_name');
					$seoFld->addFieldTagAttribute('id', 'post_seo_name');
					$seoFld->setUnique(BlogPost::DB_TBL,BlogPost::DB_TBL_PREFIX.'seo_name',BlogPost::DB_TBL_PREFIX.'id',BlogPost::DB_TBL_PREFIX.'id',BlogPost::DB_TBL_PREFIX.'id');
					$titleFld= $form->getField('post_title');
					$titleFld->addFieldTagAttribute('id', 'post_title');
					$titleFld->addFieldTagAttribute('onblur', 'setSeoName(this, post_seo_name)');
					
					$removedImagesFld = $form->getField('post_removed_images');
					$removedImagesFld->addFieldTagAttribute('id','post_removed_images');
					$fileUploadField= $form->getField('post_image_file_name');
					$fileUploadField->addFieldTagAttribute('accept','image/*');
					$submitFld = $form->getField('btn_submit');
					$submitFld->addFieldTagAttribute('id', 'btn_submit');								
					$closeFld = $form->getField( 'btn_cancel' );
					$closeFld->addFieldTagAttribute( 'id', 'btn_cancel' );
					$closeFld->addFieldTagAttribute( 'onclick', 'window.location="'.FatUtility::generateUrl('BlogPosts').'"' );
					$form->setValidatorJsObjectName ( 'objValidator' );
					$form->getField(BlogPosts::DB_TBL_PREFIX.'content')->setFieldTagAttribute('id','post_content');
					echo $form->getFormTag();
					echo $form->getFieldHtml(BlogPosts::DB_TBL_PREFIX.'id');
					echo $form->getFieldHtml(BlogPosts::DB_CHILD_TBL_PREFIX.'id');
					echo $form->getFieldHtml('post_removed_images');
					?>
					<div class="row col-sm-12">							 
						<div class="field-set">
							<label><?php echo Info::t_lang('POST_TITLE').' ' ?></label>
								<span class="mandatory">*</span>
							<?php echo $form->getFieldHtml(BlogPosts::DB_TBL_PREFIX.'title'); ?>
						</div>							 
					</div>
					<div class="row col-sm-12">							 
						<div class="field-set">
							<label><?php echo Info::t_lang('POST_CONTRIBUTOR_NAME').' ' ?></label>
							<?php echo $form->getFieldHtml(BlogPosts::DB_TBL_PREFIX.'contributor_name'); ?>
						</div>							 
					</div>
					<div class="row col-sm-12">							 
						<div class="field-set">
							<label><?php echo Info::t_lang('POST_SEO_NAME').' ' ?></label>
								<span class="mandatory">*</span>
							<?php echo $form->getFieldHtml(BlogPosts::DB_TBL_PREFIX.'seo_name'); ?>
						</div>							 
					</div>
					<div class="row col-sm-12">							 
						<div class="field-set">
							<label><?php echo Info::t_lang('POST_SHORT_DESCRIPTION').' ' ?></label>
							<?php echo $form->getFieldHtml(BlogPosts::DB_TBL_PREFIX.'short_description'); ?>
						</div>							 
					</div>
					<div class="row col-sm-12">							 
						<div class="field-set">
							<label><?php echo Info::t_lang('POST_CONTENT').' ' ?></label>
							<span class="mandatory">*</span>
							<?php echo $form->getFieldHtml(BlogPosts::DB_TBL_PREFIX.'content'); ?>
						</div>							 
					</div>
					<div class="row col-sm-12">							 
						<div class="field-set">
							<div class="boxwraplist">
								<span class="boxlabel"><?php echo Info::t_lang('POST_CATEGORY').' ' ?></span>
								<div class="scrollerwrap">
								<?php echo !empty($checkBoxesList) ? $checkBoxesList:''; ?>
								</div>
							</div>
						</div>
					</div>
					<div class="row col-sm-12">
						<div class="field-set">
							<label><?php echo Info::t_lang('POST_IMAGE').' ' ?></label>
							<?php 
							include CONF_THEME_PATH . 'upload_images.php'; 
							if(isset($postImages)) {
								$photo_html = '';
								if (isset($postImages['imgs']) && is_array($postImages['imgs']) && sizeof($postImages['imgs']) > 0) {
									$photo_html .= '<div class="photosrow" id="post_imgs">';
									// die( $postId);
									$photo_html .= getImagesHtml($postImages, FatUtility::convertToType($postId,FatUtility::VAR_INT) , 'post');
									$photo_html .= '</div>';
								}
								echo html_entity_decode($photo_html); 
							}
							?> 
						</div>
					</div>
					<div class="row col-sm-12">							 
						<div class="field-set">
							<label><?php echo Info::t_lang('POST_COMMENT_STATUS').' ' ?></label>
							<?php echo $form->getFieldHtml(BlogPosts::DB_TBL_PREFIX.'comment_status'); ?>
						</div>							 
					</div>
					<div class="row col-sm-12">							 
						<div class="field-set">
							<label><?php echo Info::t_lang('POST_META_TITLE').' ' ?></label>
							<?php echo $form->getFieldHtml(BlogPosts::DB_CHILD_TBL_PREFIX.'title'); ?>
						</div>							 
					</div>
					<div class="row col-sm-12">							 
						<div class="field-set">
							<label><?php echo Info::t_lang('META_KEYWORDS').' ' ?></label>
							<?php echo $form->getFieldHtml(BlogPosts::DB_CHILD_TBL_PREFIX.'keywords'); ?>
						</div>							 
					</div>
					<div class="row col-sm-12">							 
						<div class="field-set">
							<label><?php echo Info::t_lang('META_DESCRIPTION').' ' ?></label>
							<?php echo $form->getFieldHtml(BlogPosts::DB_CHILD_TBL_PREFIX.'description'); ?>
						</div>							 
					</div>
					<div class="row col-sm-12">							 
						<div class="field-set">
							<label><?php echo Info::t_lang('META_OTHERS').' ' ?></label>
							<?php echo $form->getFieldHtml(BlogPosts::DB_CHILD_TBL_PREFIX.'others'); ?>
						</div>
						<div class='col-xs-12 field-set'>
							<b class="b_size_class"> <?php echo $form->getFieldHtml('note'); ?> </b>
						</div>
					</div>
					<div class="row col-sm-12">							 
						<div class="field-set">
							<label><?php echo Info::t_lang('POST_STATUS').' ' ?></label>
								<span class="mandatory">*</span>
							<?php echo $form->getFieldHtml(BlogPosts::DB_TBL_PREFIX.'status'); ?>
						</div>
					</div>
					<div class="row col-md-12">
						<div class="field-set">
							<?php
							echo $form->getFieldHtml('btn_submit');
							echo $form->getFieldHtml('btn_cancel'); 
							?>
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
?>
<script>
    function validateCheckbox() {
        var chbox_length = $('input[name$="relation_category_id[]"]:checked').length;
          
        if (chbox_length == 0) {
             $.extend(objValidator,{'relation_category_id': {"selectionrange": [1,2]}});
        } else {
         $.extend(objValidator,{'relation_category_id': {"required": false}});
        }
		// console.log(objValidator);
        // $("#blogPostForm").unbind("submit");
        // frmPost_validator = $("#blogPostForm").validation(objValidator);
    }

    $(document).ready(function () {
        validateCheckbox();
    });
</script>
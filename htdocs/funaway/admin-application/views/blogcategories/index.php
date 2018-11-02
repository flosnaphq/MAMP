<?php defined('SYSTEM_INIT') or die('Invalid Usage'); ?>
<?php

	$frmCategory->setFormTagAttribute('id', 'frmSearch');
	$frmCategory->setFormTagAttribute('class', 'web_form');
	$frmCategory->setFormTagAttribute('onsubmit', 'filteredBlogCatogries(this); return(false);' );		 
	$frmCategory->setFormTagAttribute("action",FatUtility::generateUrl("Blogcategories","filteredBlogCatogries"));  
         
	$category_title= $frmCategory->getField('category_title');
	$category_title->addFieldTagAttribute('id', 'category_title');
	
	$category_status= $frmCategory->getField('category_status');
	$category_status->addFieldTagAttribute('id', 'category_status');
	 
	$btn_submit= $frmCategory->getField('btn_submit');
	$btn_submit->addFieldTagAttribute('id', 'btn_submit');	 
	  
	$cancelBtn= $frmCategory->getField('cancel_search');	 
	$cancelBtn->addFieldTagAttribute('class', 'cancel_form');	
?>
<style>
// .statustab.addmarg {
	// margin-top:0;
// }
</style>

<div class="fixed_container" >
    <div class="row">
		<div class="col-sm-12">  
            <h1><?php echo Info::t_lang('BLOG_CATEGORIES'); ?></h1>  
            <section class="section searchform_filter">
                <div class="sectionhead">
                    <h4><?php echo Info::t_lang('SEARCH_BLOG_CATEGORIES') ?></h4>
                </div>
                <div style="display:none;" class="sectionbody  togglewrap">
                    <?php echo $frmCategory->getFormTag(); ?>
						<table class="table_form_vertical">
							<tbody>
								<tr>
									<td width="20%"><?php echo Info::t_lang('CATEGORY_TITLE') ?><br><?php echo $frmCategory->getFieldHtml('category_title'); ?></td>
									<td width="20%"><?php echo Info::t_lang('CATEGORY_STATUS') ?><br><?php echo $frmCategory->getFieldHtml('category_status'); ?></td>
									<td width="20%"><br>
									<?php 
										echo $frmCategory->getFieldHtml('btn_submit'); 
										echo $frmCategory->getFieldHtml('cancel_search');
									?>
									</td>
								</tr>
							</tbody>
						</table>
					</form>
					<?php echo $frmCategory->getExternalJs(); ?>
                </div>
            </section>
        </div>
		<div class="col-sm-12">  
			<section class="section">
				<div class="sectionhead">
					<h4><?php echo Info::t_lang('BLOG_CATEGORIES_LIST') ?></h4>
					<?php if($canEdit === true) { ?>
					   <ul class="actions right">
							<li class="droplink">
								<a href="javascript:void(0)"><i class="ion-android-more-vertical icon"></i></a>
								<div class="dropwrap">
									<ul class="linksvertical">
										<?php
										$params = array();
											if($catId!=0){
												$params = array(0, $catId);
											}
										if($canEdit){
										?>
										<li><a href="<?php echo FatUtility::generateUrl('blogcategories', 'form', $params); ?>"><?php echo Info::t_lang('ADD_NEW') ?></a></li>
										<?php } ?>
									</ul>
								</div>
							</li>
						</ul>
					<?php } ?>				
				</div>
				<div class="sectionbody" id="listing-div"></div>
			</section>
		</div>
    </div>
</div>

<script>
	var catId=" <?php echo $catId ?>";
	$(document).ready(function () {       
        $('.table').tableDnD({
            onDrop: function (table, row) {
                var order = $.tableDnD.serialize('id');
                order+='&catId='+catId;
                /*$('#msgbox').load("cms-ajax.php?" + order+"&mode=REORDER_NAVIGATION");
                 $.mbsmessage('Reordering Update!',true);*/
                // $.mbsmessage('Updating display order....');
				fcom.ajax(fcom.makeUrl('blogcategories', 'setCatDisplayOrder'), order, function (t) {
                });
            }
        });
    });
</script>
<?php defined('SYSTEM_INIT') or die('Invalid Usage'); ?>  

<div class="fixed_container" >
    <div class="row">
		<div class="row">
			<div class="col-sm-12">  
				 <h1><?php echo Info::t_lang('BLOG_POSTS'); ?></h1>
				<section class="section searchform_filter">
					<div class="sectionhead">
						<h4><?php echo Info::t_lang('SEARCH_BLOG_POST'); ?></h4>
					</div>
					<div style="display:none;" class="sectionbody  togglewrap">
						<?php 
							$frmSearch->setFormTagAttribute( 'id', 'frmSearch' );
							$frmSearch->setFormTagAttribute( 'class', 'web_form' );
							$frmSearch->setFormTagAttribute( 'onsubmit', 'return searchPosts(this);' );
							$frmSearch->setFormTagAttribute( 'action', FatUtility::generateUrl("BlogPosts", "listing") );
							
							$titleFld = $frmSearch->getField('post_title');
							$titleFld->addFieldTagAttribute('id', 'keyword');
									
							$statusFld = $frmSearch->getField('post_status');
							$statusFld->addFieldTagAttribute('id', 'status');
									
							$submitFld = $frmSearch->getField('btn_submit');
							$submitFld->addFieldTagAttribute('id', 'btn_submit');
$submitFld->addFieldTagAttribute('onclick', 'document.frmSearch.page.value = 1;'); // reset pagination on click of search button
									
							$cancelFld = $frmSearch->getField('cancel_search');
							$cancelFld->addFieldTagAttribute('class', 'cancel_form');
									
							echo $frmSearch->getFormTag();
						?>
							<table class="table_form_vertical">
								<tbody>
									<tr>
										<td width="20%"><?php echo Info::t_lang('POST_TITLE'); ?><br><?php echo $frmSearch->getFieldHtml('post_title'); ?></td>
										<td width="20%"><?php echo Info::t_lang('POST_STATUS'); ?><br><?php echo $frmSearch->getFieldHtml('post_status'); ?></td>
										<td width="20%"><br>
										<?php 
											echo $frmSearch->getFieldHtml('btn_submit'); 
											echo $frmSearch->getFieldHtml('cancel_search'); 
											echo $frmSearch->getFieldHtml('page'); 
										?>
										</td>
									</tr>
								</tbody>
							</table>
						</form>
						<?php echo $frmSearch->getExternalJs(); ?>
					</div>
				</section>
			</div>
			
        <div class="col-sm-12"> 
            <section class="section">
                <div class="sectionhead">
                    <h4><?php echo Info::t_lang('BLOG_POSTS_LIST') ?></h4>
                    <?php if ($canEdit === true) { ?> 
							<ul class="actions right">
								<li class="droplink">
									<a href="javascript:void(0)"><span class="ink animate" style="height: 30px; width: 30px; top: -4.28334px; left: 7px;"></span><i class="ion-android-more-vertical icon"></i></a>
									<div class="dropwrap">
										<ul class="linksvertical">
										<?php if($canEdit==true){ ?>
											<li>
											<a href="<?php echo FatUtility::generateUrl('BlogPosts', 'form'); ?>" ><?php echo Info::t_lang('ADD_NEW_BLOG_POST') ?></a>
											</li>
										<?php } ?>
										</ul>
									</div>
								</li>
							</ul>
					<?php } ?>	
                </div>
                <div class="sectionbody" id="posts_list">
				<?php //include('listing.php'); ?>
				</div>
            </section>
        </div>
	</div>
</div>
</div>

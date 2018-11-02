<div class='addons-section'>
<?php if(!empty($addons)){?>
<div class="scrollable--x">
<table class="table table--bordered table--responsive info-table">
	<thead>
		<tr>
			<th>Add-on</th>
			<th>Price</th>
			<th>Description</th>
			<th>Action</th>
		</tr>	
	</thead>
    <tbody>
<?php foreach($addons as $add){ 
?> 
	   <tr class="info">
			<th data-label="Addon" class="info__details info__wrap"><h6 class="info__heading"><?php echo $add['activityaddon_text']?></h6></th>
			<td data-label="Price"><?php echo $add['activityaddon_price']?></td>
			<td data-label="Comment" class="info__wrap">
				<div class="more-less">
					<div class="more-block">
						<?php echo $add['activityaddon_comments']?>
					</div>
				</div>
			</td>
			<td data-label="Delete">
			<a href="javascript:;" title="Image" ng-click="addonImages(<?php echo $add['activityaddon_id']?>)" class="button button--square button--small button--fill button--green "><svg class="icon icon--search"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-image"></use></svg></a>
			<a href="javascript:;" title="Delete" ng-click="removeAddon(<?php echo $add['activityaddon_id']?>)" class="button button--square button--small button--fill button--red thumb__delete"><svg class="icon icon--search"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-delete"></use></svg></a>
			<a href="javascript:;" title="Edit" ng-click="editAddon(<?php echo $add['activityaddon_id']?>)" class="button button--square button--small button--fill button--blue"><svg class="icon icon--search"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-edit"></use></svg></a>
			</td>
		</tr>
<?php } ?>
</tbody></table></div>
<?php }else{?>
	<?php echo Info::t_lang('NO_ADD-ONS_YET');?>
<?php } ?>
</div>	
	
<hr>	


<?php 
$frm->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
$frm->setFormTagAttribute('class', 'form form--default form--horizontal');
$frm->setFormTagAttribute('id', 'frmAddons');
$frm->setFormTagAttribute('action', FatUtility::generateUrl("manage-activity",'saveAddon'));
$frm->setValidatorJsObjectName('setup7Validator');
$frm->setFormTagAttribute('ng-submit', 'saveAddon($event);');
$frm->developerTags['fld_default_col'] =12;
echo $frm->getFormHtml();
?>
<?php
function printNode($node,&$str,$checked,$relatedCategories) {

	$str.= '<li>
				<label class="checkbox leftlabel">
					<input type="checkbox" title="parent category" onchange="validateCheckbox();" name="relation_category_id[]" value=' . $node[BlogCategories::DB_TBL_PREFIX.'id'] . ' ' . $checked . '> 
					<i class="input-helper"></i>' . $node[BlogCategories::DB_TBL_PREFIX.'title'] . 
				'</label>
			</li>';
			
	if(isset($node['child']))
	{
		$str.= '<ul class="sub-categories">';
		foreach($node['child'] as $child)
		{
			$checked='';
			
			if(!empty($relatedCategories))
				{
					foreach($relatedCategories as $category)
					{
						if($child[BlogCategories::DB_TBL_PREFIX.'id']==$category)
						{
							$checked='checked="checked"';
						}
					}
				}
			printNode($child,$str,$checked,$relatedCategories);
		}
		$str.= '</ul>';
	}
}

function getImagesHtml($product_images, $product_id, $image_folder) {
	$photo_html = '';
	if (isset($product_images['imgs']) && is_array($product_images['imgs']) && sizeof($product_images['imgs']) > 0) {
		foreach ($product_images['imgs'] as $id => $img) {
			
			$id = FatUtility::convertToType($id,FatUtility::VAR_INT);
			$product_id= FatUtility::convertToType($product_id,FatUtility::VAR_INT);
			$photo_html .= '<div class="photosquare"><img alt="" src="' . FatUtility::generateUrl('Image', $image_folder, array( $img,BlogConstants::IMG_THUMB_WIDTH,BlogConstants::IMG_THUMB_HEIGHT),CONF_BASE_DIR) . '"> <a class="crossLink" href="javascript:void(0)" onclick="return removeImage(this, ' . $id . ');"></a>';
			 if (!(isset($product_images['main_img']) && $product_images['main_img'] == $id)) {
			$photo_html .= '<a class="linkset button small black" href="javascript:void(0)" onclick="setMainImage(this, ' . $id . ', ' . $product_id . ');">Set Main Image</a>';
			 }
			$photo_html .= '</div>';
		}
	}
	return $photo_html;
}
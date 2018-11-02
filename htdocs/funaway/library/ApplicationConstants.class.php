<?php

class ApplicationConstants
{
	const TYPE_ACTIVITY = 1;
	
	const USER_TRAVELER_TYPE = 0;
	const USER_HOST_TYPE = 1;
	
	const CONF_BOOL_YES = 1;
	const CONF_BOOL_NO = 0;
	
	public static function validImageMimeTypes(){
		return array("image/jpg", "image/jpeg", "image/png", "image/gif", 'image/svg+xml');
	}
	
	public static function fatShortCodes()
	{
		/*
		[fat_partnershipform] To get Partnership Form
		[fat_contactinfo] To get COntact info
		[fat_offices] To get Office addresses
		[fat_contactform] TO get COntact Form
		[block blkid='22'] To get a block HTML
		[fat_founders fatclass='section--top-border'] To get all added Founders
		[fat_investors fatclass='section--top-border section--light investor__section'] To get all added Investores
		[fat_testimonials limit='2' fatclass="test testimonial__section section--top-border" id="asSeenOn"] To get all added Testimonials
		*/
		return array(
	
					'[fat_contactinfo]' => 'To get Contact info. Information saved in configuration settings',
					'[fat_offices]' => 'To get Office addresses. Information saved in Offices section',
					'[fat_contactform]' => 'To get Contact Form',
					'[block blkid=BLOCKID]' => 'To get a block HTML. (You can also get a full shortcode from Block Section)',
					
					'[fat_testimonials limit="2" fatclass="test testimonial__section section--top-border" id="asSeenOn"]' => 'To display Testimonials. Set your limit accordingly by defining "limit attribute"',
					
				);
	}
	
	public static function getYesNoArray()
	{
		return array(static::CONF_BOOL_YES => Info::t_lang('BOOL_LBL_YES'), static::CONF_BOOL_NO => Info::t_lang('BOOL_LBL_NO'));
	}
}

?>

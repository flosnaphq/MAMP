<?php 
class BlogConstants{
	/*
		BLOG META RECORD TYPE CONSTANTS
	*/
	const BMETA_RECORD_TYPE_POST =0;
	const BMETA_RECORD_TYPE_CATEGORY =1;
	/*
		BLOG POST STATUS CONSTANTS
	*/
	const ENTITY_ACTIVE =1;
	const ENTITY_INACTIVE =0;
	const POST_DRAFTED =0;
	const POST_PUBLISHED =1;
	
	//Blog Contribution status for email template
	
	const BLOG_CONTRIBUTION_APPROVED = 'approved';
	const BLOG_CONTRIBUTION_PUBLISHED = 'published';
	
	/*
		IMAGE DIMENTIONS CONSTANTS
	*/
	const IMG_THUMB_WIDTH = 100;
	const IMG_THUMB_HEIGHT = 50;
	
	public static function blogPostStatus() { 
		return array(
		self :: POST_DRAFTED => 'Draft' ,
		self :: POST_PUBLISHED => 'Published'
		);
	}
	
	public static function blogPostStatusByKey($key) { 
		$ar = self::blogPostStatus();
		return $ar[$key];
	}
	
	public static function contriStatus() { 
		return array(
		'0' => 'Pending', 
		'1' => 'Approved', 
		'2' => 'Posted', 
		'3' => 'Rejected'
		);
	}
	
	public static function commentStatus() { 
		return  array('0' => 'Pending', '1' => 'Approved', '2' => 'Declined');
	}
	
	public static function truncateCharacters($string, $limit, $break=" ", $pad="..."){
		if(strlen($string) <= $limit) return $string;
		
		$string = substr($string, 0, $limit);
		if(false !== ($breakpoint = strrpos($string, $break))) 
		{
			$string = substr($string, 0, $breakpoint);
		}
		return $string . $pad;
	}
	
	public static function postCommentStatus() { 
		return array(
		'0' => 'Not Open' ,
		'1' => 'Open'
		);
	}
	
	
}
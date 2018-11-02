<?php
class Breadcrumb{
	private $breadcrumbs = array();
	private $separator_start = '<li class="text-heading--label">';
	private $separator_end = '</li>';
	private $start = '<ol aria-labelledby="bread-crumb-label" class="breadcrumb list list--horizontal">';
	private $end = '</ol>';

	public function __construct($admin = false){
		//if($admin){
			$this->breadcrumbs[] = array('title' => 'Home', 'href' => FatUtility::generateUrl());
		//}	
	}
	
	function add($title, $href ="javascript:;", $class =""){		
		if (!$title) return;
		$this->breadcrumbs[] = array('title' => $title, 'href' => $href,'class'=>$class);
	}
	
	function output(){
		if ($this->breadcrumbs) {
			$output = $this->start;
			foreach ($this->breadcrumbs as $key => $crumb) {
					$output .= $this->separator_start;
					$size = sizeof($this->breadcrumbs)-1;
					/* if ($size == $key) {
						$output .=  $crumb['title'] ;			
					} else { */
						$output .= '<a href="' . $crumb['href'] . '">' . $crumb['title'] . '</a>';
				//	}
					$output .= $this->separator_end;
			}
			return $output . $this->end . PHP_EOL;
		}
	}
}
?>
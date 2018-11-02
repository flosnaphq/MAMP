<?php


class SitemapController extends MyAppController{
	private $xml_folder_name = 'sitemap_xml';
	private $site_url = '';
	protected $allUrls = array();
	function __construct(){
		$this->site_url = substr(FatUtility::generateFullUrl(), 0, -1);
	}
	function index(){
	
		$obj = new Sitemap();
		$data = array();
		$data['sitemap_pages'] = $obj->getNavigation();
		$data['cms_pages'] = $obj->getCmsLinks();
		$data['services_pages'] = $obj->getService();
		$data['country_pages'] = $obj->getCountries();
		$data['city_pages'] = City::getCities();
		$data['activities_pages'] = $obj->getActivities();
		/* $blog = $obj->getBlogs();
		$data['sitemap_blog_posts'] = $blog['recentPost'];
		$data['sitemap_blog_categories'] = $blog['categories']; */
		$this->generateSitemap($data);
	}
    private function generateSitemap($site_map_array=array()){
		if(empty($site_map_array)) return;
		$xml_start = '<?xml version="1.0" encoding="UTF-8"?>
		
		<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\r\n";

		$xml_end   = '</urlset>';
					
		foreach($site_map_array AS $key=>$site_map){
			if($key=='sitemap_pages'){
				$file__path = CONF_INSTALLATION_PATH .$this->xml_folder_name."/sitemap_pages.xml";
				$handle = fopen($file__path,"w");
				$xml =$xml_start;
				foreach($site_map AS $k=>$v){
					if($v['navigation_link']=='') continue;
					$xml.='<url>'."\r\n";
					$this->allUrls[] = $this->site_url.$v['navigation_link'];
					$xml.='<loc>'.$this->site_url.$v['navigation_link']."</loc>\r\n";
					$xml.='</url>'."\r\n";	
				}
					
				$xml.=$xml_end;
				fwrite($handle,$xml);
				fclose($handle);
				$this->gzCompressFile($file__path);
			}
			if($key=='cms_pages'){
				$file__path = CONF_INSTALLATION_PATH .$this->xml_folder_name."/cms_pages.xml";
				$handle = fopen($file__path,"w");
				$xml =$xml_start;
				foreach($site_map AS $k=>$v){
					if($v['cms_slug'] == '' || in_array($this->site_url.CONF_BASE_DIR.$v['cms_slug'], $this->allUrls)) continue;
					$xml.='<url>'."\r\n";
					$xml.='<loc>'.$this->site_url.CONF_BASE_DIR.$v['cms_slug']."</loc>\r\n";
					$xml.='</url>'."\r\n";	
				}
				$xml.=$xml_end;
				fwrite($handle,$xml);
				fclose($handle);
				$this->gzCompressFile($file__path);
			}
			if($key=='services_pages'){
				$file__path = CONF_INSTALLATION_PATH . $this->xml_folder_name."/services_pages.xml";
				$handle = fopen($file__path,"w");
				$xml =$xml_start;
				foreach($site_map AS $k=>$v){
					if($v=='') continue;
					$url = Route::getRoute('services', 'index', array($v['service_id']));
					$xml.='<url>'."\r\n";
					$xml.='<loc>'.$this->site_url.$url."</loc>\r\n";
					$xml.='</url>'."\r\n";
				}
				$xml.=$xml_end;
				fwrite($handle,$xml);
				fclose($handle);
				$this->gzCompressFile($file__path);
			}
			if($key=='country_pages'){
				$file__path = CONF_INSTALLATION_PATH . $this->xml_folder_name."/country_pages.xml";
				$handle = fopen($file__path,"w");
				$xml =$xml_start;
				foreach($site_map AS $k=>$v){
					$url = Route::getRoute('country','details',array($k));
					$xml.='<url>'."\r\n";
					$xml.='<loc>'.$this->site_url.$url."</loc>\r\n";
					$xml.='</url>'."\r\n";
				}
				$xml.=$xml_end;
				fwrite($handle,$xml);
				fclose($handle);
				$this->gzCompressFile($file__path);
			}
						if($key=='city_pages'){
				$file__path = CONF_INSTALLATION_PATH . $this->xml_folder_name."/city_pages.xml";
				$handle = fopen($file__path,"w");
				$xml =$xml_start;
				foreach($site_map AS $k=>$v){
					$url = Route::getRoute('city','details',array($k));
					$xml.='<url>'."\r\n";
					$xml.='<loc>'.$this->site_url.$url."</loc>\r\n";
					$xml.='</url>'."\r\n";
				}
				$xml.=$xml_end;
				fwrite($handle,$xml);
				fclose($handle);
				$this->gzCompressFile($file__path);
			}
			
			
			
			if($key=='activities_pages'){
				$file__path = CONF_INSTALLATION_PATH . $this->xml_folder_name."/activities_pages.xml";
				$handle = fopen($file__path,"w");
				$xml =$xml_start;
				foreach($site_map AS $k=>$v){
					if($v['activity_id']=='') continue;
					$url = Route::getRoute('activity', 'detail', array($v['activity_id']));
					$xml.='<url>'."\r\n";
					$xml.='<loc>'.$this->site_url.$url."</loc>\r\n";
					$xml.='</url>'."\r\n";
				}
				$xml.=$xml_end;
				fwrite($handle,$xml);
				fclose($handle);
				$this->gzCompressFile($file__path);
			}
			if($key=='sitemap_blog_categories'){
				$file__path = CONF_INSTALLATION_PATH . $this->xml_folder_name."/sitemap_blog_categories.xml";
				$handle = fopen($file__path,"w");
				$xml =$xml_start;
				foreach($site_map AS $k=>$v){
					if($v['category_seo_name']=='') continue;
					$url = FatUtility::generateUrl('blog', 'category', array($v['category_seo_name']));
					$xml.='<url>'."\r\n";
					$xml.='<loc>'.$this->site_url.$url."</loc>\r\n";
					$xml.='</url>'."\r\n";
				}
				$xml.=$xml_end;
				fwrite($handle,$xml);
				fclose($handle);
				$this->gzCompressFile($file__path);
			}
			if($key=='sitemap_blog_posts'){
				$file__path = CONF_INSTALLATION_PATH . $this->xml_folder_name."/sitemap_blog_posts.xml";
				$handle = fopen($file__path,"w");
				$xml =$xml_start;
				foreach($site_map AS $k=>$v){
					if($v['post_seo_name']=='') continue;
					$url = FatUtility::generateUrl('blog', 'post', array($v['post_seo_name']));
					$xml.='<url>'."\r\n";
					$xml.='<loc>'.$this->site_url.$url."</loc>\r\n";
					$xml.='</url>'."\r\n";
				}
				$xml.=$xml_end;
				fwrite($handle,$xml);
				fclose($handle);
				$this->gzCompressFile($file__path);
			}
			
		}


		#ADD UPDATED SITEMAPS TO A XML FILE.
		$sitemap_xml_start = '<?xml version="1.0" encoding="UTF-8"?>
		<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\r\n";
		$sitemap_xml_end   = '</sitemapindex>';

		$path    = CONF_INSTALLATION_PATH . $this->xml_folder_name;
		$files = scandir($path);
		$files = array_diff(scandir($path), array('.', '..'));
		$xml=$sitemap_xml_start;
		foreach($files AS $file){
			if (file_exists($path.'/'.$file)) {
				$xml.='<sitemap>'."\r\n";
				$xml.='<loc>'.$this->site_url.'/'.$this->xml_folder_name.'/'.$file."</loc>\r\n";
				
				$date = date ("Y-m-d", filemtime($path.'/'.$file));
				$time = date ("H:i:s", filemtime($path.'/'.$file));
				
				$xml.='<lastmod>'.$date.'T'.$time."+00:00</lastmod>\r\n";
				$xml.='</sitemap>'."\r\n";
			}
		}


		$handle = fopen(CONF_INSTALLATION_PATH . "sitemap.xml","w");
		$xml.=$sitemap_xml_end;
		fwrite($handle,$xml);
		fclose($handle);
	}

	/**
	 * GZIPs a file on disk (appending .gz to the name)
	 * http://www.php.net/manual/en/function.gzwrite.php#34955
	 * 
	 * @param string $source Path to file that should be compressed
	 * @param integer $level GZIP compression level (default: 9)
	 * @return string New filename (with .gz appended) if success, or false if operation fails
	 */
	private function gzCompressFile($source,$flag = false, $level = 9){ 
		#THE BELOW CODE HAS NOT BEEN TESTED
        if($flag){		
			$dest = $source . '.gz'; 
			$mode = 'wb' . $level; 
			$error = false; 
			if ($fp_out = gzopen($dest, $mode)) { 
				if ($fp_in = fopen($source,'rb')) { 
					while (!feof($fp_in)) 
						gzwrite($fp_out, fread($fp_in, 1024 * 512)); 
					fclose($fp_in); 
				} else {
					$error = true; 
				}
				gzclose($fp_out); 
			} else {
				$error = true; 
			}
			if ($error){
				return false;
			}else{
				unlink($source);
				return $dest; 
			}
	    }
		return; 
	}
}
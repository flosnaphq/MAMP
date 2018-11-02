<?php
class Shortcodes extends MyAppModel {
	protected $tpl;
	protected $_db;
	public static $instance;
	
	function __construct(){
		$this->tpl = new FatTemplate('', '');
		$this->_db = FatApp::getdb();
    }
	
	public static function getInstance() {
		if (!self::$instance) {
		  self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	private function pregexp()
	{
		return '/\[([a-zA-Z0-9-_: |=,"\'\.]+)]/';
	}
	// Parsing shortcodes in content
	public function parse ($str)
    {
		// Find matches against shortcodes like [block id=1]
		
		$reqExp = $this->pregexp();

        preg_match_all($reqExp, $str, $shortcodes);
        
		if ($shortcodes == NULL)
		{
		    return $str;
        }
        $shortcode_array = array();
        foreach ($shortcodes[1] as $key => $shortcode)
		{
            if (strstr($shortcode, ' '))
			{
				$code = substr($shortcode, 0, strpos($shortcode, ' '));
				$attrStr = str_replace($code . ' ', '', $shortcode);
				$tmp = explode(' ', $attrStr);
				// Info::test($tmp);
                $parameters = array();
                if (count($tmp))
				{
                    foreach ($tmp as $params)
					{
                        $pair = explode('=', $params);
						if(count($pair) > 1)
						{
							$paramVal = str_replace(array("'", '"'), "", $pair[1] );
							$param = trim($pair[0]);
							$parameters[$param] = trim($paramVal);
						}
                    }
                }
                $array = array('shortcode' => $shortcodes[0][$key], 'code' => $code, 'params' => $parameters);
            }
            else
			{
                $array = array('shortcode' => $shortcodes[0][$key], 'code' => $shortcode, 'params' => array());
            }
            // $shortcode_array[$shortcodes[0][$key]] = $array;
            $shortcode_array[] = $array;
        }
        // Info::test($shortcode_array);
		// Replace shortcode instances with HTML strings
		if (count($shortcode_array))
		{
			foreach ($shortcode_array as $search => $shortcodeData)
			{
				switch($shortcodeData['code'])
				{
					case 'block' :
						$str = str_replace($shortcodeData['shortcode'], $this->getBlockContent($shortcodeData['params']), $str);
						break;
					default:
						$func = strtolower($shortcodeData['code']);
						if(method_exists($this, $func)){
							$str = str_replace($shortcodeData['shortcode'],  $this->$func($shortcodeData['params']), $str);
						}
					break;
				}
			}
        }
        // Return the entire parsed string
		return $str;
    }
	
	function getBlockContent($params = array())
	{
		if(count($params))
		{
			if(isset($params['blkid']) && $params['blkid'] > 0)
			{
				$content = $this->getBlock($params['blkid']);
				if($content)
				{
					$str = $this->parse($content['block_content']);
					return $str;
				}
			}
		}
		return '';
	}
	
	private function getBlock($blkid){
		$record = array();
		$srch = new SearchBase('tbl_blocks');
		
		$srch->addCondition('block_id', '=', $blkid);
		$srch->addCondition('block_active', '=', 1);
		
		$srch->doNotCalculateRecords();
		$srch->doNotLimitRecords();
		
		$rs = $srch->getResultSet();
		
		$record = $this->_db->fetch($rs);
		return $record;		
	}
	
	public function fat_testimonials($params = array())
	{
		$srch = Testimonial::getSearchObject(true, true);
		
		if(isset($params['limit']) && $params['limit'] > 0)
		{
			$srch->setPageSize($params['limit']);
		}
		$srch->addCondition(Testimonial::DB_TBL_PREFIX.'status', '=', 1);
		$srch->addOrder(Testimonial::DB_TBL_PREFIX.'display_order');
		$rs = $srch->getResultSet();
		$testimonials = FatApp::getDb()->fetchAll($rs,Testimonial::DB_TBL_PREFIX.'id');
		
		$html = '';
		if(count($testimonials) > 0)
		{
			$cls = 'testimonial__section';
			$id = 'asSeenOn';
			if(!empty($params['fatclass']))
			{
				$cls = implode(' ', explode(',', $params['fatclass']));
			}
			
			if(!empty($params['fatid']))
			{
				$id = $params['fatid'];
			}
			
			$html .= '<section class="section ' . $cls . '" id=" ' . $id . '">
				<div class="section__header">
					 <div class="container container--static">
						 <div class="span__row">
							 <div class="span span--10 span--center">
								 <hgroup>
									 <h5 class="heading-text text--center">' . Info::t_lang('AS_SEEN_ON') . '</h5>
								 </hgroup>
							 </div>
						 </div>
					</div>   
				</div>
				<div class="section__body">
					<div class="container container--static">
						<div class="span__row js-carousel" data-slides="2">';
							foreach($testimonials as $testimonial)
							{
			$html .=			'<div class="span span--6">
									<div class="media testimonial__item">
										<div class="media__figure media--left">
											<div class="testimonial__image"><img alt="' . $testimonial[Testimonial::DB_TBL_PREFIX.'name'] . '" title="' . $testimonial[Testimonial::DB_TBL_PREFIX.'name'] . '" src="' . FatUtility::generateUrl('image','testimonial',array($testimonial[Testimonial::DB_TBL_PREFIX.'id'],100,100)) . '"></div>
										</div>
										<div class="media__body testimonial__content">
											<h6 class="testimonial__heading">' . $testimonial[Testimonial::DB_TBL_PREFIX.'name'] . '</h6>
											<p class="testimonial__text">' . $testimonial[Testimonial::DB_TBL_PREFIX.'content'] . '</p>
										</div>     
									</div>
								</div>';
							} 
			$html .= 		'</div>
					</div>
				 </div>
			</section>';
		}
		return $html;
	}
	
	public function fat_team($params = array())
	{
		
	}
	
	public function fat_founders($params = array())
	{
		$founderObj = new Founder();
		$founders = $founderObj->getFounders();
		
		$html = '';
		if(!empty($founders))
		{
			$cls = 'section--top-border founder__section';
			$id = 'founder';
			
			if(!empty($params['fatclass']))
			{
				$cls = implode(' ', explode(',', $params['fatclass']));
			}
			
			if(!empty($params['fatid']))
			{
				$id = $params['fatid'];
			}
			
			$html .= '<section class="section ' . $cls . ' " id="' . $id . '">
				 <div class="section__header">
					 <div class="container container--static">
						 <div class="span__row">
							 <div class="span span--10 span--center">
								 <hgroup>
									 <h5 class="heading-text text--center">'. Info::t_lang('MEET_THE_FOUNDERS') .'</h5>
									 <h6 class="sub-heading-text text--center text--green">'. Info::t_lang('WHO_MADE_IDEA_REAL') . '</h6>
								 </hgroup>
							 </div>
						 </div>
					</div> 
				 </div>';
			$html .=  '<div class="section__body">
						<div class="container container--static">
							<div class="span__row">
								<div class="span span--10 span--center">
									<div class="founder__list">';
										foreach($founders as $founder)
										{ 
			$html .=					'<div class="media founder__item">
												<div class="media__figure media--left founder__image">
													<img src="'. FatUtility::generateUrl('image','founder',array($founder['founder_id'],480,480)) .'" alt="">
												</div>
												<div class="media__body founder__content">
													<h6 class="founder__name">'. $founder['founder_name'] .'</h6>
													<span class="founder__desi">'. $founder['founder_designation'] .'</span>
													<div class="founder__desc">
														<div class="innova-editor">'.
															html_entity_decode($founder['founder_content'])
														.'</div>
													</div>
												</div>
											</div>';
										}
			$html .=				'</div>
								</div>
							</div>
						</div>
					</div>
			</section>';
		}
		return $html;
	}
	public function fat_investors($params = array())
	{
		$investor = new Investor();
		$investors = $investor->getInvestors();
		$html = '';
		if(!empty($investors))
		{
			$cls = 'section--light investor__section';
			$id = 'investor';
			if(!empty($params['fatclass']))
			{
				$cls = implode(' ', explode(',', $params['fatclass']));
			}
			
			if(!empty($params['fatid']))
			{
				$id = $params['fatid'];
			}
			$html .= '<section class="section ' . $cls . ' " id="' . $id . '">
						<div class="section__header">
							<div class="container container--static">
								<div class="span__row">
									<div class="span span--12">
										<hgroup>
											<h5 class="heading-text text--center">'. Info::t_lang('OUR_INVESTORS') .'</h5>
											<h6 class="sub-heading-text text--center text--green">'. Info::t_lang('WHO_BELIEVE_US') . '</h6>
										</hgroup>
									</div>
								 </div>
							</div> 
						</div>';
			$html .= '<div class="section__body">
							<div class="container container--static">
								<div class="span__row">
									<div class="span span--12">
										<div class="investor__list">
											foreach($investors as $investor){
												<a href="'. $investor['investor_link'] . '" target="_blank" class="investor__item">
												<img src="'. FatUtility::generateUrl('image','investor',array($investor['investor_id'],155,103)) .'" alt="">
											</a>
											}							
										</div>
									 </div>
								 </div>
							 </div>
						</div>
			</section>';
		}
		return $html;
	}
	
	public function fat_contactinfo($params = array())
	{
		$cls = 'tip__section';
		$id = 'tips';
		if(!empty($params['fatclass']))
		{
			$cls = implode(' ', explode(',', $params['fatclass']));
		}
		
		if(!empty($params['fatid']))
		{
			$id = $params['fatid'];
		}
		
		$html = '<section class="section ' . $cls . ' " id="' . $id . '">
			<div class="section__header">
				 <div class="container container--static">
					 <div class="span__row">
						 <div class="span span--10 span--center">
							 <hgroup>
								 <h5 class="heading-text text--center">' . Info::t_lang('CONTACT_DETAILS') . '</h5>
							 </hgroup>
						 </div>
					 </div>
				</div>   
			</div>
			<div class="section__body">
				<div class="container container--static">
					 <div class="span__row">
						 <div class="span span--10 span--center">
							 <article class="island-tip">
								 <div class="tip__list island-tip__list">
									<div class="media tip__item">
										<div class="media__figure tip__image">
											<div class="tip__icon">
												<img src="/images/graphics/mail.svg" alt="">
											</div>
										</div>
										<div class="media__body media--middle tip__content">
											<h6 class="tip__heading">' . Info::t_lang('EMAIL') . '</h6>
											<p class="tip__text">' . FatApp::getConfig('CONTACT_US_EMAIL_ID') . '</p>
										</div>
									</div>
									<div class="media tip__item">
										<div class="media__figure tip__image">
											<div class="tip__icon">
												<img src="/images/graphics/viber.svg" alt="">
											</div>
										</div>
										<div class="media__body media--middle tip__content">
											<h6 class="tip__heading">' . Info::t_lang('VIBER_LINE') . '</h6>
											<p class="tip__text">' . FatApp::getConfig('VIBER_LINE') . '</p>
										</div>
									</div>
									<div class="media tip__item">
										<div class="media__figure tip__image">
											<div class="tip__icon">
												<img src="/images/graphics/skype.svg" alt="">
											</div>
										</div>
										<div class="media__body media--middle tip__content">
											<h6 class="tip__heading">' . Info::t_lang('SKYPE') . '</h6>
											<p class="tip__text">' . FatApp::getConfig('SKYPE_ID') . '</p>
										</div>
									</div>
								 </div>
							 </article>
						 </div>
					 </div>
				 </div>
			 </div>
		 </section>';
		return $html;
	}
	
	public function fat_offices($params = array())
	{
		$ofc = new Office();
		$offices = $ofc->getOffices();
		$html = '';
		if(!empty($offices))
		{
			$cls = 'why-choose__section';
			$id = 'whyChooseUs';
			if(!empty($params['fatclass']))
			{
				$cls = implode(' ', explode(',', $params['fatclass']));
			}
			
			if(!empty($params['fatid']))
			{
				$id = $params['fatid'];
			}
			$html .= '<section class="section ' . $cls . ' " id="' . $id . '">
				<div class="section__header">
					 <div class="container container--static">
						 <div class="span__row">
							 <div class="span span--10 span--center">
								 <hgroup>
									 <h5 class="heading-text text--center">' . Info::t_lang("VISIT_HEADQUARTERS") . '</h5>
								 </hgroup>
							 </div>
						 </div>
					</div>   
				</div>
				<div class="section__body">
					<div class="container container--static"> 
						<div class="span__row"> 
							<div class="span span--10 span--center"> 
								<article class="wcu"> 
									<div class="point__list wcu-point__list">';
										if(isset($offices[1]))
										{
											$office = $offices[1];
					$html .=				'<div class="media point__item">
												<div class="media__figure point__image">
													<div class="point__icon"><img alt="" src="/images/graphics/singapore.svg" />
													</div>
												</div>
												<div class="media__body media--middle point__content">
													<h6 class="point__heading">' . $office['office_country'] . '</h6>
													<ul class="list list--vertical">
														<li>' . nl2br($office['office_address']) . '</li>   
													</ul>
												</div>
											</div>';
										} 
										if(isset($offices[2])){
											$office = $offices[2];
					$html .=				'<div class="media point__item">
												<div class="media__figure point__image">
													<div class="point__icon"><img alt="" src="/images/graphics/singapore.svg" /></div>
												</div>
												<div class="media__body media--middle point__content">
													<h6 class="point__heading">' . $office['office_country'] . '</h6>
													<ul class="list list--vertical">
														<li>' . nl2br($office['office_address']) .'</li>
													</ul>
												</div>
											</div>';
										}
				$html .=			'</div> 
								</article>
							</div> 
						</div> 
					</div> 
				</div>
			</section>';
		}
		return $html;
	}
	
	public function fat_contactform()
	{
		$frm = $this->getContactForm();
		
		$this->tpl->set('frm', $frm);
		$formHtml = $this->tpl->render(false, false, 'cms/contact-form.php', true);
		
		return $formHtml;
	}
	
	public function getContactForm(){
		$frm = new Form('frmContact');
	
		$frm->addRequiredField(Info::t_lang('NAME'),"name");
		$frm->addEmailField(Info::t_lang('EMAIL_ADDRESS'),"email");
		$fld = $frm->addSelectBox(Info::t_lang('HOW_CAN_WE_HELP'),'option',Info::contactUsOptions(),'',array(),'');
		$fld->requirements()->setRequired();
		$fld = $frm->addTextArea(Info::t_lang('MESSAGE'), 'message')->requirements()->setRequired();
	
		$fld=$frm->addRequiredField(Info::t_lang('SECURITY_CODE'), 'security_code','',array('autocomplete'=>'off'))->htmlAfterField='<div class="captcha-wrapper"><img src="'.FatUtility::generateUrl("image","captcha").'" id="image" class="captcha captchapic"/><a href="javascript:void(0);" class ="reloadpic reloadlink reload" onclick="refreshCaptcha(\'image\')"><svg class="icon icon--reload">
				<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-reload"></use>
			</svg></a></div>';
		$fld = $frm->addSubmitButton('&nbsp;', 'btn_submit', Info::t_lang('SUBMIT'));
		return $frm;
	}
}


<?php
/*
Available shortcodes:

[fat_partnershipform] To get Partnership Form
[fat_contactinfo] To get COntact info
[fat_offices] To get Office addresses
[fat_contactform] TO get COntact Form
[block blkid='22'] To get a block HTML
[fat_founders fatclass='section--top-border'] To get all added Founders
[fat_investors fatclass='section--top-border section--light investor__section'] To get all added Investores
[fat_testimonials limit='2' fatclass="test testimonial__section section--top-border" id="asSeenOn"] To get all added Testimonials
[fat_sociallinks heading="HANG OUT WITH US", list="facebook, twitter, linkedin"]
*/

require_once CONF_APPLICATION_PATH . 'utilities/commonshortcodefunctions.class.php';

class Shortcodes extends MyAppModel
{
	use CommonShortcodeFunctions;
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
	public function parse ($content)
    {
		// Find matches against shortcodes like [block blkid=1]
		$reqExp = $this->pregexp();

        preg_match_all($reqExp, $content, $shortcodes, PREG_SET_ORDER);
        
		if ($shortcodes == NULL)
		{
		    return $content;
        }
		// Info::test($shortcodes);
        $shortcode_array = array();
        foreach ($shortcodes as $key => $vals)
		{
			$fullshortcode = $vals[0];
			$shortcode = $vals[1];
			
            if (strstr($shortcode, ' '))
			{
				$code = substr($shortcode, 0, strpos($shortcode, ' '));
				$attrStr = str_replace($code . ' ', '', $shortcode);
				
				$pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
				$tmp = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $attrStr);
				
				preg_match_all($pattern, $tmp, $match, PREG_SET_ORDER);
					
				$parameters = array();
                if (count($match))
				{
                    foreach ($match as $params)
					{
                        $pair = explode('=', $params[0]);
						if(count($pair) > 1)
						{
							$paramVal = str_replace(array("'", '"'), "", $pair[1] );
							$param = trim($pair[0]);
							$parameters[$param] = trim($paramVal);
						}
                    }
                }
                $array = array('shortcode' => $fullshortcode, 'code' => $code, 'params' => $parameters);
            }
            else
			{
                $array = array('shortcode' => $fullshortcode, 'code' => $shortcode, 'params' => array());
            }
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
						$content = str_replace($shortcodeData['shortcode'], $this->getBlockContent($shortcodeData['params']), $content);
						break;
					default:
						$func = strtolower($shortcodeData['code']);
						if(method_exists($this, $func)){
							$content = str_replace($shortcodeData['shortcode'],  $this->$func($shortcodeData['params']), $content);
						}
					break;
				}
			}
        }
        // Return the entire parsed string
		return $content;
    }
	
	function getBlockContent($params = array())
	{
		$str = '';
		if(count($params) > 0)
		{
			$cls = 'section--block';
			$id = '';
			if(!empty($params['fatclass']))
			{
				$cls = $params['fatclass'];
			}
			
			if(!empty($params['fatid']))
			{
				$id = $params['fatid'];
			}
			$str .= '<section class="section '.$cls .' ">
						<div class="container container--static">';
			if(isset($params['blkid']) && $params['blkid'] > 0)
			{
				$content = $this->getBlock($params['blkid']);
				if($content)
				{
					$str .= $this->parse($content['block_content']);
				}
			}
			$str .=		'</div>
					</section>';
		}
		return $str;
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
				$cls = $params['fatclass'];
			}
			
			if(!empty($params['fatid']))
			{
				$id = $params['fatid'];
			}
			$this->tpl->set('cls', $cls );
			$this->tpl->set('id', $id );
			$this->tpl->set('testimonials', $testimonials );
			$html = $this->tpl->render(false, false, '_partial/templates/fat-testimonials.php', true);
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
				$cls = $params['fatclass'];
			}
			
			if(!empty($params['fatid']))
			{
				$id = $params['fatid'];
			}
			$this->tpl->set('cls', $cls );
			$this->tpl->set('id', $id );
			$this->tpl->set('founders', $founders );
			$html = $this->tpl->render(false, false, '_partial/templates/fat-founders.php', true);
			
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
				$cls = $params['fatclass'];
			}
			
			if(!empty($params['fatid']))
			{
				$id = $params['fatid'];
			}
			$this->tpl->set('cls', $cls );
			$this->tpl->set('id', $id );
			$this->tpl->set('investors', $investors );
			$html = $this->tpl->render(false, false, '_partial/templates/fat-investors.php', true);
		}
		return $html;
	}
	
	public function fat_contactinfo($params = array())
	{
		$cls = 'tip__section';
		$id = 'tips';
		if(!empty($params['fatclass']))
		{
			$cls = $params['fatclass'];
		}
		
		if(!empty($params['fatid']))
		{
			$id = $params['fatid'];
		}
		
		$this->tpl->set('cls', $cls );
		$this->tpl->set('id', $id );
		$html = $this->tpl->render(false, false, '_partial/templates/fat-contactinfo.php', true);
		
		return $html;
	}
	
	public function fat_offices($params = array())
	{
		$ofc = new Office();
		$offices = $ofc->getOffices();
		$html = '';
		if(count($offices) > 0)
		{
			$cls = 'why-choose__section';
			$id = 'whyChooseUs';
			if(!empty($params['fatclass']))
			{
				$cls = $params['fatclass'];
			}
			
			if(!empty($params['fatid']))
			{
				$id = $params['fatid'];
			}
			$this->tpl->set('cls', $cls );
			$this->tpl->set('id', $id );
			$this->tpl->set('offices', $offices );
			$html = $this->tpl->render(false, false, '_partial/templates/fat-offices.php', true);
		}
		return $html;
	}
	
	public function fat_contactform()
	{
		$frm = $this->getContactForm();
		
		$this->tpl->set('frm', $frm);
		$formHtml = $this->tpl->render(false, false, '_partial/templates/fat-contact-form.php', true);
		
		return $formHtml;
	}
	

	
	public function getContactForm(){
		$frm = new Form('frmContact');
	
		$frm->addRequiredField(Info::t_lang('NAME'),"name");
		$frm->addEmailField(Info::t_lang('EMAIL_ADDRESS'),"email");
		$fld = $frm->addSelectBox(Info::t_lang('HOW_CAN_WE_HELP'),'option',Info::contactUsOptions(),'',array(),'');
		$fld->requirements()->setRequired();
		$fld = $frm->addTextArea(Info::t_lang('MESSAGE'), 'message')->requirements()->setRequired();
	
		$captchaSiteKey		= FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '');
		if($captchaSiteKey !='') {
			$frm->addHtml('', 'security_code','<div class="g-recaptcha" data-sitekey="'.$captchaSiteKey.'"></div>');
		}
		
		$fld = $frm->addSubmitButton('&nbsp;', 'btn_submit', Info::t_lang('SUBMIT'));
		return $frm;
	}
	
	public function getPartnershipsForm(){
		$frm = new Form('partnershipsFrm');
		$frm->addRequiredField(Info::t_lang('NAME'),'partner_name');
		$partner_email = $frm->addEmailField(Info::t_lang('EMAIL'),'partner_email');
		$partner_email->setUnique(Partners::DB_TBL,Partners::DB_TBL_PREFIX.'email',Partners::DB_TBL_PREFIX.'id','partner_email','partner_email');
		
		$frm->addTextBox(Info::t_lang('COMPANY'),'partner_company');
		$frm->addTextBox(Info::t_lang('WEBSITE'),'partner_website');
		$frm->addTextArea(Info::t_lang('MESSAGE'),'partner_message');
		$countries = Country::getCountries();
		$partner_country = $frm->addSelectBox(Info::t_lang('COUNTRY'),'partner_country',$countries,'',array(),Info::t_lang('SELECT_COUNTRY'));
		$partner_country->requirements()->setRequired();
		$partner_describe = $frm->addSelectBox(Info::t_lang('HOW_TO_DESCRIBE_YOU'),'partner_describe',Info::PartnerDescribe(),1,array(),'');
		$partner_describe->requirements()->setRequired();
		
		$submit_btn = $frm->addSubmitButton('', 'btn_submit', Info::t_lang('SUBMIT'),array('class'=>'button button--fill button--green'));
		return $frm;
	}
	
	public function fat_mailchimpnewsletter($params = array())
	{
		$params['fieldcols'] = ((isset($params['fieldcols']) && $params['fieldcols'] > 0) ? $params['fieldcols'] : 9);
		
		$frm = Helper::getNewsletterForm($params);
		
		$this->tpl->set('params', $params);
		$this->tpl->set('frm', $frm);
		$formHtml = $this->tpl->render(false, false, '_partial/templates/fat-mailchimp-form.php', true);
		
		return $formHtml;
	}
	
}


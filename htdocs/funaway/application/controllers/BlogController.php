<?php
class BlogController extends MyAppController 
{
    const PAGESIZE = 10;
    const RECENT_POSTSIZE = 10;

    private $total_records, $total_pages;

    public function __construct($action) {
        parent::__construct($action);
        $this->page_class .='is--blog';
        $this->set('page_class', $this->page_class);
        $this->_template->addJs('blog/page-js/social.js');
    }

    public function index() {
        $this->topPanel();
        $this->_template->render();
    }

    function topPanel() {
        $cat = new BlogCategories();
        $categories = $cat->getCategories();
        $this->set('categories', $categories);
    }

    public function listPosts() {

        $post = FatApp::getPostedData();
        $blogPost = new BlogPosts();
        $blog_cat = new BlogCategories();
        $post['page'] = FatUtility::int($post['page']);
        $post['page'] = $page = ( $post['page'] > 0 ) ? $post['page'] : 1;
        $post['pagesize'] = $pagesize = static::PAGESIZE;

        /* if( isset( $post['search'] ) ) { 
          $posts = $blogPost->getBlogPosts( $post );
          $blog_cat->getPostCategories( $posts['records'] );
          } else */
        if (isset($post['cat'])) {

            $post['seo_name'] = $post['cat'];
            unset($post['cat']);

            $posts = $blog_cat->getBlogPostsByCategory($post);
            //$blog_cat->getPostCategories( $posts['records'] );
        } /* elseif( isset( $post['year'] ) && isset( $post['month'] ) ) { 

          $post['year']		= ( $post['year'] )?$post['year']:date('Y',Info::timestamp());
          $post['month']		= ( $post['month'] )?$post['month']:date('m',Info::timestamp());
          $posts = $blogPost->getBlogPosts( $post );
          //$blog_cat->getPostCategories( $posts['records'] );

          } */ else {
            $posts = $blogPost->getBlogPosts($post);
            //$blog_cat->getPostCategories( $posts['records'] );
        }
        //Info::test($posts);
        $this->set("posts", $posts['records']);
        $this->set("__metaData", array());
        $this->set('pagination', $this->getPagination(@$posts['total_pages'], $page, 5, 'getBlogPosts'));

        $this->_template->render(false, false, 'blog/list-posts.php', false, true);
    }

    public function post($postSeoName = '') {
        $this->_template->addJs('common-js/plugins/slick.min.js');

        $blog_post = new BlogPosts();
        $blog_cat = new BlogCategories();
        $postData = $blog_post->getBlogPostsByName($postSeoName);

        if (empty($postData)) {

            Message::addErrorMessage(Info::t_lang('POST_NOT_FOUND'));
            FatApp::redirectUser(FatUtility::generateUrl('Blog'));
        }

        $commentFrm = $this->getCommentForm();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $post = $commentFrm->getFormDataFromArray(FatApp::getPostedData());
            unset($post['security_code']);
            $commentFrm->fill($post);
        } elseif (User::isUserLogged()) {
            $usr = new User();
            $user_detail = $usr->getUserByUserId(User::getLoggedUserId());
            $user_first_name = $user_detail['user_firstname'];
            $fld = $commentFrm->getField('comment_author_name');
            if ($fld) {
                $fld->value = trim($user_first_name);
            }
        }


        $metaData = array();
        if (isset($postData[BlogPosts::DB_CHILD_TBL_PREFIX . 'title']) ||
                isset($postData[BlogPosts::DB_CHILD_TBL_PREFIX . 'keywords']) ||
                isset($postData[BlogPosts::DB_CHILD_TBL_PREFIX . 'description'])) {

            $metaData['title'] = $postData[BlogPosts::DB_CHILD_TBL_PREFIX . 'title'];
            $metaData['keywords'] = $postData[BlogPosts::DB_CHILD_TBL_PREFIX . 'keywords'];
            $metaData['description'] = $postData[BlogPosts::DB_CHILD_TBL_PREFIX . 'description'];
            $metaData['other_meta_tags'] = $postData[BlogPosts::DB_CHILD_TBL_PREFIX . 'others'];
            $metaData['image'] = FatUtility::generateFullUrl('Image', 'postImage', array($postData[BlogPosts::DB_TBL_PREFIX . 'id'], 600, 315));
            $metaData['og:title'] = $metaData['title'];
            $metaData['og:type'] = Info::t_lang('WEBSITE');
            $metaData['og:image'] = $metaData['image'];
            $metaData['og:url'] = FatUtility::generateFullUrl('blog', 'post', array($postData['post_seo_name']));
            $metaData['og:description'] = $metaData['description'];
            $metaData['og:site_name'] = FatApp::getConfig("conf_website_name");

            $this->set("__metaData", $metaData);
        }

        $sliderImages = $blog_post->getAllImagesOfPost($postData[BlogPosts::DB_TBL_PREFIX . 'id']);

        $this->set("__sliderImages", $sliderImages);

        $title = ( isset($postData[BlogPosts::DB_TBL_PREFIX . 'title']) ) ? $postData[BlogPosts::DB_TBL_PREFIX . 'title'] : '';
        $this->set("pageTitle", $title);
        $next_post_slug = $blog_post->getNextPostSlug($postData[BlogPosts::DB_TBL_PREFIX . 'id']);
        $pre_post_slug = $blog_post->getPreviousPostSlug($postData[BlogPosts::DB_TBL_PREFIX . 'id']);

        $this->set("next_post_slug", $next_post_slug);
        $this->set("pre_post_slug", $pre_post_slug);
        $this->set("metaData", $metaData);
        $this->set("commentFrm", $commentFrm);
        $this->set("postSeoName", $postSeoName);

        $this->set("post", $postData);
        $this->_template->render(true, true, 'blog/post.php', false, true);
    }

    public function saveComment($postSeoName = '') {

        if (strlen($postSeoName) <= 0) {
            Message::addErrorMessage(Info::t_lang('COMMENTINVALID_REQUEST'));
            FatApp::redirectUser(FatUtility::generateUrl('Blog', 'post', array($postSeoName)));
        }
        $blog_post = new BlogPosts();
        $blog_comment = new Blogcomments();
        $postData = $blog_post->getBlogPostsByName($postSeoName);
        if (!$postData) {
            Message::addErrorMessage(Info::t_lang('INVALID_REQUEST'));
            FatApp::redirectUser(FatUtility::generateUrl('Blog'));
        }


        $frm = $this->getCommentForm();

        if (User::isUserLogged()) {
            $field = $frm->getField('comment_author_email');
            if ($field)
                $frm->removeField($field);
        }

        $post = $frm->getFormDataFromArray(FatApp::getPostedData());

        if ($post == false) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            $this->post($postSeoName);
            return;
        }
        
		if(FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '') && !Helper::verifyCaptcha($_POST['g-recaptcha-response']))
		{
			Message::addErrorMessage(Info::t_lang('INCORRECT_SECURITY_CODE'));
            $this->post($postSeoName);
            return;
		}

        if (User::isUserLogged()) {
            $authorEmail = User::getLoggedUserAttribute(User::DB_TBL_PREFIX . "email");
            $post['comment_author_email'] = $authorEmail;
        }

        $apacheRequest = FatApp::getApacheRequestHeaders();
        $insertedData = array(
            Blogcomments::DB_TBL_PREFIX . 'post_id' => $postData[BlogPosts::DB_TBL_PREFIX . 'id'],
            Blogcomments::DB_TBL_PREFIX . 'author_name' => $post['comment_author_name'],
            Blogcomments::DB_TBL_PREFIX . 'author_email' => $post['comment_author_email'],
            Blogcomments::DB_TBL_PREFIX . 'content' => strip_tags($post['comment_content']),
            Blogcomments::DB_TBL_PREFIX . 'status' => 0,
            Blogcomments::DB_TBL_PREFIX . 'date_time' => 'mysql_func_now()',
            Blogcomments::DB_TBL_PREFIX . 'ip' => $_SERVER['REMOTE_ADDR'],
            Blogcomments::DB_TBL_PREFIX . 'user_agent' => $apacheRequest['User-Agent'],
            Blogcomments::DB_TBL_PREFIX . 'user_id' => FatUtility::int(User::getLoggedUserId(true))
        );

        if (!$blog_comment->addComment($insertedData)) {
            Message::addErrorMessage(Info::t_lang('SOMETHING_WENT_WRONG._PLEASE_TRY_AGAIN.'));
            $this->post($postSeoName);
            return;
        }
        $replace_vars = array(
            '{name}' => $insertedData[Blogcomments::DB_TBL_PREFIX . 'author_name'],
            '{blog_name}' => $postData[BlogPosts::DB_TBL_PREFIX . 'title'],
            '{email}' => $insertedData[Blogcomments::DB_TBL_PREFIX . 'author_email'],
            '{comment}' => $insertedData[Blogcomments::DB_TBL_PREFIX . 'content']
        );
        Email::sendMail(FatApp::getConfig('conf_admin_email_id'), 34, $replace_vars);
        $notify_text = Info::t_lang('New Comment Post On %s blog by %s');
        $notify_text = sprintf($notify_text, $postData[BlogPosts::DB_TBL_PREFIX . 'title'], $insertedData[Blogcomments::DB_TBL_PREFIX . 'author_name']);
        $notify = new Notification();
        $notify->notify(0, 0, '', $notify_text);
        Message::addMessage(Info::t_lang('COMMENT_POSTED!_AWAITING_FOR_ADMIN_APPROVAL.'));
        FatApp::redirectUser(FatUtility::generateUrl('Blog', 'post', array($postSeoName)));
    }

    private function getCommentForm() {

        $frm = new Form('frmComment');

        $fld = $frm->addRequiredField(Info::t_lang('NAME'), 'comment_author_name', '');
        $fld->setRequiredStarWith('none');

        if (!User::isUserLogged()) {
            $fld = $frm->addEmailField(Info::t_lang('EMAIL_ADDRESS'), 'comment_author_email', '');
            $fld->setRequiredStarWith('none');
        }
        $fld = $frm->addTextArea(Info::t_lang('COMMENT'), 'comment_content');
        $fld->requirements()->setRequired();
        $fld->setRequiredStarWith('none');
		
        $captchaSiteKey		= FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '');
		if($captchaSiteKey !='') {
			$frm->addHtml('', 'security_code','<div class="g-recaptcha" data-sitekey="'.$captchaSiteKey.'"></div>');
		}
		
        $fld = $frm->addSubmitButton('', 'btn_submit', Info::t_lang('POST_MY_COMMENT'));
        //$fld->developerTags['noCaptionTag'] =true; 
        return $frm;
    }

    public function category($seoName = '') {

        if (!$seoName) {
            Message::addErrorMessage(Info::t_lang('INVALID_CATEGORY'));
            FatApp::redirectUser(FatUtility::generateUrl('Blog'));
        }
        $this->set('cat_seo_name', $seoName);
        $this->topPanel();
        $this->set("seoName", $seoName);
        $this->_template->render(true, true, 'blog/category.php', false, true);
    }

    function getPagination($pageCount, $pageNumber = 1, $linksToDisp = 10, $callBackJsFunc = '') {
        $pagination = '';
        if ($pageCount <= 1) {
            return $pagination;
        }

        if ($callBackJsFunc == '') {
            $callBackJsFunc = 'listPage';
        }
        $callBackJsFu = $callBackJsFunc;
        $callBackJsFunc = $callBackJsFunc . '(xxpagexx);';

        $pagination .= '<nav  class="pagination no--padding text--center">';
        if ($pageNumber > 1) {
            $pagination .='<a  href="javascript:;" onclick="' . $callBackJsFu . '(' . ($pageNumber - 1) . ')" class="button button--fill button--dark fl--left">' . Info::t_lang('NEW') . ' <span class="hidden-on--mobile">' . Info::t_lang('BLOG') . ' </span>' . Info::t_lang('POSTS') . '</a>';
        }
        if ($pageNumber < $pageCount) {
            $pagination .='<a  href="javascript:;" onclick="' . $callBackJsFu . '(' . ($pageNumber + 1) . ')" class="button button--fill button--dark fl--right">' . Info::t_lang('OLD') . ' <span class="hidden-on--mobile">' . Info::t_lang('BLOG') . ' </span>' . Info::t_lang('POSTS') . '</a>';
        }
        $pagination .= '<ul  class="list list--horizontal no--margin-bottom visible-on--desktop">';
        $pagination .= FatUtility::getPageString(
                        '<li><a href="javascript:void(0);" onclick="' . $callBackJsFunc . '">xxpagexx</a></li>', $pageCount, $pageNumber, ' <li ><a  class="active" href="javascript:void(0);">xxpagexx</a></li>', ' <li><a href="javascript:void(0);">...</a></li> ', $linksToDisp, ' <li class="first"><a href="javascript:void(0);" onclick="' . $callBackJsFunc . '">' . Info::t_lang('FIRST') . '</a></li>', ' <li class="last"><a href="javascript:void(0);" onclick="' . $callBackJsFunc . '">' . Info::t_lang('LAST') . '</a></li>', ' <li><a href="javascript:void(0);" onclick="' . $callBackJsFunc . '"><i class="icon ion-ios-arrow-left"></i></a></li>', ' <li><a href="javascript:void(0);" onclick="' . $callBackJsFunc . '"><i class="icon ion-ios-arrow-right"></i></a></li>'
        );
        $pagination .= '</ul></nav>';

        return $pagination;
    }

    public function search() {
        $post = FatApp::getPostedData();

        if (!isset($post['search']) || empty($post['search'])) {
            Message::addErrorMessage(Info::t_lang('INVALID_SEARCH'));
            FatApp::redirectUser(FatUtility::generateUrl('Blog'));
        }

        $this->rightPanel(true);
        $this->brcmb->add(Info::t_lang('SEARCH'));
        $this->brcmb->add($post['search']);
        $this->set('breadcrumb', $this->brcmb->output());
        $this->set('searchTxt', $post['search']);
        $this->_template->render(true, true, 'blog/search.php', false, true);
    }

    public function listPostComments() {
        $blog_comment = new Blogcomments();
        $post = FatApp::getPostedData();
        $post['post_id'] = FatUtility::int($post['post_id']);

        if ($post['post_id'] < 1) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }

        $post['page'] = FatUtility::int($post['page']);
        $post['page'] = $page = ( $post['page'] > 0 ) ? $post['page'] : 1;
        $post['pagesize'] = $pagesize = static::PAGESIZE;

        $allComments = $blog_comment->getPostComments($post);

        $this->set("allComments", $allComments['records']);
        $this->set('pagination', $this->getPagination($allComments['total_pages'], $page, 3, 'blogPostComments'));

        $this->_template->render(false, false, 'blog/list-post-comments.php', false, true);
    }

}

<?php

class AdminPrivilege {
    /* public static function canViewUsers($adminId = 0, $returnResult = false) {
      // Discuss about not querying db again and again for permission check.

      if (false) {
      if ($returnResult) {
      return false;
      }
      FatUtility::dieWithError('Unauthorized Access!');
      }

      return true;
      } */

    const PERMISSION_LEVEL_NONE = 0;
    const PERMISSION_LEVEL_READ = 1;
    const PERMISSION_LEVEL_WRITE = 2;

    private function __construct(){}

	private static function isAdminSuperAdmin ($adminId)
	{
		return ( 1 === $adminId );
	}
	
    static function checkAdminPermission($admin_id, $permission_id, $level = 1) {
        $db = FatApp::getdb();

		$admin_id = FatUtility::int($admin_id);
		
        $permission_id = intval($permission_id);
        if ($admin_id == 0) {
            $admin_id = Admin::getLoggedId();
		}
		
		if (true === static::isAdminSuperAdmin($admin_id) ) {
            return true;
		}
		
        $srch = new SearchBase('tbl_admin_permissions');
        $srch->joinTable('tbl_admin_permission_names','INNER JOIN','permission_permname_id=permname_id');
        $srch->addCondition('permission_admin_id', '=', $admin_id);
        $srch->addCondition('permission_permname_id', '=', $permission_id);
        $rs = $srch->getResultSet();
        if (!$row = $db->fetch($rs))
            return false;
        
     
        if($row['permname_manageable']==0 && $admin_id > 1){
            return false;
        }
        return ($row['permission_level'] >= $level);
    }

    //==>Payment Methods pemission .... permission id = 1 ///////////////////////
    static function canViewPaymentMehods($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 1, self::PERMISSION_LEVEL_READ));
    }

    static function canEditPaymentMehods($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 1, self::PERMISSION_LEVEL_WRITE));
    }

    //==>Country pemission .... permission id = 1 ///////////////////////
    static function canAddHost($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 1, self::PERMISSION_LEVEL_WRITE));
    }

    static function canEditHost($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 1, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewHost($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 1, self::PERMISSION_LEVEL_READ));
    }

    //==>Traveller permission .... permission id = 2 ///////////////////////
    static function canAddTraveller($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 2, self::PERMISSION_LEVEL_WRITE));
    }

    static function canEditTraveller($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 2, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewTraveller($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 2, self::PERMISSION_LEVEL_READ));
    }

    //==>Languages permission .... permission id = 3 ///////////////////////
    static function canAddLanguage($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 3, self::PERMISSION_LEVEL_WRITE));
    }

    static function canEditLanguage($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 3, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewLanguage($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 3, self::PERMISSION_LEVEL_READ));
    }

    //==>Meta pemission .... permission id = 4 ///////////////////////
    static function canAddMeta($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 4, self::PERMISSION_LEVEL_WRITE));
    }

    static function canEditMeta($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 4, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewMeta($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 4, self::PERMISSION_LEVEL_READ));
    }

    //==>CMS pemission .... cms id = 5 ///////////////////////
    static function canAddCms($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 5, self::PERMISSION_LEVEL_WRITE));
    }

    static function canEditCms($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 5, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewCms($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 5, self::PERMISSION_LEVEL_READ));
    }

    //==>FAQ pemission .... Permission id = 6 ///////////////////////
    static function canAddFaq($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 6, self::PERMISSION_LEVEL_WRITE));
    }

    static function canEditFaq($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 6, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewFaq($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 6, self::PERMISSION_LEVEL_READ));
    }

    //==>Email Template management permission .... management id = 7 ///////////////////////

    static function canEditEmailTemp($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 7, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewEmailTemp($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 7, self::PERMISSION_LEVEL_READ));
    }

    //==>Password .... management id = 8 ///////////////////////
    static function canEditPassword($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 8, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewPassword($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 8, self::PERMISSION_LEVEL_READ));
    }

    //==> Admin Users permission .... permission id = 9 ///////////////////////

    static function canEditAdmin($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 9, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewAdmin($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 9, self::PERMISSION_LEVEL_READ));
    }

    //==>Setting permission .... permission id = 10 ///////////////////////
    /* static function canAddSetting($admin_id = 0){
      return (self::checkAdminPermission($admin_id, 10, self::PERMISSION_LEVEL_WRITE));
      }

      static function canEditSetting($admin_id = 0){
      return (self::checkAdminPermission($admin_id, 10, self::PERMISSION_LEVEL_WRITE));
      }

      static function canViewSetting($admin_id = 0){
      return (self::checkAdminPermission($admin_id, 10, self::PERMISSION_LEVEL_READ));
      } */
    //==>Configurations permission .... permission id = 11 ///////////////////////
    static function canViewConfigurations($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 11, self::PERMISSION_LEVEL_READ));
    }

    static function canEditConfigurations($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 11, self::PERMISSION_LEVEL_WRITE));
    }

    //==> World Activities permission .... permission id = 12 ///////////////////////
    /* static function canAddWorldActivites($admin_id = 0){
      return (self::checkAdminPermission($admin_id, 12, self::PERMISSION_LEVEL_WRITE));
      }
      //activities
      static function canEditWorldActivites($admin_id = 0){
      return (self::checkAdminPermission($admin_id, 12, self::PERMISSION_LEVEL_WRITE));
      }

      static function canViewWorldActivites($admin_id = 0){
      return (self::checkAdminPermission($admin_id, 12, self::PERMISSION_LEVEL_READ));
      } */

    //==>Service permission .... Program management id = 13 ///////////////////////

    static function canEditService($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 13, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewService($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 13, self::PERMISSION_LEVEL_READ));
    }

    //==>Message permission .... Program management id = 14 ///////////////////////

    static function canEditMessage($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 14, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewMessage($admin_id = 0) {
        //return false;
        return (self::checkAdminPermission($admin_id, 14, self::PERMISSION_LEVEL_READ));
    }

    //==>Notification permission .... Program management id = 15 ///////////////////////
    static function canViewNotification($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 15, self::PERMISSION_LEVEL_READ));
    }

    static function canEditNotification($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 15, self::PERMISSION_LEVEL_READ));
    }

    //==>Country pemission .... permission id = 16 ///////////////////////
    static function canAddLocation($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 16, self::PERMISSION_LEVEL_WRITE));
    }

    static function canEditLocation($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 16, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewLocation($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 16, self::PERMISSION_LEVEL_READ));
    }

    //==>CMS pemission .... Permission id = 17 ///////////////////////
    static function canAddIsland($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 17, self::PERMISSION_LEVEL_WRITE));
    }

    static function canEditIsland($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 17, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewIsland($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 17, self::PERMISSION_LEVEL_READ));
    }

    //==>Banner pemission .... Program management id = 17 ///////////////////////
    static function canAddBanners($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 18, self::PERMISSION_LEVEL_WRITE));
    }

    static function canEditBanners($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 18, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewBanners($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 18, self::PERMISSION_LEVEL_READ));
    }

    //==>Block pemission .... Program management id = 19 ///////////////////////
    static function canAddBlock($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 19, self::PERMISSION_LEVEL_WRITE));
    }

    static function canEditBlock($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 19, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewBlock($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 19, self::PERMISSION_LEVEL_READ));
    }

    //==>Founder pemission .... Program management id = 20 ///////////////////////
    static function canAddFounder($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 20, self::PERMISSION_LEVEL_WRITE));
    }

    static function canEditFounder($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 20, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewFounder($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 20, self::PERMISSION_LEVEL_READ));
    }

    //==>Investor pemission .... Program management id = 21 ///////////////////////
    static function canAddInvestor($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 21, self::PERMISSION_LEVEL_WRITE));
    }

    static function canEditInvestor($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 21, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewInvestor($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 21, self::PERMISSION_LEVEL_READ));
    }

    //==>Currency permission .... permission id = 3 ///////////////////////
    static function canAddLCurrency($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 22, self::PERMISSION_LEVEL_WRITE));
    }

    static function canEditCurrency($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 22, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewCurrency($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 22, self::PERMISSION_LEVEL_READ));
    }

    //----------------------------------------
    //==>Currency permission .... permission id = 23 ///////////////////////
    static function canAddActivity($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 23, self::PERMISSION_LEVEL_WRITE));
    }

    static function canEditActivity($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 23, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewActivity($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 23, self::PERMISSION_LEVEL_READ));
    }

    //----------------------------------------
    //==>Currency permission .... permission id = 24 ///////////////////////
    static function canAddOffice($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 24, self::PERMISSION_LEVEL_WRITE));
    }

    static function canEditOffice($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 24, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewOffice($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 24, self::PERMISSION_LEVEL_READ));
    }

    //==>Currency permission .... permission id = 25 ///////////////////////
    static function canAddLabel($admin_id = 0) {

        return (self::checkAdminPermission($admin_id, 25, self::PERMISSION_LEVEL_WRITE));
    }

    static function canEditLabel($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 25, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewLabel($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 25, self::PERMISSION_LEVEL_READ));
    }

    //==>Currency permission .... permission id = 25 ///////////////////////
    static function canEditBankAccount($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 26, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewBankAccount($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 26, self::PERMISSION_LEVEL_READ));
    }

    //==>Currency permission .... permission id = 27 ///////////////////////
    static function canEditCancellationPolicy($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 27, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewCancellationPolicy($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 27, self::PERMISSION_LEVEL_READ));
    }

    //==>Currency permission .... permission id = 28 ///////////////////////
    static function canEditReview($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 28, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewReview($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 28, self::PERMISSION_LEVEL_READ));
    }

    //==>Currency permission .... permission id = 29 ///////////////////////
    static function canEditPartner($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 29, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewPartner($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 29, self::PERMISSION_LEVEL_READ));
    }

    //----------------------------------------
    //==>Currency permission .... permission id = 30 ///////////////////////
    static function canEditOrder($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 30, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewOrder($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 30, self::PERMISSION_LEVEL_READ));
    }

    //----------------------------------------
    //==>Wallet permission .... permission id = 31 ///////////////////////
    static function canEditWallet($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 31, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewWallet($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 31, self::PERMISSION_LEVEL_READ));
    }

    //----------------------------------------
    //==>Sms permission .... permission id = 32 ///////////////////////
    static function canEditSmsTemplate($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 32, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewSmsTemplate($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 32, self::PERMISSION_LEVEL_READ));
    }

    //==>Admin commission permission .... permission id = 33 ///////////////////////
    static function canEditAdminCommission($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 33, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewAdminCommission($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 33, self::PERMISSION_LEVEL_READ));
    }

    //==>Admin Withdrawal Request permission .... permission id = 34 ///////////////////////
    static function canEditWithdrawalRequest($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 34, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewWithdrawalRequest($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 34, self::PERMISSION_LEVEL_READ));
    }

    //==>Admin Withdrawal Request permission .... permission id = 35 ///////////////////////
    static function canEditReport($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 35, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewReport($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 35, self::PERMISSION_LEVEL_READ));
    }

    //==>Phone code permission .... permission id = 36 ///////////////////////
    static function canEditPhoneCode($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 36, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewPhoneCode($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 36, self::PERMISSION_LEVEL_READ));
    }

    //==>Navigation permission .... permission id = 37 ///////////////////////
    static function canEditNavigation($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 37, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewNavigation($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 37, self::PERMISSION_LEVEL_READ));
    }

    //----------------------------------------
    //==>Attribute permission .... permission id = 38 ///////////////////////
    static function canEditActivityAttribute($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 38, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewActivityAttribute($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 38, self::PERMISSION_LEVEL_READ));
    }

    //----------------------------------------
    //==>Blog Category permission .... permission id = 39 ///////////////////////
    static function canEditBlogCategory($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 39, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewBlogCategory($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 39, self::PERMISSION_LEVEL_READ));
    }

    //----------------------------------------
    //==> Blog Comment permission .... permission id = 40 ///////////////////////
    static function canEditBlogComment($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 40, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewBlogComment($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 40, self::PERMISSION_LEVEL_READ));
    }

    //----------------------------------------
    //==> Blog Contribute permission .... permission id = 41 ///////////////////////
    static function canEditBlogCont($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 41, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewBlogCont($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 41, self::PERMISSION_LEVEL_READ));
    }

    //==> Blog Post permission .... permission id = 42 ///////////////////////
    static function canEditBlogPost($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 42, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewBlogPost($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 42, self::PERMISSION_LEVEL_READ));
    }

    //==> Testimonials permission .... permission id = 43 ///////////////////////
    static function canEditTestimonial($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 43, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewTestimonial($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 43, self::PERMISSION_LEVEL_READ));
    }

    //==> Region permission .... permission id = 44 ///////////////////////
    static function canAddRegion($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 44, self::PERMISSION_LEVEL_WRITE));
    }

    static function canEditRegion($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 44, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewRegion($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 44, self::PERMISSION_LEVEL_READ));
    }

    //==> City permission .... permission id = 45 ///////////////////////
    static function canAddCity($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 45, self::PERMISSION_LEVEL_WRITE));
    }

    static function canEditCity($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 45, self::PERMISSION_LEVEL_WRITE));
    }

    static function canViewCity($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 45, self::PERMISSION_LEVEL_READ));
    }

    //==>Notification permission .... Program management id = 46 ///////////////////////
    static function canViewUserRequests($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 46, self::PERMISSION_LEVEL_READ));
    }

    static function canEditUserRequests($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 46, self::PERMISSION_LEVEL_WRITE));
    }

    //==>Notification permission .... Program management id = 47 ///////////////////////
    static function canViewMetaTags($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 47, self::PERMISSION_LEVEL_READ));
    }

    static function canEditMetaTags($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 47, self::PERMISSION_LEVEL_WRITE));
    }
    //==>Activity Aduses permission .... Program management id = 48 ///////////////////////
    static function canViewActivityAbuses($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 48, self::PERMISSION_LEVEL_READ));
    }

    static function canEditActivityAbuses($admin_id = 0) {
        return (self::checkAdminPermission($admin_id, 48, self::PERMISSION_LEVEL_WRITE));
    }
    /*
     *  System Restore
     */
    static function canViewSystemRestore($admin_id = 0) {
        return $admin_id==1?true:false;
    }
}

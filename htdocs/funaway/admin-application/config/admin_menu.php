<?php

$header = array(
    'DASHBOARD' => array('controller' => '', 'action' => ''),
    'USERS MANAGEMENT' => array('child' => array(
            'Hosts Management' => array('controller' => 'host', 'action' => '', 'callback' => 'canViewHost'),
            'Travelers Management' => array('controller' => 'traveler', 'action' => '', 'callback' => 'canViewTraveller'),
        )),
    'ACTIVITY MANAGEMENT' => array('child' => array(
            'Manage Listing' => array('controller' => 'activities', 'action' => '', 'callback' => 'canViewActivity'),
            'Activity Types' => array('controller' => 'service', 'action' => '', 'callback' => 'canViewService'),
            'Manage Activity Abuses' => array('controller' => 'activityAbuses', 'action' => '', 'callback' => 'canViewActivityAbuses'),
            'Manage Reviews' => array('controller' => 'reviews', 'action' => '', 'callback' => 'canViewReview'),
        )),
    'BOOKINGS MANAGEMENT' => array('child' => array(
            'Manage Bookings' => array('controller' => 'Orders', 'action' => '', 'callback' => 'canViewOrder'),
            'Manage Booking Cancellation' => array('controller' => 'orderCancelRequests', 'action' => '', 'callback' => 'canViewOrder'),
        )),
    'CMS' => array('child' => array(
            'Navigation Management' => array('controller' => 'navigation', 'action' => '', 'callback' => 'canViewNavigation'),
            'Banners Management' => array('controller' => 'Banners', 'action' => '', 'callback' => 'canViewBanners'),
            'Home Page Banners Management' => array('controller' => 'homepageBanners', 'action' => '', 'callback' => 'canViewBanners'),
            'Content Pages' => array('controller' => 'cms', 'action' => '', 'callback' => 'canViewCms'),
            'Content Blocks' => array('controller' => 'blocks', 'action' => '', 'callback' => 'canViewBlock'),
            'Language Labels' => array('controller' => 'LabelManagement', 'action' => '', 'callback' => 'canViewLanguage'),
            'Offices' => array('controller' => 'offices', 'action' => '', 'callback' => 'canViewOffice'),
            'Cancellation Policy' => array('controller' => 'CancellationPolicies', 'action' => '', 'callback' => 'canViewCancellationPolicy'),
            'Testimonials' => array('controller' => 'testimonials', 'action' => '', 'callback' => 'canViewTestimonial'),
            'FAQs' => array('controller' => 'faqs', 'action' => '', 'callback' => 'canViewFaq'),
        )),
    'SETTINGS' => array('child' => array(
            'General Settings' => array('controller' => 'configurations', 'action' => '', 'callback' => 'canViewConfigurations'),
            'Payment Methods' => array('controller' => 'paymentMethods', 'action' => '', 'callback' => 'canViewPaymentMehods'),
            'Regions Management' => array('controller' => 'regions', 'action' => '', 'callback' => 'canViewRegion'),
            'Countries Management' => array('controller' => 'countries', 'action' => '', 'callback' => 'canViewLocation'),
            'Cities Management' => array('controller' => 'cities', 'action' => '', 'callback' => 'canViewCity'),
            'User Request Management' => array('controller' => 'userRequest', 'action' => '', 'callback' => 'canViewCurrency', 'id' => 'user-request-management-js'),
            'Currency Management' => array('controller' => 'currency', 'action' => '', 'callback' => 'canViewCurrency'),
            'Commision Management' => array('controller' => 'commissionChart', 'action' => '', 'callback' => 'canViewAdminCommission'),
            'Languages' => array('controller' => 'languages', 'action' => '', 'callback' => 'canViewLanguage'),
            'Email Templates' => array('controller' => 'email-template', 'action' => '', 'callback' => 'canViewEmailTemp'),
            'SMS Templates' => array('controller' => 'sms-template', 'action' => '', 'callback' => 'canViewSmsTemplate'),
            'Maintenance Mode Settings' => array('controller' => 'configurations', 'action' => 'maintenanceSettings', 'callback' => 'canViewConfigurations'),
        )),
    'WITHDRAWAL REQUESTS MANAGEMENT' => array('child' => array(
            'My Earnings' => array('controller' => 'wallet', 'action' => 'admin', 'callback' => 'canViewWallet'),
            'Manage Withdrawal Requests' => array('controller' => 'withdrawal-requests', 'action' => '', 'callback' => 'canViewWithdrawalRequest'),
        )),
    'BLOG MANAGEMENT' => array('child' => array(
            'Categories' => array('controller' => 'blogcategories', 'action' => '', 'callback' => 'canViewBlogCategory'),
            'Posts' => array('controller' => 'blogPosts', 'action' => '', 'callback' => 'canViewBlogPost'),
            'Comments' => array('controller' => 'blogcomments', 'action' => '', 'callback' => 'canViewBlogComment'),
        )),
    'REPORTS' => array('controller' => 'reports', 'action' => '', 'callback' => 'canViewReport'),
    'ADMIN USERS MANAGEMENT' => array('controller' => 'admin', 'action' => '', 'callback' => 'canViewAdmin'),
    'BACKUP' => array('controller' => 'SystemRestore', 'action' => '', 'callback' => 'canViewSystemRestore'),
    'NOTIFICATIONS' => array('controller' => 'notifications', 'action' => '', 'callback' => 'canViewNotification'),
);



return $header;
?>


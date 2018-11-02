<?php

$header =  array(
    'DASHBOARD' => array('controller' => '', 'action' => ''),
    'USERS MANAGEMENT' => array('child' => array(
            'Hosts Management' => array('controller' => 'host', 'action' => 'add', 'module' => 'campaigns'),
            'Travelers Management' => array('controller' => 'traveler', 'action' => '', 'module' => 'campaigns'),
        )),
    'ACTIVITY MANAGEMENT' => array('child' => array(
            'Manage Listing' => array('controller' => 'activities', 'action' => 'add', 'module' => 'campaigns'),
            'Manage Activity Abuses' => array('controller' => 'activityAbuses', 'action' => '', 'module' => 'campaigns'),
            'Manage Reviews' => array('controller' => 'reviews', 'action' => '', 'module' => 'campaigns'),
        )),
    'BOOKINGS MANAGEMENT' => array('child' => array(
            'Manage Bookings' => array('controller' => 'Orders', 'action' => 'add', 'module' => 'campaigns'),
            'Manage Booking Cancellation' => array('controller' => 'orderCancelRequests', 'action' => '', 'module' => 'campaigns'),
        )),
    'CMS' => array('child' => array(
            'Navigation Management' => array('controller' => 'navigation', 'action' => 'add', 'module' => 'campaigns'),
            'Banners Management' => array('controller' => 'Banners', 'action' => '', 'module' => 'campaigns'),
            'Content Pages' => array('controller' => 'cms', 'action' => '', 'module' => 'campaigns'),
            'Content Blocks' => array('controller' => 'blocks', 'action' => '', 'module' => 'campaigns'),
            'Language Labels' => array('controller' => 'LabelManagement', 'action' => '', 'module' => 'campaigns'),
            'Services' => array('controller' => 'service', 'action' => '', 'module' => 'campaigns'),
            'Offices' => array('controller' => 'offices', 'action' => '', 'module' => 'campaigns'),
            'Cancellation Policy' => array('controller' => 'CancellationPolicies', 'action' => '', 'module' => 'campaigns'),
            'Testimonials' => array('controller' => 'Banners', 'action' => '', 'module' => 'campaigns'),
            'FAQs' => array('controller' => 'faqs', 'action' => '', 'module' => 'campaigns'),
        )),
    'SETTINGS' => array('child' => array(
            'General Settings' => array('controller' => 'configurations', 'action' => 'add', 'module' => 'campaigns'),
            'Payment Methods' => array('controller' => 'paymentMethods', 'action' => '', 'module' => 'campaigns'),
            'Regions Management' => array('controller' => 'regions', 'action' => '', 'module' => 'campaigns'),
            'Countries Management' => array('controller' => 'countries', 'action' => '', 'module' => 'campaigns'),
            'Cities Management' => array('controller' => 'cities', 'action' => '', 'module' => 'campaigns'),
            'Location Requests Management' => array('controller' => 'userRequest', 'action' => '', 'module' => 'campaigns'),
            'Currency Management' => array('controller' => 'currency', 'action' => '', 'module' => 'campaigns'),
            'Languages' => array('controller' => 'languages', 'action' => '', 'module' => 'campaigns'),
            'Email Templates' => array('controller' => 'email-template', 'action' => '', 'module' => 'campaigns'),
            'SMS Templates' => array('controller' => 'sms-template', 'action' => '', 'module' => 'campaigns'),
            'Maintenance Mode Settings' => array('controller' => 'configurations', 'action' => 'maintenanceSettings', 'module' => 'campaigns'),
        )),
    
        'WITHDRAWAL REQUESTS MANAGEMENT' => array('child' => array(
            'Wallet' => array('controller' => 'wallet', 'action' => 'admin', 'module' => 'campaigns'),
            'Manage Withdrawal Requests' => array('controller' => 'withdrawal-requests', 'action' => '', 'module' => 'campaigns'),
        )),
        'BLOG MANAGEMENT' => array('child' => array(
            'Categories' => array('controller' => 'blogcategories', 'action' => 'admin', 'module' => 'campaigns'),
            'Posts' => array('controller' => 'blogPosts', 'action' => '', 'module' => 'campaigns'),
            'Comments' => array('controller' => 'blogcomments', 'action' => '', 'module' => 'campaigns'),
        )),
    'REPORTS' => array('controller' => 'reports', 'action' => ''),
    'ADMIN USERS MANAGEMENT' => array('controller' => 'admin', 'action' => ''),
    'BACKUP' => array('controller' => 'SystemRestore', 'action' => ''),
    'NOTIFICATIONS' => array('controller' => 'notifications', 'action' => ''),
);



return $header;
?>


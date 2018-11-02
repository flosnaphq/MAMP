<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>

    <div class="fixed_container">
        <div class="row">
            <div class="col-sm-12">  
                <h1>Permissions</h1>   	
                <section class="section">
                    <div class="sectionhead">
                        <h4>Permissions for Admin User: <span style="text-transform: none;"><?php echo $admin_detail; ?></span></h4>
                    </div>


                    <div class="sectionbody space">
                        <?php
                        $frmPermissions->setValidatorJsObjectName('loginValidator');
                        $frmPermissions->setFormTagAttribute('class', 'web_form ');
                        $frmPermissions->setFormTagAttribute('action', FatUtility::generateUrl("admin", "permission_action", array('admin_id' => $admin_id)));
                        $frmPermissions->developerTags['fld_default_col'] = 4;
                        echo $frmPermissions->getFormHtml();
                        ?>
                    </div>
                </section>  
            </div>
        </div>
    </div>





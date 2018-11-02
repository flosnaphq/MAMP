
<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<div class="tabs_nav_container responsive flat">
    <ul class="tabs_nav">
        <li><a class="active" rel="tabs_01" href="javascript:;"> CMS</a></li>

        <li><a rel="tabs_02" href="javascript:;">SEO</a></li>

    </ul>
    <div class="tabs_panel_wrap">
        <span class="togglehead active" rel="tabs_01">CMS </span>
        <div id="tabs_01" class="tabs_panel">
            <?php
            $frm->setValidatorJsObjectName('formValidator');
            $frm->setRequiredStarWith(FORM::FORM_REQUIRED_STAR_POSITION_NONE);
            $frm->setFormTagAttribute('class', 'web_form');
            $frm->developerTags['fld_default_col'] = 12;
            $cms_name = $frm->getField('cms_name');
            $cms_name->setFieldTagAttribute('onblur', 'getSlug(this.value)');
            echo $frm->getFormHtml();
            ?>	
        </div>
        <!--tab2 start here-->
        <span class="togglehead" rel="tabs_02">SEO </span>
        <div id="tabs_02" class="tabs_panel">
            <?php
            $tag_form->setValidatorJsObjectName('metaTagValidator');

            $tag_form->setFormTagAttribute('class', 'web_form');
            $tag_form->developerTags['fld_default_col'] = 12;
            $tag_form->setRequiredStarWith(FORM::FORM_REQUIRED_STAR_POSITION_NONE);
            $tag_form->setFormTagAttribute('action', FatUtility::generateUrl("cms", "meta-tag-action"));
            $tag_form->setFormTagAttribute('onsubmit', 'submitMetaTagForm(metaTagValidator,"meta_form"); return(false);');
            echo $tag_form->getFormHtml();
            ?>	
        </div>
    </div>     

</div> 
<script>

    $(".tabs_panel").hide();
    $('.tabs_panel_wrap').find(".tabs_panel:first").show();
    $(".tabs_nav li a").click(function () {
        $(this).parents('.tabs_nav_container:first').find(".tabs_panel").hide();
        var activeTab = $(this).attr("rel");
        $("#" + activeTab).fadeIn();

        $(this).parents('.tabs_nav_container:first').find(".tabs_nav li a").removeClass("active");
        $(this).addClass("active");

        $(".togglehead").removeClass("active");
        $(".togglehead[rel^='" + activeTab + "']").addClass("active");

    });

</script>




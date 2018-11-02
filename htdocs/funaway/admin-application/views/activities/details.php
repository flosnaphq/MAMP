<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<link href='https://api.mapbox.com/mapbox.js/v2.4.0/mapbox.css' rel='stylesheet' />
<script src='https://api.mapbox.com/mapbox.js/v2.4.0/mapbox.js'></script>

    <div class="fixed_container">
        <div class="row">



            <div class="col-sm-12">  
                <h1>Activity Details</h1>   

<!--<section class="section searchform_filter">-->
                <!-- <div class="sectionhead ">-->
                <div class="tabs_nav_container responsive flat">

                    <ul class="tabs_nav">
                        <li><a class="active" rel="tabs_01" href="javascript:;"  onclick="tab(1, this)" ><?php echo Info::t_lang('BASIC_INFORMATION'); ?></a></li>
                        <li><a rel="tabs_02" href="javascript:;"  onclick="tab(2, this, ' (<i>Activity images: 1600X900 or [aspect ratio 4:3]</i>)')"><?php echo Info::t_lang('PHOTOS'); ?></a></li>
                        <li><a rel="tabs_03" href="javascript:;"  onclick="tab(3, this)"><?php echo Info::t_lang('VIDEOS'); ?></a></li>
                        <li><a rel="tabs_04" href="javascript:;"  onclick="tab(4, this)"><?php echo Info::t_lang('ACTIVITY_BRIEF') ?></a></li>
                        <li><a rel="tabs_05" href="javascript:;"  onclick="tab(5, this)"><?php echo Info::t_lang('MAP'); ?></a></li>
                        <li><a rel="tabs_06" href="javascript:;"  onclick="tab(6, this)"><?php echo Info::t_lang('AVAILABILITY'); ?></a></li>
                        <li><a rel="tabs_07" href="javascript:;"  onclick="tab(7, this)"><?php echo Info::t_lang('ADDONS'); ?></a></li>
                        <?php if ($canViewReview) { ?>
                            <li><a rel="tabs_08" href="javascript:;"  onclick="tab(8, this)"><?php echo Info::t_lang('REVIEWS'); ?></a></li>
                        <?php } ?>

                    </ul>

                    <div class="tabs_panel_wrap">


                        <span class="togglehead active" rel="tabs_01"><?php echo Info::t_lang('BASIC_INFORMATION'); ?> </span>
                        <div id="tabs_01" class="tabs_panel">
                        </div>

                        <span class="togglehead active" rel="tabs_02"><?php echo Info::t_lang('PHOTOS'); ?></span>
                        <div id="tabs_02" class="tabs_panel">
                        </div>

                        <span class="togglehead active" rel="tabs_03"><?php echo Info::t_lang('VIDEOS'); ?> </span>
                        <div id="tabs_03" class="tabs_panel">
                        </div>

                        <span class="togglehead active" rel="tabs_04"><?php echo Info::t_lang('ACTIVITY_BRIEF') ?> </span>
                        <div id="tabs_04" class="tabs_panel">
                        </div>

                        <span class="togglehead active" rel="tabs_05"><?php echo Info::t_lang('MAP'); ?> </span>
                        <div id="tabs_05" class="tabs_panel">
                        </div>

                        <span class="togglehead" rel="tabs_06"><?php echo Info::t_lang('AVAILABILITY'); ?> </span>
                        <div id="tabs_06" class="tabs_panel">

                        </div>
                        <span class="togglehead" rel="tabs_07"><?php echo Info::t_lang('ADDONS'); ?> </span>
                        <div id="tabs_07" class="tabs_panel">

                        </div>
                        <span class="togglehead" rel="tabs_08"><?php echo Info::t_lang('REVIEWS'); ?> </span>
                        <div id="tabs_08" class="tabs_panel">

                        </div>



                    </div>      

                </div> 



                <!--</div>-->
                <div class="sectionbody togglewrap space" style="overflow: hidden; display: none;">


                </div>
                <!--</section>-->


                <div id = "form-tab" >

                </div>



                <section class="section">
                    <div class="sectionhead">
                        <h4 id="tab-heading">Basic Information</h4>
                        <a href="javascript:;clearSearch()" id="clearSearch" style="display:none" class="themebtn btn-default btn-sm">Clear search</a>
                        <a href="javascript:;" onclick="getAddOnForm();
                                return;"  id="new-add-on" class="themebtn btn-default btn-sm">
                            Add New
                        </a>
                        <?php if ($canEditReview) { ?>
                            <a href="javascript:;" onclick="getReviewForm();
                                    return;"  id="new-review" class="themebtn btn-default btn-sm">
                                Add New
                            </a>
                        <?php } ?>
                    </div>
                    <div class="sectionbody">

                        <div id="listing">
                            processing....
                        </div>		
                    </div>
                </section>  
            </div> 
        </div>
    </div>

<script>
    var activity_id = '<?php echo $activity_id; ?>';
    var mapbox_access_token = '<?php echo FatApp::getConfig('mapbox_access_token') ?>';
	
	$(document).ready(function() {
		/* $("select[name='activity_region_id']").trigger('change'); */
	});
</script>   								
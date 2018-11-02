<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<style>
    .listing-ul li{
        display:block;

    }
    .listing-ul li .svg{ width:20px; height:20px; display:inline-block;}
</style>

<div class="fixed_container">
    <div class="row">
        <div class="col-sm-12">  
            <h1>Admins</h1>   









            <section class="section searchform_filter">
                <div class="sectionhead">
                    <h4>Search</h4>


                </div>
                <div class="sectionbody togglewrap space" style="overflow: hidden; display: none;">

                    <?php
                    $search->setFormTagAttribute('onsubmit', 'searchForm(this); return(false);');
                    $search->setFormTagAttribute('class', 'web_form');
                    $search->developerTags['fld_default_col'] = 3;
                    echo $search->getFormHtml();
                    ?>	
                </div>
            </section>


            <div id = "form-tab">
            </div>



            <section class="section">
                <div class="sectionhead">
                    <h4>Admins</h4>

                    <?php if ($canEdit) { ?>
                        <a href="javascript:;" onclick="getForm();
                                            return;"  class="themebtn btn-default btn-sm">
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


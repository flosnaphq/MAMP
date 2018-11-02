<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>

<div class="fixed_container">
    <div class="row">



        <div class="col-sm-12">  
            <h1>Languages</h1>   



            <div id = "form-tab">
            </div>



            <section class="section">
                <div class="sectionhead">
                    <h4>Languages</h4>

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
  

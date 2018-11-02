<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<script type="text/javascript">
    var paginateController = "<?php echo $pageController;?>";
</script>    
<div class="page">
    <div class="fixed_container">
        <div class="row">
            <div class="col-sm-12">  
           
                <h1><?php echo $pageTitle; ?></h1>   
                <?php if($paginateSearch):?>
                <section class="section searchform_filter">
                    <div class="sectionhead ">
                        <h4>Search</h4>
                    </div>
                    <div class="sectionbody togglewrap space" style="overflow: hidden; display: none;">
                        <?php echo $search->getFormHtml(); ?>
                    </div>
                </section
                <?php endif;?>
                <div id = "form-tab" > </div>
                <section class="section">
                    <div class="sectionhead">
                        <h4><?php echo $pageTitle; ?></h4>
                        <?php echo html_entity_decode($pageExtra); ?>
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
</div>  

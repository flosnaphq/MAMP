<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

$InfoUrl = FatUtility::generateUrl('countries', 'info', array($countryId));
$galleryUrl = FatUtility::generateUrl('countries', 'imageForm', array($countryId));
$addNewCountry = FatUtility::generateUrl('countries', 'setup');
$metaUrl = FatUtility::generateUrl('countries', 'getMetaForm', array($countryId));
$countries = FatUtility::generateUrl('countries');
?>

<div class="fixed_container">
    <div class="row">
        <div class="col-sm-12">  
            <h1>Country</h1>   
            <section class="section">
                <div class="sectionhead">
                    <h4>Country</h4>
                    <a href="javascript:;clearSearch()" id="clearSearch" style="display:none" class="themebtn btn-default btn-sm">Clear search</a>
                     <a href="<?php echo $countries;?>"  class="themebtn btn-default btn-sm">
                               Back
                            </a> 
                    <?php if ($canEdit) { ?>
                        <a href="<?php echo $addNewCountry; ?>"   class="themebtn btn-default btn-sm">
                            Add New
                        </a> 
                    <?php } ?>
                </div>
                <div class="sectionbody">
                    <ul class="sidetabs normaltabs nmltabs">
                        <li><a href="#info" onclick="jQuery.fn.tabLoader(1, this);" data-href="<?php echo $InfoUrl ?>">Country</a></li>
                        <?php if ($countryId): ?>
                            <li><a href="#images" onclick="jQuery.fn.tabLoader(2, this);" data-href="<?php echo $galleryUrl ?>">Images</a></li>
                        <?php endif; ?>
                    </ul>
                    <div class="tab_container tab--loader">
                        <div id="tab-1" class="tab_content"> </div>
                        <?php if ($countryId): ?>
                            <div id="tab-2" class="tab_content"> </div>
                            <div id="tab-3" class="tab_content"> </div>
                        <?php endif; ?>
                    </div>				
                </div>
            </section>  
        </div> 
    </div>
</div>


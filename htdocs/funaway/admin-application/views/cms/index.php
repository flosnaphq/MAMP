<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>

<div class="fixed_container">
    <div class="row">
        <div class="col-sm-12">  
            <h1>CMS</h1>   
            <section class="section searchform_filter">
                <div class="sectionhead ">
                    <h4>Search</h4>
                </div>
                <div class="sectionbody togglewrap space" style="overflow: hidden; display: none;">
                    <?php
                    $search->setFormTagAttribute('onsubmit', 'search(this); return(false);');
                    $search->setFormTagAttribute('class', 'web_form');
                    $search->developerTags['fld_default_col'] = 6;
                    echo $search->getFormHtml();
                    ?>	
                </div>
            </section>
            <div id = "form-tab" >
            </div>
            <section class="section">
                <div class="sectionhead">
                    <h4>CMS</h4>
                    <a href="javascript:;clearSearch()" id="clearSearch" style="display:none" class="themebtn btn-default btn-sm">Clear search</a>
                    <?php
                    if ($canEdit) {
                        ?>
                        <a href="javascript:;" onclick="getForm();
                                                                    return;"  class="themebtn btn-default btn-sm">
                            Add New
                        </a>
                        <?php
                        if (count($fatShortCodes) > 0) {
                            ?>
                            <a href="javascript:void(0);" onclick="getShortCodeList();
                                                                                return(false);"  class="themebtn btn-default btn-sm">
                                Shortcodes List
                            </a>
        <?php
    }
}
?>
                </div>
                <div class="sectionbody">
                    <div id="listing">
                        processing....
                    </div>		
                </div>

                <?php
                $str = '';
                if (count($fatShortCodes) > 0) {
                    $str .= '<div id="fat-shortcodes" style="display:none">';
                    $str .= '<table class="table table-responsive" width="100%">';

                    $str .= '<tr><th colspan="3">Available shortcodes that you can use in your cms pages. </th></tr>';
                    $str .= '<tr>';
                    $str .= '<th width="5%">#</th>';
                    $str .= '<th width="50%">ShortCode</th>';
                    $str .= '<th width="45%">Description</th>';
                    $str .= '</tr>';
                    $i = 1;
                    foreach ($fatShortCodes as $key => $val) {
                        $str .= '<tr>';
                        $str .= '<td>' . $i . '</td>';
                        $str .= '<td>' . $key . '</td>';
                        $str .= '<td>' . $val . '</td>';
                        $str .= '</tr>';
                        $i++;
                    }
                    $str .= '</table>';
                    $str .= '</div>';
                }
                echo $str;
                ?>
        </div>
        </section>  
    </div> 
</div>
</div>


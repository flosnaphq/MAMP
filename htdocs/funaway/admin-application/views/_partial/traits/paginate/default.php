<?php
defined('SYSTEM_INIT') or die('Invalid Usage');



echo $table->getHtml();


if ($totalPage > 1) {
    ?>
    <div class="footinfo">
        <aside class="grid_1">
            <ul class="pagination">
                <?php
                echo FatUtility::getPageString('<li><a href="javascript:void(0);" onclick="listing(xxpagexx);">xxpagexx</a></li>', $totalPage, $page, $lnkcurrent = '<li class="selected"><a href="javascript:void(0);" >xxpagexx</a></li>', '  ', 5, '<li class="more"> <a href="javascript:void(0);" onclick="listing(xxpagexx);"><span class="ink animate" style="height: 35px; width: 35px; top: 0.5px; left: 4px;"></span></a></li>', ' <li class="more"><a href="javascript:void(0);" onclick="listing(xxpagexx);"><span class="ink animate" style="height: 35px; width: 35px; top: 0.5px; left: 4px;"></span></a></li>', '<li class="prev"> <a href="javascript:void(0);" onclick="listing(xxpagexx);"></a></li>', '<li class="next"> <a href="javascript:void(0);" onclick="listing(xxpagexx);"></a></li>');
                ?>
            </ul>
        </aside>  

    </div>
    <?php
}
?>


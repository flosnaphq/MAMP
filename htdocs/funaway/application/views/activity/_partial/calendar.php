<?php /*'<h6 class="book-card__heading"><?php echo Info::t_lang('BOOK_YOUR_SEAT') ?></h6>' */?>
<style type="text/css">
    .calendar__actions a{padding: 2px;}
</style>
<div class="book-card__dates">
    <div class="book-card__dates-start">
        <label></label>
        <span></span>
        <label></label>
    </div>
    <div class="book-card__dates-end">
        <label></label>
        <span></span>
        <label></label>
    </div>
</div>
<div class="book-card__calendar calendar">
    <div style="display:none;">
        <a rel='<?php echo $year ?>' id='cal-year'></a><a rel='<?php echo $month ?>' id='cal-month'></a>
    </div>
    <header class="calendar__actions">
        <?php if ($next) { ?>
            <a href="javascript:;" onclick = 'nextMonth(<?php echo $year ?>,<?php echo $month ?>)' class="fl--right"> <?php echo Info::t_lang("NEXT"); ?> </a> 
        <?php } ?>
        <?php if ($prev) { ?>
            <a href="javascript:;"  onclick = 'prevMonth(<?php echo $year ?>,<?php echo $month ?>)' class="fl--right"><?php echo Info::t_lang("PREV"); ?> | </a>
        <?php } ?>
        <h6 class="calendar__heading text--left"><?php echo $year; ?> <?php echo $showmonth; ?></h6>
    </header>
    <div class="calendar__dates">
        <table class="">
            <thead class="calendar__dates__header">
                <tr>
                    <th><span><?php echo Info::t_lang('S') ?></span></th>
                    <th><span><?php echo Info::t_lang('M') ?></span></th>
                    <th><span><?php echo Info::t_lang('T') ?></span></th>
                    <th><span><?php echo Info::t_lang('W') ?></span></th>
                    <th><span><?php echo Info::t_lang('T') ?></span></th>
                    <th><span><?php echo Info::t_lang('F') ?></span></th>
                    <th><span><?php echo Info::t_lang('S') ?></span></th>

                </tr>
            </thead>
            <tbody class="calendar__dates__body">
                <?php
                foreach ($calendar as $k => $cal) {
                    if ($k % 7 == 0) {
                        echo '<tr>';
                    }
                    ?>
                <td class="<?php echo $cal['class'] ?>">
                    <span class = " <?php echo $cal['subclass'] ?>"><?php echo $cal['date'] ?></span>
                </td>			
                <?php
                if ($k % 7 == 6) {
                    echo '</tr>';
                }
            }
            ?>
            </tbody>
        </table>
    </div>
    <?php /*<span style="color:#0599b2;">*/?>
    <?php
    /* $sysDateFormat = FatApp::getConfig('conf_date_format_php', FATUtility::VAR_STRING, 'Y-m-d');
      $sysTimeFormat = FatApp::getConfig('conf_date_format_time', FATUtility::VAR_STRING, 'H:i');
      echo sprintf(Info::t_lang('Current_website_time'), FatDate::nowInTimezone(FatApp::getConfig('conf_timezone'), $sysTimeFormat . ', ' . $sysDateFormat)); */

    echo Info::t_lang('Current_website_time');
    echo '<br>';
    echo Info::sysCurrentDateTime(null, true, null, true);
    ?>
    <?php /*</span>*/?>
</div>
<div class="book-card__event" id='book-card-event'></div>
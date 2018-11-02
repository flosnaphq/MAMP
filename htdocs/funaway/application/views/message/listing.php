<?php
//var_dump($arr_listing);
if (!empty($arr_listing)) {
    ?>

    <table class="table table--fixed table--bordered table--responsive messages-table">
        <tbody>
            <?php
            foreach ($arr_listing as $msg) {
                if (empty($msg['user_id'])) {
                    $user_name = "admin";
                } else {
                    $user_name = $msg['user_firstname'] . " " . $msg['user_lastname'];
                }
                $class = "";
                if($msg['message_user_id']!==$user_id &&  $msg["message_seen"]==0){
                    $class = "striped";
                }
                
                $message_time = FatDate::format($msg['message_date'], true);
                $activity_name = $msg['activity_name'];
                ?>
                <tr class="message <?php echo $class;?>">
                    <td class="message__details " data-label="Details">
                        <h6 class="message__heading"><?php echo $user_name; ?></h6>
                        <?php if(!empty($activity_name)):?>
                        <div class="message-activity-name"><?php echo $activity_name; ?></div>
                        <?php endif;?>
                        <span class="message-time"><?php echo $message_time; ?></span>
                                <div class="more-less">
                                    <div class="more-block">
                                        <p class="regular-text">
        <?php echo nl2br($msg['message_text']) ?>
                                        </p>

                                    </div>
                                </div>
                                </td>
                                <td class="message__actions" data-label="Action">
                                    <nav class="buttons__group" role="navigation">
                                        <a href="<?php echo FatUtility::generateUrl('message', 'view', array($msg['message_thread_id'])) ?>"  class="button button--square button--fill button--dark button--small"><svg class="icon icon--search"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-view"></use></svg></a>
                                        <a href="#reply-msg" onclick="replyForm(<?php echo $msg['message_thread_id'] ?>)" class="reply-msg button button--square button--fill button--blue button--small"><svg class="icon icon--search"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-reply"></use></svg></a>
                                    </nav>
                                </td>
                                </tr>

                                <?php } ?>

                                </tbody>
                                </table>				


                                <?php
                                if ($totalPage > 1) {
                                    ?>


                                    <nav class="pagination text--center">
                                        <ul class="list list--horizontal no--margin-bottom">      
                                            <?php
                                            echo FatUtility::getPageString('<li><a href="javascript:void(0);" onclick="listing(xxpagexx);">xxpagexx</a></li>', $totalPage, $page, $lnkcurrent = '<li class="selected"><a href="javascript:void(0);" >xxpagexx</a></li>', '  ', 5, '<li class="more"> <a href="javascript:void(0);" onclick="listing(xxpagexx);"><span class="ink animate" style="height: 35px; width: 35px; top: 0.5px; left: 4px;"></span></a></li>', ' <li class="more"><a href="javascript:void(0);" onclick="listing(xxpagexx);"><span class="ink animate" style="height: 35px; width: 35px; top: 0.5px; left: 4px;"></span></a></li>', '<li class="prev"> <a href="javascript:void(0);" onclick="listing(xxpagexx);"></a></li>', '<li class="next"> <a href="javascript:void(0);" onclick="listing(xxpagexx);"></a></li>');
                                            ?>
                                        </ul>
                                    </nav>

                                            <?php
                                        }
                                    } else {
                                        echo Helper::noRecord(Info::t_lang('NO_MESSAGE'));
                                    }
                                    ?>
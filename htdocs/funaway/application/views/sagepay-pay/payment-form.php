<?php defined('SYSTEM_INIT') or die('Invalid Usage'); ?>
<div class="payment-page">
    <div class="cc-payment">
        <div class="payment-from" id="payment">
            <?php
            if (!isset($error)) {
                ?>
                <h6 class="block__heading-text summary__total" ><span><?= Info::t_lang('Your_Billing_Address') ?></span></h6>  

                <?php
                echo $frm->getFormHtml();
            } else {
                ?>
                <div class="alert alert-danger"><?php echo $error ?><div>
                        <?php
                    }
                    ?>
                    <div id="ajax_message"></div>
                </div>
            </div>
        </div>
        <script type="text/javascript">

            $(document).ready(function () {

                $('#button-confirm').on('click', function () {
                    fcom.ajax(fcom.makeUrl('sagepay-pay', 'send'), fcom.frmData(document.frmSagepay), function (json) {
                        json = $.parseJSON(json);

                        // if success
                        if (json['redirect']) {
                            html = '<form action="' + json['redirect'] + '" method="post" id="redirect">';
                            html += '  <input type="hidden" name="Status" value="' + json['Status'] + '" />';
                            html += '  <input type="hidden" name="StatusDetail" value="' + json['StatusDetail'] + '" />';
                            html += '</form>';
                            $('#payment').after(html);
                            $('#redirect').submit();
                        }// if error
                        if (json['error']) {
                            $('#payment').before('<div id="sagepay_message_error" class="alert alert-warning"><i class="fa fa-info-circle"></i> ' + json['error'] + '</div>');
                        }

                        /*if ("1" == json.status) {
                         $("#listing").html(json.msg);
                         $('#clearSearch').show();
                         jsonSuccessMessage("List Updated.")
                         } else {
                         jsonErrorMessage("something went wrong.")
                         }*/
                    });


                    return false;
                    $.ajax({
                        url: 'index.php?route=payment/sagepay_server_v3/send',
                        type: 'post',
                        data: $('#card-existing :input:checked, #card-save :input:enabled, #payment select:enabled'),
                        dataType: 'json',
                        cache: false,
                        beforeSend: function () {
                            $('#button-confirm').button('loading');
                        },
                        complete: function () {
                            $('#button-confirm').button('reset');
                        },
                        success: function (json) {
                            // if success
                            if (json['redirect']) {
                                html = '<form action="' + json['redirect'] + '" method="post" id="redirect">';
                                html += '  <input type="hidden" name="Status" value="' + json['Status'] + '" />';
                                html += '  <input type="hidden" name="StatusDetail" value="' + json['StatusDetail'] + '" />';
                                html += '</form>';
                                $('#payment').after(html);
                                $('#redirect').submit();
                            }// if error
                            if (json['error']) {
                                $('#payment').before('<div id="sagepay_message_error" class="alert alert-warning"><i class="fa fa-info-circle"></i> ' + json['error'] + '</div>');
                            }
                        }
                    });
                });
            });
        </script>
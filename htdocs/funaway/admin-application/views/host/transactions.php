
<div class="fixed_container">
    <div class="row">



        <div class="col-sm-12">  

            <h1>My Profile</h1> 
            <div class="containerwhite">
                <aside class="grid_1 profile">
                    <div id="profile-section">

                    </div>
                </aside>  
                <div class="message-box">
                    <aside class="grid_2 thread-listing">

                        <?php require_once("_partial/heading.php") ?>
                        <section class="section">
                            <div class="sectionhead">
                                <h4>Wallet Balance</h4>
                                <?php if ($canViewWallet) { ?>
                                    <a class="themebtn btn-default btn-sm" onclick="getForm();
                                                                                    return;" href="javascript:;">
                                        Update Wallet
                                    </a>
                                <?php } ?>
                            </div>


                            <div class="sectionbody">
                                <div id="wallet-record"><table width="100%" class="table table-responsive">
                                        <thead>
                                            <tr>
                                                <th>Total Balance</th>
                                                <th>Credit Balance</th>
                                                <th>Debit Balance</th>
                                            </tr>
                                            <tr>
                                                <td><?php echo Currency::displayPrice($wallet['wallet_balance']); ?></td>
                                                <td><?php echo Currency::displayPrice($wallet['credit_amount']); ?></td>
                                                <td><?php echo Currency::displayPrice(abs($wallet['debit_amount'])); ?></td>
                                            </tr>
                                        </thead>

                                    </table></div>		
                            </div>
                        </section>
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
                        <div class = "message-list" id="listing"> 
                        </div>
                    </aside>  
                </div>
            </div>
        </div> 


    </div>
</div>



<script>
    user_id = <?php echo $user_id ?>;
</script>
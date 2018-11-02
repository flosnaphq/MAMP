<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>

            <div class="fixed_container">
                <div class="row">
					<div class="col-sm-12">
						<h1>Navigation Management</h1>
						<div class="containerwhite">
							<aside class="grid_1">
								<div class="contactarea">
									<h3>CMS PAGES </h3>
									<ul class="contactlist">
											<?php  foreach($links as $lnk) { ?>
												<li><?php echo $lnk['cms_name']?>
                                                                                                  <?php  if($canEdit){ ?>
                                                                                                    <a style="float:right" href = "javascript:;" onclick = "addInListing(<?php echo $lnk['cms_id']?>)">Add</a></li>
                                                                                                 <?php }?>
											<?php } ?>
									 </ul>

									<h3>Others </h3>
									<ul class="contactlist">
										<?php  foreach($navigations as $k=>$lnk) { ?>

											<li><?php echo $lnk['name'] ?>
                                                                                              <?php  if($canEdit){ ?>
                                                                                            <a style="float:right" href = "javascript:;" onclick = "addOtherInListing(<?php echo $k?>)">Add</a></li>
                                                                        	<?php } ?>
										<?php } ?>
									</ul>
								</div>
							</aside>
							<aside class="grid_2">
								<ul class="centered_nav">
									<?php
									$options = Info::getCmsPositions();
									foreach($options as $k=>$v){ ?>
									<li>
										<a href="javascript:;" onclick = "showListing(<?php echo $k?>)" id = "navsec-<?php echo $k?>" class = "nav-section" >
											<?php echo $v?>
										</a>
									</li>
									<?php } ?>
								</ul>

								<div class="areabody">
									<div class="formhorizontal">
										<div class="repeatedrow" id = "list-section">
										</div>
									</div>
								</div>
							</aside>
						</div>
					</div>
                </div>
            </div>

        <!--main panel end here-->
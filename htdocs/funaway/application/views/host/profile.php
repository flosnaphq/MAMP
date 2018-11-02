<?php 
defined('SYSTEM_INIT') or die('Invalid Usage.'); 
?>
<main id="MAIN" class="site-main   with--sidebar">
            <div class="site-main__body">
                 <?php require_once(CONF_THEME_PATH.'host/common/profile-menu.php')?>
               <section class="section">
                        <div class="container container--static">
                    <header class="section__header section-header--bordered">
                        <h6 class="header__heading-text "><?php echo Info::t_lang('EDIT_PROFILE')?></h6>
                    </header>
                    <div class="section__body">
                        <div class="container container--fluid">
                            <div class="span__row">
                                <div class="span span--3" >
                                    <?php require_once(CONF_THEME_PATH.'host/common/profile-left.php')?>
                                </div>
                                <div  class="span span--9 span-offset--1" id="form-wrapper">                                  
                                </div>
                            </div>
                        </div>
                    </div>
                       </div>
                </section>
            </div>
        </main>

<div id="phone-instr" style="display:none">
	<div class="modal">
		
		
		<div class="modal__footer">
			<div class="regular-text innova-editor">
				<?php echo Info::t_lang('PLEASE_PROVIDE_A_VALID_PHONE_NUMBER_VIA_SMS');?>
			</div>
		</div>
	</div>
</div>		
<div id="second-email-instruction" style="display:none">
	<div class="modal">
		
		
		<div class="modal__footer">
			<div class="regular-text innova-editor">
				<?php echo Info::t_lang('SECOND_EMAILS_WHENEVER_THERE_IS_A_BOOKING');?>
			</div>
		</div>
	</div>
</div>
<?php if(!empty($introduce_yourself)){ ?>
<div id="introduce-yourself-instruction" style="display:none">
	<div class="modal">
		<div class="modal__header">
			<h6 class="modal__heading"><?php echo $introduce_yourself['block_title'];?></h6>
		</div>
		
		<div class="modal__footer">
			<div class="regular-text innova-editor">
				<?php echo html_entity_decode($introduce_yourself['block_content']);?>
			</div>
		</div>
	</div>
</div>
<?php } ?>
<main id="MAIN" class="site-main  with--sidebar">
    <div class="site-main__body">
        <?php require_once(CONF_THEME_PATH.'traveler/common/order-menu.php')?>	
        <section class="section  no--padding">
    	    <div class="container container--static">
        		<header class="section__header section-header--bordered">
        			<h6 class="header__heading-text fl--left"><?php echo Info::t_lang('MY_BOOKINGS')?></h6>
        		</header>
    	    </div>
            <div class="container container--static" id="listing">
                <div class="section__body">
                    <div class="container container--fluid">
                        <div class="span__row">
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
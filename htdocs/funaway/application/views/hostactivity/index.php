<main id="MAIN" class="site-main  with--sidebar">
    <div class="site-main__body">

        <?php require_once(CONF_THEME_PATH . 'hostactivity/common/right-menu.php'); ?> 


        <section class="section">
            <div class="container container--static">
                <header class="section__header section-header--bordered">
                    <h6 class="header__heading-text fl--left"><?php echo Info::t_lang('ACTIVITIES'); ?></h6>
                    <nav class="filters fl--right" role="navigation">

                        <ul class="list list--horizontal">
                            <li>
                                <select id="confirm_status" name="confirm_status" onchange="listing()">
                                    <option value='-1'><?php echo Info::t_lang('ALL') ?></option>
                                    <?php
                                    if (!empty($confirm_status)) {
                                        foreach ($confirm_status as $status_key => $status_name) {
                                            ?>
                                            <option value="<?php echo $status_key; ?>"><?php echo $status_name; ?></option>
                                            <?php
                                        }
                                    }
                                    ?>

                                </select>
                            </li>
                            <li>
                                <select id="status" name="status" onchange="listing()" >
                                    <option value='-1'><?php echo Info::t_lang('ALL') ?></option>
                                    <?php
                                    if (!empty($status)) {
                                        foreach ($status as $status_key => $status_name) {
                                            ?>
                                            <option value="<?php echo $status_key; ?>"><?php echo $status_name; ?></option>
                                            <?php
                                        }
                                    }
                                    ?>

                                </select>
                            </li>


                        </ul>
                    </nav>
                </header>
                <div class="section__body">
                    <div class="container container--fluid">
                        <div class="span__row">
                            <div class="span span--12 activity-list">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
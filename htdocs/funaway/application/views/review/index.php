<main id="MAIN" class="site-main  with--sidebar">
    <div class="site-main__body">
        <?php require_once(dirname(dirname(__FILE__)) . '/_partial/sub-header.php') ?>
        <section class="section">
            <div class="container container--static">
                <header class="section__header section-header--bordered">
                    <div class="container container--fluid container--flex">
                        <h6 class="header__heading-text fl--left"><?php echo Info::t_lang('Reviews') ?></h6>
                        <nav class="filters fl--right" role="navigation">
                            <ul class="list list--horizontal">

                                <li>
                                    <label class="select" for="status">
                                        <?php if (!empty($activities)) { ?>
                                            <select id="activity" onchange="listing(1, this.value)">
                                                <option value=""><?php echo Info::t_lang('ALL_ACTIVITIES'); ?></option>
                                                <?php foreach ($activities as $act_id => $act_name) { ?>
                                                    <option value="<?php echo $act_id ?>"><?php echo $act_name; ?></option>
                                                <?php } ?>
                                            </select>
                                        <?php } ?>
                                    </label>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </header>
                <div class="section__body">
                    <div class="container container--fluid">
                        <div class="span__row">
                            <div class="span span--12 review-list">

                            </div>
                            <div style="display:none;" id='abuse-review'>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
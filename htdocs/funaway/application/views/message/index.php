<main id="MAIN" class="site-main  with--sidebar">
    <div class="site-main__body">
        <?php require_once(dirname(dirname(__FILE__)) . '/_partial/sub-header.php') ?>
        <section class="section">
            <div class="container container--static">
                <header class="section__header section-header--bordered">
                    <div class="container container--fluid container--flex">
                        <h6 class="header__heading-text fl--left"><?php echo Info::t_lang('MESSAGES') ?></h6>
                        <nav class="filters fl--right" role="navigation">
                            <ul class="list list--horizontal">

                                <li>
                                    <label class="select" for="status">
                                        <select id="status" onchange="listing(1, this.value)">
                                            <option value="-1"><?php echo Info::t_lang('ALL') ?>
                                                <?php if ($user_type == 0) { ?>
                                                <option value="1"><?php echo Info::t_lang('HOST') ?></option>
                                            <?php } else { ?>
                                                <option value="1"><?php echo Info::t_lang('TRAVELERS') ?></option>
                                            <?php } ?>
                                            <option value="0"><?php echo Info::t_lang('ADMIN') ?></option>

                                        </select></label>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </header>
                <div class="section__body">
                    <div class="container container--fluid">
                        <div class="span__row">
                            <div class="span span--12 message-list">

                            </div>
                            <div style="display:none;" id='reply-msg'>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
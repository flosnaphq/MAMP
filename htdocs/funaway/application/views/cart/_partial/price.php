<h6 class="book-card__price__heading"><?php echo Currency::displayPrice($price); ?></h6>
<button id = "book-now" onclick="addInCart()" class="button  button--fill button--red"><?php echo INFO::t_lang('ADD_TO_CART') ?></button>
<p class="regular-text no--margin"><?php echo INFO::t_lang('INCLUSIVE ALL TAXES') ?></p>

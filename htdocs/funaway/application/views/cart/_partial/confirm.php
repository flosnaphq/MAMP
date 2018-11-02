<h6 class="book-card__price__heading"><?php echo Currency::displayPrice($price);?></h6>
<span><?php echo Info::t_lang('PRIOR_CONFIRMATION_REQUIRED_HOST_TO_BOOK');?></span>
<button id = "book-now" onclick="sendConfirmationRequest()" class="button button--fit button--fill button--red"><?php echo Info::t_lang('REQUEST')?></button>
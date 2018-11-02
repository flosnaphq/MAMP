<h6 class="filter__heading"><?php echo Info::t_lang('CATEGORIES')?></h6>
<ul class="list list--horizontal list--5">
	<?php foreach($services as $k=>$serv){ ?>
	<li>
		<label class="checkbox">
			
				<input  value= "<?php echo $k?>" name='themes[]' <?php /* if(in_array($k,@$theme)) echo 'checked="checked"' */?> class='searchCategories' type="checkbox">
				<span class="checkbox__icon"></span>
				<span class="checkbox__label"><?php echo $serv;?></span>
			
		</label>
	</li>
<?php } ?> 
</ul>
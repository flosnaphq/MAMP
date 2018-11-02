searchPage = 1;

$(document).ready(function(){
	jsonNotifyMessage('Loading...');
	/* if($('.searchThemes').val() != ""){
		getSubService('.searchThemes');
		
	} */
	
	listing();
	$('.searchThemes').change(function(){
		$(".searchCategories").val("");
		resetSearchPageCounter();
		listing();
	});

	$(".searchCategories").change(function(){
		resetSearchPageCounter();
		listing();
	});
	$(".searchcountry").change(function(){
		$(".searchcity").val("");
		resetSearchPageCounter();
		listing();
	});
	$(".searchcity").change(function(){
		resetSearchPageCounter();
		listing();
	});
	$('.searchIslands').change(function(){
		resetSearchPageCounter();
		listing();
	});
	
	$('.searchPrice').change(function(){
		resetSearchPageCounter();
		listing();
	});
	
	$('.searchDuration').change(function(){
		resetSearchPageCounter();
		listing();
	});
	
	$('.sortFilter').change(function(){
		resetSearchPageCounter();
		listing();
	});
	
	$('.displayFilter').click(function(){
		if($(this).text() == "Show Filter"){
			$(this).text('Hide Filter');
			$('.listing__filter').slideDown();
		}else{
			$(this).text('Show Filter');
			$('.listing__filter').slideUp();
		}
	});
});

getSubService = function(obj){
	$.ajax({
		url:fcom.makeUrl('services','sub-service'),
		type:'post',
		data:{'service_id':$(obj).val()},
		success:function(json){
			json = $.parseJSON(json);
			$('.searchCategories').html(json.msg);
		}
	});
}

getCities = function(obj){
	$.ajax({
		url:fcom.makeUrl('country','cities'),
		type:'post',
		data:{'country_id':$(obj).val()},
		success:function(json){
			json = $.parseJSON(json);
			$('.searchcity').html(json.msg);
		}
	});
}

resetSearchPageCounter = function(pgCounter)
{
	var pgCounter = pgCounter || 1;
	searchPage = pgCounter;
}

listing = function(page){
	if(typeof page === 'undefined'){
		page = 1;
		$("#js-activity-list").html("");
	}
	
	$(".showMoreButton").hide();
	jsonNotifyMessage('Loading...');
	/* search  = getSearchList(false);
	history.pushState({}, '', search); */
	arr = getSearchList(true);
	data = {"page":page,
				"sort":arr.sorttype,
				"island":arr.islands,
				"theme":arr.themes,
				"categories":arr.categories,
				"durations":arr.durations,
				"cities":arr.cities,
				"countries":arr.countries,
				"prices":arr.prices,
				"type":arr.type
			  };
	url = fcom.makeUrl("activity","listing");		  
			  
	$.ajax({
		url:url,
		data: data,
		type: "post",
		success:function(json){
			jsonRemoveMessage();
			json = $.parseJSON(json);
			if(1 == json.status){
				$("#js-activity-list").append(json.msg);
				if(json.more_record == 1){
					$(".showMoreButton").show();
				}
				$('.modaal-ajax').modaal({
						type: 'ajax'
				});
				
			}else{
				$("#js-activity-list").html(json.msg);
			}
		}
		
	});
}

changeListView = function(type,obj){
	$('.displayButton').removeClass('active');
	$(obj).addClass('active');
	if(type == "list"){
		$('.activity-card__list').removeClass('grid--style').addClass('list--style');
		
	}else{
		$('.activity-card__list').removeClass('list--style').addClass('grid--style');
	
	}
	
}

clearSearch = function(){
	resetSearchPageCounter();
	$('.searchIslands').prop('checked', false);
	$('.searchCategories').val('');
	$('.searchDuration').val('');
	$('.searchcountry').val('');
	$('.searchcity').val('');
	$('.searchPrice').val('');
	$('.sortFilter').val('');
	$('.searchThemes').val('');
	
	listing();
}

function getSearchList(arrayreturn){
 	search = [];
	sorttype=[];
	islands=[];
	themes=[];
	categories=[];
	prices = [];
	durations = [];
	type = [];
	countries = [];
	cities = [];
	
	
	sortval = $(".sortFilter").val();
	if(sortval != ""){
		sorttype.push(sortval);
	}
	
	
	
	priceval = $(".searchPrice").val();
	if(priceval != ""){
		prices.push(priceval);
	}
	
	themeVal = $(".searchThemes").val();
	if(themeVal != ""){
		themes.push(themeVal);
	}
	
	durationval = $(".searchDuration").val();
	if(durationval != ""){
		durations.push(durationval);
	}
	
	
	catval = $(".searchCategories").val();
	if(catval != "" && catval != null){
		categories.push(catval);
	}
	
	countryval = $(".searchcountry").val();
	if(countryval != "" && countryval != null){
		countries.push(countryval);
	}
	
	citiesval = $(".searchcity").val();
	if(citiesval != "" && citiesval != null){
		cities.push(citiesval);
	}
	/* $(".searchCategories").each(function(){
		if ($(this).is(':checked')){
			categories.push($(this).val());
		}		
	}); */
	
	$(".searchIslands").each(function(){
		if ($(this).is(':checked')){
			islands.push($(this).val());
		}		
	});
	
	
	
	
	if($('#listView').hasClass('active')){
		type.push('list');
	}else if($('#gridView').hasClass('active')){
		type.push('grid');
	}
	
	
	search.sorttype=sorttype;
	search.islands=islands;
	search.themes=themes;
	search.categories=categories;
	search.cities=cities;
	search.countries=countries;
	search.durations = durations;
	search.prices = prices;
	search.type = type;
	
	
	if(arrayreturn){
		return search;
	}
	
	ques = false;
	amp = false;
	str = "activity";
	if(type.length > 0){
		if(ques == false){
		str +="?";
		ques = true;
		}
		if(amp == true){
			str +="&";
		}
		amp = true;
		str += "type="+type.join();
	}

	if(sorttype.length > 0){
		if(ques == false){
		str +="?";
		ques = true;
		}
		if(amp == true){
			str +="&";
		}
		amp = true;
		str += "sort="+sorttype.join();
	}
	
	if(islands.length > 0){
		if(ques == false){
		str +="?";
		ques = true;
		}
		if(amp == true){
			str +="&";
		}
		amp = true;
		str += "island="+islands.join();
	}	
	if(themes.length > 0){
		if(ques == false){
		str +="?";
		ques = true;
		}
		if(amp == true){
			str +="&";
		}
		amp = true;
		str += "theme="+themes.join();
	}
	
	if(categories.length > 0){
		if(ques == false){
		str +="?";
		ques = true;
		}
		if(amp == true){
			str +="&";
		}
		amp = true;
		str += "categories="+categories.join();
	}
	
	if(cities.length > 0){
		if(ques == false){
		str +="?";
		ques = true;
		}
		if(amp == true){
			str +="&";
		}
		amp = true;
		str += "cities="+cities.join();
	}
	
	if(countries.length > 0){
		if(ques == false){
		str +="?";
		ques = true;
		}
		if(amp == true){
			str +="&";
		}
		amp = true;
		str += "countries="+countries.join();
	}

	if(durations.length > 0){
		if(ques == false){
		str +="?";
		ques = true;
		}
		if(amp == true){
			str +="&";
		}
		amp = true;
		str += "duration="+durations.join();
	}

	if(prices.length > 0){
		if(ques == false){
		str +="?";
		ques = true;
		}
		if(amp == true){
			str +="&";
		}
		amp = true;
		str += "price="+prices.join();
	}	
	return str; 
}

showMoreActivity = function(){
	searchPage++;
	listing(searchPage);
}

wishlist = function(obj,activityId){
	jsonNotifyMessage();
	facebookWishListTrack();
	$.ajax({
		url:fcom.makeUrl("wishlist","add-to-wish"),
		data: {"activity_id":activityId,fIsAjax:1
			  },
		type: "post",
		success:function(json){
			json = $.parseJSON(json);
			if(1 == json.status ){
				jsonSuccessMessage(json.msg);
				if(json.type == "add"){
					$(obj).addClass('has--active');
				}else{
					$(obj).removeClass('has--active');
				}
			}else{
				jsonErrorMessage(json.msg);
			}
		}
		
	});
}

window.onpopstate = function(e){
	srch_str = location.search;
	data = queryStringToJSON(srch_str);
	if(typeof data.sort !== 'undefined'){
		$('.sortFilter').val(data.sort);
	}else{
		$('.sortFilter').val("");
	}
	
	if(typeof data.duration !== 'undefined'){
		$('.searchDuration').val(data.duration);
	}else{
		$('.searchDuration').val("");
	}
	
	if(typeof data.price !== 'undefined'){
		$('.searchPrice').val(data.price);
	}else{
		$('.searchPrice').val("");
	}
	
	
	if(typeof data.type !== 'undefined'){
		if(data.type == "grid"){
		    $("#gridView").trigger('click');
		}else{
			$("#listView").trigger('click');
		}
	}else{
		$("#listView").trigger('click');
	}
	
	
	
	$('.searchIslands').prop('checked', false);
	$('.searchCategories').prop('checked', false);
	if(typeof data.theme !== 'undefined'){
		$.each(data.theme,function(k,v){
			$('.searchCategories').each(function(){
				if($(this).val() == v){
					$(this).prop('checked',true);
				}	
			});
		})
	}
	
	if(typeof data.island !== 'undefined'){
		$.each(data.island,function(k,v){
			$('.searchIslands').each(function(){
				if($(this).val() == v){
					$(this).prop('checked',true);
				}	
			});
		})
	}
	
	data.page = 1;
	url = fcom.makeUrl("activity","listing");	
	$.ajax({
		url:url,
		data: data,
		type: "post",
		success:function(json){
			json = $.parseJSON(json);
			if(1 == json.status){
				
				$("#js-activity-list").html(json.msg);
				$('.modaal-ajax').modaal({
						type: 'ajax'
				});
			}else{
				$("#js-activity-list").html(json.msg);
			}
		}
		
	});
}


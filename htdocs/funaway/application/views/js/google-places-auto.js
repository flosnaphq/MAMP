loadGoogleScript();

var placeSearch, autocomplete, data = {};

var componentForm = {
    street_number: 'short_name',
    route: 'long_name',
    locality: 'long_name',
    administrative_area_level_1: 'long_name',
    country: 'long_name',
    postal_code: 'short_name'
};

function loadGoogleScript() {
    
    var script = document.createElement('script');
    script.type = 'text/javascript';
    //script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&key=AIzaSyCyDAWsixlw-IFYnoAqLhcz7r_f1h01T6s&libraries=places,map&callback=initAutocomplete&language=en';    
    script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&key=AIzaSyCyDAWsixlw-IFYnoAqLhcz7r_f1h01T6s&libraries=places,map&language=en';    
    document.head.appendChild(script);

}






function initAutocomplete() {
    // Create the autocomplete object, restricting the search to geographical
    // location types.
    //   {types: ['establishment'], language: 'en'}
	

	
    autocomplete = new google.maps.places.Autocomplete(
            /** @type {!HTMLInputElement} */(document.getElementById("autocomplete")),
            {}
    );
    autocomplete.addListener('place_changed', fillInAddress);
}

function fillInAddress() {
    // Get the place details from the autocomplete object.
    var place = autocomplete.getPlace();
    if (!place.geometry) {
        window.alert("Autocomplete's returned place contains no geometry");
        return;
    }

    var PlaceType = "";


    var SelectedTextArray = [];

    for (var i = 0; i < place.address_components.length; i++) {
        var addressType = place.address_components[i].types[0];
        if (componentForm[addressType]) {
            var val = place.address_components[i][componentForm[addressType]];
            data[addressType] = val;
        }
        PlaceType = place.address_components[i].types.join();

        if (PlaceType.indexOf("administrative_area_level_1") < 0 && PlaceType.indexOf("country") < 0 && PlaceType.indexOf("postal_code") < 0)
            SelectedTextArray.push(place.address_components[i].long_name);
    }

    var location = place.geometry.location;

    document.getElementsByName('activity_latitude')[0].value = location.lat();
    document.getElementsByName('activity_longitude')[0].value = location.lng();
    $(document).trigger('google-places-postion-change');

    $('#autocomplete').val(place.name);
    $('#verify_change').val(1);

    return true;
}

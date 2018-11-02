
<link href='https://api.mapbox.com/mapbox.js/v2.4.0/mapbox.css' rel='stylesheet' />

<?php  
$frm->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
$frm->setFormTagAttribute('class', 'form form--default form--horizontal');
$frm->setFormTagAttribute('style', 'margin-top:1.25em');
$frm->setFormTagAttribute('id', 'frmMap');
$frm->setFormTagAttribute('action', FatUtility::generateUrl("manage-activity",'saveMapInfo'));
$frm->setFormTagAttribute('ng-submit', 'saveMapInfo($event);');
echo $frm->getFormHtml();?>


<script>
    
initAutocomplete();
showMap(<?php echo $lat?>,<?php echo $long?>);


$(document).bind('google-places-postion-change',function(){
    var lat = document.getElementById("act_lat").value; 
   map.remove();
    var lng = document.getElementById("act_long").value; 
  showMap(lat,lng);; 
    
});

</script>


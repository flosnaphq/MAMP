<?php

class MiscController extends MyAppController {

    public function __construct($action) {
        parent::__construct($action);
    }

    public function getCities($countryId=0) {
        if (!FatUtility::isAjaxCall()) {
            die("Invalid Action");
        }

        $countryId =  FatUtility::int($countryId);
        if($countryId==0){
             FatUtility::dieJsonSuccess(array());
        }
        $list = City::getAllCitiesByCountryId($countryId);
        FatUtility::dieJsonSuccess(array('msg'=>$list));
    }
    
    public function getCountries($regionId=0) {
        if (!FatUtility::isAjaxCall()) {
            die("Invalid Action");
        }

        $regionId =  FatUtility::int($regionId);
        if($regionId==0){
             FatUtility::dieJsonSuccess(array());
        }
        $list = Country::getAllCountryByRegionId($regionId);
        FatUtility::dieJsonSuccess(array('msg'=>$list));
    }
    
}

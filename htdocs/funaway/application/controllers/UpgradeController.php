<?php


class UpgradeController extends MyAppController{
    
    
    
    
    public function exportImages(){
        
      $fileType = 3;
      $srch = new SearchBase(AttachedFile::DB_TBL);
      $srch->addCondition(AttachedFile::DB_TBL_PREFIX . 'type', '=', $fileType);  
      $rs = $srch->getResultSet();
      $Dir = "/var/www/dv/a/a/4demo/%s/user-uploads/";
      $importFrom = sprintf("/var/www/dv/a/a/4demo/%s/user-uploads/",'funaway');
      $copyTo = sprintf("/var/www/dv/a/a/4demo/%s/user-uploads/",'funaway-latest');
      while($row =  FatApp::getDb()->fetch($rs)){
    
           $imageFile = $row['afile_physical_path'];
     
	
          if(!copy($importFrom.$imageFile,$copyTo.$imageFile)){
             
          }
          
          
          
          
      }
        
        
        
    }
    
    
    
    
    
    
    
}






























?>
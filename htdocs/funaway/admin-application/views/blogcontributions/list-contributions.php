<?php defined('SYSTEM_INIT') or die('Invalid Usage'); ?>
<?php if($canView === true ){ ?>
<div class="sectionbody">
        <?php
        $arr_flds = array(
            'listserial' => Info::t_lang('S._NO.'),
            'contribution_author_first_name' => Info::t_lang('FIRST_NAME'),
			'contribution_author_last_name'=>Info::t_lang('LAST_NAME'),
			'contribution_author_email'=>Info::t_lang('EMAIL'),
			'contribution_status'=>Info::t_lang('STATUS'),
            'actions' => Info::t_lang('ACTION'),
        );
		if(!$canEdit) {
			unset($arr_flds['actions']);
		}
        $tbl = new HtmlElement('table', array('class' => 'table table-bordered'));
        $th = $tbl->appendElement('thead')->appendElement('tr');
        foreach ($arr_flds as $key => $val) {
            $th->appendElement('th', array(), $val);
        }
		
		$i = ($pageNumber - 1) * $pageSize + 1;
		
		if (!is_array($list) || count($list) == 0) {
            $tbl->appendElement('tr')->appendElement('td', array('colspan' => count($arr_flds), 'class' => 'records_txt'),  Info::t_lang('NO_RECORDS_FOUND'));
			die($tbl->getHtml());
		}
		
        foreach ($list as $sn => $row) {
            			 
            $tr = $tbl->appendElement('tr');
            foreach ($arr_flds as $key => $val) {

                $td = $tr->appendElement('td', array('style' => ''));
                switch ($key) {
                    case 'listserial':
                        $td->appendElement('plaintext', array(), $i);
                        break;
					case 'contribution_status':
							$str='';
							$contri_status=Info::contriStatus();
							if(isset($contri_status[$row['contribution_status']]))
							{
								$str=$contri_status[$row['contribution_status']];
							}
					
						$td->appendElement('plaintext', array(), $str,true);
						break;
                    case 'actions':
                        $ul = $td->appendElement('ul', array('class' => 'actions'));
                        
						if($canEdit) {
							$li = $ul->appendElement('li');
							$li->appendElement('a', array('href' =>  FatUtility::generateUrl('blogcontributions', 'view', array($row['contribution_id'])), 'title' => 'View'), '<i class="ion-ios-eye icon"></i>', true);
							
							$li = $ul->appendElement('li');
							$li->appendElement('a', array('onclick'=>'confirmDelete(this);', 'data-href' =>  FatUtility::generateUrl('blogcontributions', 'delete', array($row['contribution_id'], $row['contribution_file_name'])), 'title' => 'Delete'), '<i class="ion-close-circled icon"></i>', true);
						}
						break;
					default:
                        $td->appendElement('plaintext', array(), $row[$key]);
                        break;
                }
            }
			 $i++;
        }        
		echo $tbl->getHtml();
			
        ?>
</div>
<?php 	
if ($pageCount > 1)
				echo html_entity_decode($pagination);
}
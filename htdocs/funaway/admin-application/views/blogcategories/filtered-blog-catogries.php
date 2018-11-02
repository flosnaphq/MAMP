<?php defined('SYSTEM_INIT') or die('Invalid Usage'); ?>
<?php if($canView === true ){ ?>
<div class="sectionbody">
        <?php
        $arr_flds = array(
			'listserial' => Info::t_lang('S.NO.'),
			'category_title' => Info::t_lang('CATEGORY_TITLE'),
			'cat_parent' => Info::t_lang('CATEGORY_PARENT'),
			'category_description'=> Info::t_lang('CATEGORY_DESCRIPTION'),
			'category_status'=> Info::t_lang('CATEGORY_STATUS'),
            'actions' => Info::t_lang('ACTION')
        );
		$tbl = new HtmlElement('table', array('class' => 'table table-bordered', '', 'id' => 'category'));
        $th = $tbl->appendElement('thead')->appendElement('tr');
        foreach ($arr_flds as $key => $val) {
            $th->appendElement('th', array(), $val);
        }
		if (!is_array($records) || count($records) == 0) {
            $tbl->appendElement('tr')->appendElement('td', array('colspan' => count($arr_flds), 'class' => 'records_txt'), Info::t_lang('NO_RECORDS_FOUND'));
			die($tbl->getHtml());
		}
		foreach ($records as $sn => $row) {
			$trClass = ($row['category_status'] != 0) ? '' : 'inactive nodrag nodrop';
            $tr = $tbl->appendElement('tr', array('id'=>$row['category_id'], 'class'=>$trClass));
            foreach ($arr_flds as $key => $val) {
                $td = $tr->appendElement('td', array('style' => ''));
                switch ($key) {
                    case 'listserial':
                        $td->appendElement('plaintext', array(), $sn+1);
                        break; 
					case 'category_title':
                        $td->appendElement('plaintext', array(), $row['category_title']);
                        break;
					case 'cat_parent':
                        $td->appendElement('plaintext', array(), ($row['cat_parent']=="")?' --- ':$row['cat_parent']);
                        break;
						
					case 'category_description':
                        $td->appendElement('plaintext', array(), BlogConstants::truncateCharacters($row['category_description'],20));
                        break;
					 
					case 'category_status':
							$active = "active";
							if($row['category_status']) {
								$active = '';
							}
							$statucAct = ($canEdit === true) ? 'toggleStatus(this)' : '';
						$str='
							<label id="'.$row['category_id'].'" class="statustab addmarg '.$active.'" onclick="'.$statucAct.'">
							  <span class="switch-labels" data-on="Inactive" data-off="Active"></span>
							  <span class="switch-handles"></span>
							</label>';
					
						$td->appendElement('plaintext', array(), $str,true);
						break;
                    case 'actions':
                        $ul = $td->appendElement('ul', array('class' => 'actions'));
                        $li = $ul->appendElement('li');
						if($canEdit) {
							$li->appendElement('a', array('href' =>  FatUtility::generateUrl('blogcategories', 'form', array($row['category_id'])), 'title' => 'Edit'), '<i class="ion-edit icon"></i>', true);
						}
						if ($row['category_status'] == 1) {
							$li = $ul->appendElement('li');
							$li->appendElement('a', array('href' =>  FatUtility::generateUrl('blogcategories', 'blogchildcategories', array($row['category_id'])), 'title' => 'Manage Sub Categories'), '<i class="ion-drag icon"></i>', true);
							 
						}						
						break;
					default:
                        $td->appendElement('plaintext', array(), $row[$key]);
                        break;
                }
            }
        }
		echo $tbl->getHtml();
		?>
</div>
 <?php } ?> 
 <script>
    $(document).ready(function () {
		$('#category').tableDnD({
			onDrop: function (tbody, row) {
                var order = $.tableDnD.serialize('id');
                order += '&catId=' + catId;
				fcom.ajax(fcom.makeUrl('blogcategories', 'setCatDisplayOrder'), order, function (t) {
				});
            }
        });
    });
</script>
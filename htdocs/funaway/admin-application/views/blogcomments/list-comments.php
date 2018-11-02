<?php defined('SYSTEM_INIT') or die('Invalid Usage'); ?>
<?php if($canView === true ){ ?>
<div class="sectionbody">
        <?php
        $arr_flds = array(
            'listserial' => Info::t_lang('S._NO.'),
            'comment_author_name' => Info::t_lang('AUTHOR_NAME'),
			'comment_author_email'=> Info::t_lang('AUTHOR_EMAIL'),
			'comment_content'=> Info::t_lang('COMMENT'),
			'post_title'=> Info::t_lang('POST'),
			'comment_status'=> Info::t_lang('STATUS'),
            'actions' => Info::t_lang('ACTION')
        );
        $tbl = new HtmlElement('table', array('class' => 'table table-bordered', '', 'id' => 'category'));
        $th = $tbl->appendElement('thead')->appendElement('tr');
        foreach ($arr_flds as $key => $val) {
            $th->appendElement('th', array(), $val);
        }
		
		$i = ($pageNumber - 1) * $pageSize + 1;
		
		if (!is_array($list) || count($list) == 0) {
            $tbl->appendElement('tr')->appendElement('td', array('colspan' => count($arr_flds), 'class' => 'records_txt'), Info::t_lang('NO_RECORDS_FOUND'));
			die($tbl->getHtml());			
		}
	
        foreach ($list as $sn => $row) {
              
            $tr = $tbl->appendElement('tr', array('id' => 'row-'.$row['comment_id']));
            foreach ($arr_flds as $key => $val) {

                $td = $tr->appendElement('td', array('style' => ''));
                switch ($key) {
                    case 'listserial':
                        $td->appendElement('plaintext', array(), $i);
                        break; 
					case 'comment_author_name':
                        $td->appendElement('plaintext', array(), $row['comment_author_name']);
                        break;
					case 'comment_author_email':
                        $td->appendElement('plaintext', array(), $row['comment_author_email']);
                        break;
					case 'comment_content':
                        $td->appendElement('plaintext', array(), BlogConstants::truncateCharacters($row['comment_content'],20));
                        break;
					case 'post_title':
						 
						if($canEdit) {
							$td->appendElement('a', array('href' => FatUtility::generateUrl('BlogPosts', 'form', array($row['post_id'])), 'title' => 'View Post'), $row['post_title'], true);
						}
                        break;
					 
					case 'comment_status':
						$str = "";
						if ($row['comment_status'] == 1) {
							$str = 'Approved';	
						} elseif ($row['comment_status'] == 2) {
							$str = 'Declined';	
						} else {
							$str = 'Pending';
						}
					
						$td->appendElement('plaintext', array(), $str,true);
						break;
                    case 'actions':
                        $ul = $td->appendElement('ul', array('class' => 'actions'));
						if($canView) {
							$li = $ul->appendElement('li');
							$li->appendElement('a', array('href' =>  FatUtility::generateUrl('blogcomments', 'view', array($row['comment_id'])), 'title' => 'View'), '<i class="ion-ios-eye icon"></i>', true);
						}
						if($canEdit) {
							$li = $ul->appendElement('li');
							$li->appendElement('a', array('data-href' =>  FatUtility::generateUrl('blogcomments', 'delete', array($row['comment_id'])), 'title' => 'Delete' , 'onclick'=>'return confirmDelete(this);', 'data-id' => 'row-'.$row['comment_id']), '<i class="ion-close-circled icon"></i>', true);
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
		if ($pageCount > 1)
				echo html_entity_decode($pagination);
		?>
</div>

<?php
}
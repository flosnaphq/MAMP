<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

?>

<table width='100%' class='table table-responsive'>
    <thead>
        <th>Posted by</th>
        <th>Name</th>
        <th>Added on</th>
        <th>Message</th>
        <th></th>
    </thead>
	<?php 
	foreach($records as $message){
        ?>
        <tr>
            <td><?php echo $message['reviewmsg_user_type'] == 0 ? 'Admin' : 'Host'; ?></td>
            <td><?php echo $message['reviewmsg_user_type'] == 0 ? $message['admin_name'] : $message['user_full_name']; ?></td>
            <td><?php echo $message['reviewmsg_added_on']; ?></td>
            <td width='30%' style='word-break: break-word;'><?php echo $message['reviewmsg_message']; ?></td>
            <td><ul class="actions"><li><a href="javascript:;" class="button small" title="Edit" onclick="replyToReview('<?php echo $message['reviewmsg_review_id']; ?>','<?php echo $message['reviewmsg_id']; ?>')"><i class="ion-edit icon"></i></a></li></td>
        </tr>
        <?php
    }
	?>
</table>
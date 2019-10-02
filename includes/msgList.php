<?php
if(!isset($PAGE)) { //if the user isn't coming in from the main market page, then load normal variables
	if(!isset($_SESSION['loggedIn'])) {
		$SESSION_NAME="battle";
		session_name($SESSION_NAME);
		session_start();
	}
	$user=$_SESSION['UserID'];
	
	require_once 'connect.inc';
	$db=do_connect();
	
	include 'config.php';
	
	if(isset($_GET['type'])) {
		$type=$_GET['type']; //registar globals is OFF, gets type
	} else { $type=1; }

	$which=$_GET['which'];
	//to prevent the hacking attempt notice
	$PAGE='AJAX';
	include '../Page.php';
	$page=new Page('../template');
	
	msgList($type);
	$page->replace_table($pageArray['msg_list']);
	echo $pageArray['msg_list'];
}

function msgList($type)
{
	global $page, $pageArray, $db, $user, $which;
	$pageArray['msg_list']=$page->loadPage('messages_list',PAGE);
	$preArray['Inbox_Anchor'] = ($type==0) ? '<a href="javascript:void(0)" onclick="selectMessage(1,{WHICH})">' : '';
	$preArray['Inbox_End'] = ($type==0) ? '</a>' : '';
	$preArray['Outbox_Anchor'] = ($type==1) ? '<a href="javascript:void(0)" onclick="selectMessage(0,{WHICH})">' : '';
	$preArray['Outbox_End'] = ($type==1) ? '</a>' : '';
	$preArray['which']=$which;
	$page->replace_tags($preArray,$pageArray['msg_list']);
	
	//the following code is already executed if this file is coming from the main file
	//otherwise, it's to find what character is looking at the page
	if(!isset($pageArray['from'])) {
		$sql="SELECT id FROM characters WHERE game_id=? AND user_id='$user'";
		$char=get_row($db,$sql,0,$which);
		if(empty($char)) {
			$from=0;
		} else { $from=$char['id']; }
	} else {
		$from=$pageArray['from'];
	}
	
	//this sql will be more dynamic later, for sorting messages and stuff
	switch($type) {
		case 0:
			$sql="SELECT messages.*, reciever.name FROM messages
					LEFT JOIN characters AS reciever ON messages.receiver_ID=reciever.id
					WHERE messages.sender_ID=? AND messages.game_id=? AND type=0";
			break;
		case 1:
			$sql="SELECT messages.*, sender.name FROM messages
					LEFT JOIN characters AS sender ON messages.sender_ID=sender.id
					WHERE messages.receiver_ID=? AND messages.game_id=? AND type=1";
			break;
	}

	$query=query($db,$sql,$from,$which);
	$total=how_many($query);

	//if there's nothing showing up, don't bother with outputing anything else
	if($total==0) {
		$page->table='No messages here.';
		return;
	}

	$result=next_row($query);

	//outputs all the messages currently selected
	for($i=0;$i<$total;$i++) {
		//adds a double space between messages
		if($i>0) { $page->table=$page->table.'<br/><br/>'; }
		//if the type of message view is the INBOX
		if($type) {
			$page->table=$page->table.$page->loadPage('message_item_inbox',TABLE);
			$result['name']=(empty($result['name'])) ? 'Game Operator' : '<a href="{URL}character/{sender_id}">'.$result['name'].'</a>';
		} else { //otherwise, OUTBOX
			$page->table=$page->table.$page->loadPage('message_item_outbox',TABLE);
			$result['name']=(empty($result['name'])) ? 'Game Operator' : '<a href="{URL}character/{receiver_id}">'.$result['name'].'</a>';
		}
		
		$result['time_sent']=date("F jS</b> g:i:s A",$result['time_sent']);
		$result['msg_text']=nl2br($result['msg_text']);

		if(($result['time_sent']+3600*24)>time()) {
			$result['delete']='<a class="small" href="{URL}messages/delete/{ID}/'.$type.'">Delete</a>';
		} else {
			$result['delete']='';
		}
		
		//adds some nice formatting in this case
		if($type) $result['delete']=' | '.$result['delete'];
		//needed for best results, to make the page output correctly
		$result=array_reverse($result,true);
		$page->replace_tags($result,$page->table);
		
		$result=next_row($query);
	}
	return;
}
?>
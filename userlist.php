<?php
$PERMISSIONS=0;
$PAGE="User List";
include 'includes/common.inc';
include 'includes/builds.inc';
include 'includes/headers.inc';

if(isset($_REQUEST['style'])) {
	$style=$_REQUEST['style']; //registar globals is OFF, gets style
} else { $style=""; }
if(isset($_REQUEST['sort'])) {
	$sort=$_REQUEST['sort']; //registar globals is OFF, gets sort
} else { $sort=""; }
if(isset($_REQUEST['start'])) {
  $start=$_REQUEST['start']; //registar globals is OFF, gets start
} else { $start=0; }

if(isset($_REQUEST['action'])) {
  $action=$_REQUEST['action']; //registar globals is OFF, gets action
} else { $action=""; }

define('PER_PAGE',50);

$sortArray=array('id'=>'','name'=>'','current_males'=>'','current_females'=>'','creation_time'=>'','last_activity'=>'','desc'=>'');

if(empty($sort)) { $sort="user_id"; $style="ASC"; $start=0; }
$pageArray['start']=$start;
$sortArray[$sort]='checked="checked"';

$sql="SELECT SQL_CALC_FOUND_ROWS * FROM users ORDER BY " . $sort .' '. $style . " LIMIT " . $start . ", ".PER_PAGE;

$query=query($db, $sql);
$grandtotal=get_row($db,"SELECT FOUND_ROWS()",1);
$total=how_many($query);
$result=next_row($query);

$pageArray['page_numbers']=buildPageNumbers('game/mine');
$page->table='';

for($i=0;$i<$total;$i++) {
	$page->table=$page->table.$page->loadPage('userlist',TABLE);

	switch($result['user_level']) {
		case 0:
			$result['rank']='Not Active';
			break;
		case 1:
			$result['rank']='Normal User';
			break;
		case 2:
			$result['rank']='Power User';
			break;
		case 3:
			$result['rank']='Game Moderater';
			break;
		case 4:
			$result['rank']='Administrator';
			break;
	}
	$page->replace_tags($result,$page->table);
	
	$result=next_row($query);
}

if($user==0) { $pageArray['class']='class="margin"'; } else { $pageArray['class']=''; }

$page->page=$page->loadPage('userlist',PAGE);
$page->replace_tags($sortArray,$page->page);
$page->replace_tags($pageArray,$page->page);
$page->replace_table($page->page);
$page->outputPage();
?>
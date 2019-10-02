<?php
$PERMISSIONS=2;
$PAGE="Weapons";
include 'includes/common.inc';
include 'includes/builds.inc';
include 'includes/headers.inc';

if(isset($_REQUEST['action'])) {
	$action=$_REQUEST['action']; //registar globals is OFF, gets action
} else { $action=""; }

$pageArray['msg']='';

switch($action) {
	case "view":
		$sql="SELECT * FROM weapons WHERE id=?";
		$result=get_row($db,$sql,0,$_GET['which']);
		$pageArray['which']=$_GET['which'];
		$result['name']=sanitize($result['name']);
		$result['description']=sanitize($result['description']);
		
		$page->page=$page->loadPage('weapons_view',PAGE);
		$page->replace_tags($pageArray,$page->page);
		$page->replace_tags($result,$page->page);
		$page->outputPage();
		break;
	case "edit":
		$sql="SELECT * FROM weapons WHERE id=?";
		$result=get_row($db,$sql,0,$_GET['which']);
		$pageArray['which']=$_GET['which'];
		$result['name']=sanitize($result['name']);
		$result['description']=sanitize($result['description']);
		
		$page->page=$page->loadPage('weapons_edit',PAGE);
		$page->replace_tags($pageArray,$page->page);
		$page->replace_tags($result,$page->page);
		$page->outputPage();
		break;
	case "add":
		$page->page=$page->loadPage('weapons_add',PAGE);
		$page->replace_tags($pageArray,$page->page);
		$page->outputPage();
		break;
	case "edited":
		$which=$_GET['which'];
	case "added":
		$name=sanitize($_POST['name']);
		$desc=sanitize($_POST['desc']);
		$ammo=sanitize($_POST['ammo']);
		$minDmg=sanitize($_POST['minDmg']);
		$maxDmg=sanitize($_POST['maxDmg']);
		$notes=sanitize($_POST['notes']);

		if($action=="added") {
			$sql="INSERT INTO weapons (name, description, ammo, min_dmg, max_dmg, creator_ID, notes)
						VALUES(?,?,?,?,?,'$user',?)";
			query($db, $sql, $name, $desc, $ammo, $minDmg, $maxDmg, $notes);
		}
		if($action=="edited") {
			$sql="UPDATE weapons SET name=?, description=?, ammo=?, min_dmg=?, max_dmg=?, notes=?
						WHERE id=?";
			query($db, $sql, $name, $desc, $ammo, $minDmg, $maxDmg, $notes,$which);
		}
		$pageArray['msg']=$name.' successfully '.$action.'<br/>';

	default:
		if(isset($_REQUEST['style'])) {
			$style=$_REQUEST['style']; //registar globals is OFF, gets style
		} else { $style=""; }
		if(isset($_REQUEST['sort'])) {
			$sort=$_REQUEST['sort']; //registar globals is OFF, gets sort
		} else { $sort=""; }
		
		define('PER_PAGE',50);
		$sortArray=array('id'=>'','name'=>'','current_males'=>'','current_females'=>'','creation_time'=>'','last_activity'=>'','desc'=>'');
		
		if(empty($sort)) { $sort="id"; $style="ASC"; $start=0; }

		$sql='SELECT SQL_CALC_FOUND_ROWS weapons.*, users.username FROM weapons JOIN users ON weapons.creator_ID=users.user_id
					ORDER BY ' . $sort .' '. $style . ' LIMIT ' . $start . ', '.PER_PAGE;
		$query=query($db, $sql);
		
		$grandtotal=get_row($db,"SELECT FOUND_ROWS()",1);
		$total=how_many($query);
		$result=next_row($query);

		$pageArray['page_numbers']=buildPageNumbers('game/find');
		$page->table='';

		for($i=0;$i<$total;$i++) {
			$page->table=$page->table.$page->loadPage('weapon_list',TABLE);
			
			$result['edit']=($result['creator_ID']==$user) ? '<a href="{URL}weapons/edit/'.$result['id'].'">Edit</a>': 'N/A';

			$page->replace_tags($result,$page->table);
			$result=next_row($query);
		}
		
		$page->page=$page->loadPage('weapons',PAGE);
		$page->replace_tags($sortArray,$page->page);
		$page->replace_tags($pageArray,$page->page);
		$page->replace_table($page->page);
		$page->outputPage();
		break;
}
?>
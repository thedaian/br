<?
include 'Page.php';

$page=new Page('template');
$page->loadHeaders();

function displayPageHeaders() {
	global $page;
	$page->replace_tags(returnPageInfo(),$page->header);
	$page->replace_tags(buildMenu(),$page->menu);

	$page->outputHeader();
	$page->outputMenu();
}

function displaySideMenu() {
	global $page, $db, $user;
	$sql="SELECT id, name, map_type, applied, current_males+current_females AS total, start_time FROM games WHERE owner_id='$user' AND options | 1";
	$query=query($db,$sql);
	$total=how_many($query);
	if($total>0) {
		for($i=0;$i<$total;$i++) {
			$gamesRun=next_row($query);
			$page->setTable($page->loadPage('side_gamesRun',HEADER));
			$page->replace_tags(buildGamesRun($gamesRun),$page->table);
		}
	}

	$sql="SELECT characters.id, characters.game_id, games.name, games.map_type FROM characters JOIN games ON characters.game_id=games.id
			WHERE user_id='$user' AND game_id<>0 ORDER BY game_id DESC";
	$query=query($db,$sql);
	$total=how_many($query);
	if($total>0) {
		for($i=0;$i<$total;$i++) {
			$gameIn=next_row($query);
			$page->setTable($page->loadPage('side_gamesIn',HEADER));
			$page->replace_tags(buildGamesIn($gameIn),$page->table);
		}
		$side='side_menu';
	} else {
		$side='side_menu_nt';
	}

	$page->side_menu=$page->loadPage($side,HEADER);
	$page->replace_table($page->side_menu);

	$page->outputSideMenu();
}

//top level page headers
displayPageHeaders();
//if the user is logged in, display the side menu
if($user>0)
	displaySideMenu();

$page->content=$page->loadPage('content');

$pageArray=array('USERID'=>$user);
?>
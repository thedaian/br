<?
//returns basic information of the page in an array format for use with the header section
function returnPageInfo() {
	global $user, $PAGE;
	$baseStyle=($user>0) ?	'game-styles.css' :	$baseStyle='notLoggedIn.css';
	if($PAGE=='Game Map') {
		$javascript='<script src="{URL}js/map.js" type="text/javascript"></script>';
		$javascript.='<script type="text/javascript">var url=\'{URL}\';</script>';
	} elseif($PAGE=='Messages') {
		$javascript='<script src="{URL}js/messages.js" type="text/javascript"></script>';
		$javascript.='<script type="text/javascript">var url=\'{URL}\';</script>';
	} else { $javascript=''; }
	return array('PAGE'=>$PAGE,'MYID'=>$user,'BASESTYLE'=>$baseStyle,'JAVASCRIPT'=>$javascript,'DIRNAME'=>$_SERVER['PHP_SELF']);
}
//builds the main menu information
function buildMenu() {
	$creator='';
	$admin='';
	if($_SESSION['GameRank']>=2) {
		$creator='<li><a href="{URL}game/mine">My Games</a></li>';
		$creator.='<li><a href="{URL}weapons/">Weapons</a></li>';
		if($_SESSION['GameRank']==4)
			$admin='<li><a href="{URL}admin.php?mode=sessions" target="_blank">Admin Panel</a></li>';
	}
	return array('CREATOR'=>$creator,'ADMIN'=>$admin);
}
//builds the side menu
//games running section
function buildGamesRun($games=array()) {
	$menu='';
	if($games['applied']>0) {
		$menu.='<li><a href="{URL}manage/applied/{ID}">List Applicants ({APPLIED})</a></li>';	}
	if($games['total']>0) {
		$menu.='<li><a href="{URL}{MANAGE}/list/{ID}">List Members ({TOTAL})</a></li>';	}
	if(!empty($games['map_type'])) {
		$menu.='<li><a href="{URL}map/manage/{ID}">Change Map</a></li>';
	} else { $menu.='<li><a href="{URL}manage/{ID}">Upload a map</a></li>'; }
	$manage=($games['start_time']==0) ? 'manage' : 'run';
	return array('INNER'=>$menu,'ID'=>$games['id'],'NAME'=>$games['name'],'APPLIED'=>$games['applied'],'TOTAL'=>$games['total'], 'MANAGE'=>$manage);
}
//builds the side menu
//games currently in section
function buildGamesIn($game=array()) {
	$menu='';
	if(!empty($game['map_type']))
		$menu='<li><a href="{URL}map/{GAME_ID}">Game Map</a></li>';
	return array('MAP'=>$menu,'GAME_ID'=>$game['game_id'],'ID'=>$game['id'],'GAME_NAME'=>$game['name']);
}
//builds a single comment based on array input
//returns a string of a comment
function buildUserComment($comment=array()) {
	global $page;
	$thiscomment=$page->loadPage('user_comment',PAGE);
	$comment['time_posted']=date("n/d g:i:s A",$comment['time_posted']);
	if($_SESSION['GameRank']>=3) {
		$thiscomment=$thiscomment.'<a class="small" href="{URL}user/delete/{WHICH}/{ID}">Delete</a><br/>';
	}
	$page->replace_tags($comment,$thiscomment);
	return $thiscomment;
}
//outputs an error message
//using the base page styles, so it "fits" with the theme of the site
function error($msg) {
	global $pageArray, $page;
	$page->content=$page->loadPage('error',BASE);
	$pageArray['error']=$msg;
	$page->replace_tags($pageArray,$page->content);
	echo $page->content;
	die();
}

function buildPageNumbers($url) {
	global  $total, $grandtotal, $sort, $style, $start;
	$pageNumber='';
	//gets the total amount of pages, and the current page
	$pages=($grandtotal[0]-1)/PER_PAGE;
	$currentpage=($start/PER_PAGE);
	//set the starting page to zero
	$pagestart=0;

	for($page=0;$page<=$pages;$page++) {
		//if the page number is not 0, add a comma to make the formatting look good
		if($page!=0) {
			$pageNumber=$pageNumber.', ';
		}
		//if the page number is not the current page, then output a page link, otherwise, just output the actual page as a number
		if($currentpage!=$page) {
			$pageNumber=$pageNumber.'<a href="{URL}'.$url.'/sort/'.$sort.'/'.$style.'/'.($pagestart+$page*PER_PAGE).'">'.($page+1).'</a>';
		} else {
			$pageNumber=$pageNumber.($page+1);
		}
	}
	
	return $pageNumber;
}
?>
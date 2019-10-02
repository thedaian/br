<?php
$PERMISSIONS=0;
$PAGE="About";
include 'includes/common.inc';
include 'includes/builds.inc';
include 'includes/headers.inc';

$page->page=$page->loadPage('about',PAGE);
$page->replace_tags($pageArray,$page->page);
$page->outputPage();
?>
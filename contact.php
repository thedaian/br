<?php
$PERMISSIONS=0;
$PAGE="Contact";
include 'includes/common.inc';
include 'includes/builds.inc';
include 'includes/headers.inc';

$page->page=$page->loadPage('contact',PAGE);

$page->outputPage();
?>
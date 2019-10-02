<?php
$PERMISSIONS=0;
$PAGE="FAQ";
include 'includes/common.inc';
include 'includes/builds.inc';
include 'includes/headers.inc';

$page->page=$page->loadPage('faq',PAGE);

$page->outputPage();
?>
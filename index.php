<?php

include("template.php");

$page = getSkeleton();
$page = addTitle($page);

$page = setTitle("Hello World", $page);
$page = addScript("scripts/test.js", $page);
$page = addStyle("styles/main.css", $page);

$page = addAfterMany("div", "", "many1", "body", "", $page);
$page = addAfterMany("div", "", "many1", "div", "many1", $page);
$page = addAfterMany("div", "", "many2", "div", "many1", $page);
echo $page;
?>
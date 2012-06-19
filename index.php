<?php

include("template.php");

$page = getSkeleton();
$page = addTitle($page);

$page = setTitle("Hello World", $page);
$page = addScript("scripts/test.js", $page);
$page = addStyle("styles/main.css", $page);

echo $page;
?>
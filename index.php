<?php

include("template.php");

$page = file_get_contents("partials/index.html");

$page = setTitle("Hello World", $page);
$page = addScript("scripts/test.js", $page);
$page = addStyle("styles/main.css", $page);

echo $page;
?>
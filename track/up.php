<?php
include_once 'include/database.php';
$l = $_GET['l'];

$success = $database->addLocationItem($l);

if ($success) {
	echo "1";
} else {
	echo "0";
}
?>
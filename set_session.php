<?php

session_start();

if(!empty($_GET['user'])) {
	if($_GET['user'] == 'norman') {
		$_SESSION['user'] = 'norman';
	}
}

header("Location: index.php");
<?

if(isset($_POST['action'])){

	require("includes/configure.php");

	switch ($_POST['action']) {
		case 'new_folder':
			$r = mysql_query("INSERT INTO folders(folder_name) VALUES('{$_POST['data']['name']}')");
			break;
	}
	echo json_encode($r);
}
?>
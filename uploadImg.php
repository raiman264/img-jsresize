<?



	if(isset($_POST['imgData'])){
		// accept image data encoded

		$data = $_POST['imgData'];
		$desc = isset($_POST['info']['desc']) ? $_POST['info']['desc'] : '';
		$file_name = 'vta-surfndive_'.urlencode(substr($desc, 15)).date("_YmdHis-").substr(microtime(),2,80);
		$base_url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']);

		$data = explode(",",$data,2);
		$type = strpos ( $data[0], "/")+1;
		$type = substr($data[0],$type, strpos ( $data[0], ";",$type)-$type);

		$save_path_o = "uploaded_images/{$file_name}_original.$type";
		$save_path = "uploaded_images/{$file_name}.$type";

		$data = base64_decode($data[1]);

		//save original image
		$r = file_put_contents ( $save_path_o , $data);

		//exit($r);


		// save image with logo
		$im = imagecreatefromstring($data);
		$logo = imagecreatefrompng("img/wmark_all.png");

		//$s = imagecopymerge($im, $logo, 325, 375, 0, 0, 650, 150, 100);
		imagecopy($im, $logo, 0, 0, 0, 0, 1300, 900);
		

		if ($im !== false) {
		    //header("Content-Type: image/$type");

		    switch($type){
		    	case 'png':
		    		$r = imagepng($im,$save_path,2);
		    		break;
				case 'jpeg':
				case 'jpg' :
		    		$r = imagejpeg($im,$save_path,80);
		    		break;
				case 'gif':
					$r = imagejpeg($im,$save_path);
		    		break;
		    }
		    imagedestroy($im);

		    echo json_encode($r);
		}

		if($r){
			require("includes/configure.php");

			$in_fb = isset($_POST['info']['in_facebook']) ? $_POST['info']['in_facebook'] : 0;

			mysql_query("INSERT INTO `images` (`image_path`, `image_in_fb`, `image_desc`) 
									VALUES ('$save_path', '{$in_fb}', '{$desc}');");
			echo mysql_error();

			$id = mysql_insert_id();
			if(!empty($id) && !empty($_POST['folder']['id'])){
				mysql_query("INSERT INTO `relate_images` (`folder_id`, `image_id`) 
									VALUES ('{$_POST['folder']['id']}', {$id});");
				echo mysql_error();
			}

			if($in_fb){
				echo $url = $save_path;
				echo $album = isset($_POST['folder']['name']) ? $_POST['folder']['name'] : '';
				echo $mail = 'trigger@ifttt.com';

			}
		}
	}

?>
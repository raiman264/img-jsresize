<?
$data = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAMAAABEpIrGAAAAA3NCSVQICAjb4U/gAAACZ1BMVEX///+vpaFhv9xiudnSmInUlINlwOBkvt5kw+OSpqycoaWdoKJnw+Vkw+Ngvt9atc5bs8hnw+Vgvt+mmpdhv9x5zutyyuhjwdxhv9xZudNWt9BQttVvqruZmZn////5/P3y/P3q+/zp9/rf+fzd+PvX+Pzk9fjf9PfP9/zS9fvP9fvS8/rX8PfM9PvD9P3C9P/S8PfG8vvQ7vS+8/658v3O7fPD7/m/8Pu87/q08Py07/rE6vOu7vyq7/ym7vy/6PGp6/us6vm55vKf6/yx5/Sp6Pen5/iz5PGh6fui5/md5/us4++Z5v6h4/Ob5feS5/yX5fid4vOO5PmK5fyg3fCf3fGG4vmb3O2S3vKF3/mV2fCB3viP2+583viB3fiS1/B43viH2/F+2/J02/aK1exy2/d82PKC1/OG1up42PKG0+uI0+tl1/h/0ul/0Oxu0+960OV10ul+z+h5zutn0/Fc1fdyzuh2zelk0e9g0OxyyuhtzOZ0yuhuyutry+Rqyutpyudly+dlyut3xt5pyeNxxud0xdxlx+hMzvFpxt9yw9phx+Fdx+dhxuZjxd9Ly+5wwthXyOdNy+xQyetdxuNSyehaxeJIye1TxuVtwNdMxudjwdxGx+ppv9hIxudWwuRQw+FPwuJNwuFDw+VIwuJlutdFwuNBwuNKv99Mv+FEv+JCv+JHvd1ft89ZudNBvuA9vd9Hu9tFu9tWt9A8u91JuNdCuttQttU3u945uttIttM/uNlGtdE3t9s1t9owttpAs9E0tddCsc08sc+ZmZkxs9Y+r805sM4ssdYqsdQ7rcwrr9c2qskcnpCoAAAAzXRSTlMAM0RERERVVXd3d3eIiLu7u8zMzN3u7u7u7u7u7u7/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////+fUuggAAAAlwSFlzAAALEgAACxIB0t1+/AAAAB50RVh0U29mdHdhcmUAQWRvYmUgRmlyZXdvcmtzIENTNS4xqx9I6wAAAsVJREFUOI2t02lXElEcgHFtt2wxtxa7qIRiISBqZUibmWalpUSh4RaQYbm0TAkoSgZpM7hgLpOhqIGiBhoZuGBUFJUfqtmg5VUves5hhjn/37nnDocbEvJ/2hzxVKlWKtVqNXaD8K/YJ2JTcB4eXVFa8XelFdHh1HxLnVDRjKXRapp/SyGs207M18cIlbBei8D36yBY+ysEuhqzDgc7FApYr4cfcQGIP6PVajQaEuhhhWI3DmKlEILAqkQQnwAAP7iGXo9A0lgcRNWq+pCRPJDAoDNooBYmp1hIs/QlDvYoWmF4hAUYdDqDDoRmGHsa0SMI0qepIUAUBVKYTCYdJHJZLBa/tB9GYLiVXGH/PRIw2Wz2QUDFx0Xr3fc4WDPMLU2ZWSCVw+akMRlYTGwv18bMC3OGNRx8Nyz98H3LBOyMdCwOh5OenpEGeH6ff6njKw6+GBZ8Xn8myBBkBxJkgWSv17fQToKOeZ9nlQeyTggEJ3LIUsFJ/4p3vv0TDj62z3hXMJCdk59zPAXvELbX5x7nykwQeNweHjh1saAojXqJ5IZVp9M92fKBBJNuJwbyi4tKjpLzpIZVu93hnlQRYLkFAy4uKCwpFh0L/A4XXNN255hqmQRTToeLBy6XiUTFBXiFp+NBjcvmoMCiasxhd+WBXJlEIqkkkhUCrsNmN0OLAWBzNIJEmaySSiYBSRbL9Cj0jgSjdpvFygOHb96henge8K0m2yAFoEG7xTQ+mAxoR3LPEWF7bbSaLAGganNMmNDhF2dpgXcAtBvjKGptI8GufumEBUWNxuGmqiuXiK43DRtRE1oF78RB6L56aY/JhBp7e7u6nmF1DQz0Go1oj7R+byjxvw97+/iW/LZcXl0uFouwxOKy8mp5tfjBmzDq5GyLm309NNTdqdM9IdLpOru7X83GbQ2evQ2Rn//oAH6J3PhP5/on/FlVMstPiUYAAAAASUVORK5CYII=";



	if(isset($_POST['imgData'])){
		// accept image data encoded

		$data = $_POST['imgData'];

		$data = explode(",",$data,2);
		$type = strpos ( $data[0], "/")+1;
		$type = substr($data[0],$type, strpos ( $data[0], ";",$type)-$type);

		$save_path = "test/test.$type";

		$data = base64_decode($data[1]);

		$r = file_put_contents ( $save_path , $data);

		exit($r);

		$im = imagecreatefromstring($data);

		

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


	}
?>
<?php
/**
 * Created by PhpStorm.
 * User: chenchen
 * Date: 2018/6/3
 * Time: 19:07
 */
session_start();
$severname = 'localhost';
$username = 'root';
$pass = 'admin';
$dbname = 'labelattr';
$user=$psw="";
$uid = $_SESSION["userId"];
$attr = $_SESSION["attr"];
$imgids = $_SESSION['imgids'];
$row_capacity = $_SESSION['row_capacity'];
$num_rows = $_SESSION['num_rows'];
$num_lst_row = $_SESSION['num_lst_rows'];
 
$conn = mysqli_connect($severname, $username, $pass, $dbname);
if ($conn->connect_error)
{
    die("connect error:" . $conn->connect_error);
}
$conn->query("set names utf8");
/*
print_r($_SESSION);
print_r($_POST);
echo"<br>";
 */

/*
if($num_rows_form<>$num_rows_session)
	echo "<script>alert('No. of forms: $num_rows_form does not equal No. of session: $num_rows_session. Check agian please.'); history.go(-1);</script>";
 */

for($k = 1; $k <= $num_rows; $k++)
{
	if($k == $num_rows)
		$row_capacity_temp = $num_lst_row;
	else
		$row_capacity_temp = $row_capacity;
	for($i = 1; $i <= $row_capacity_temp; $i++)
	{
		$temp_idx = ($k-1)*$row_capacity + $i - 1;
		$groupname = sprintf("group%04d", $temp_idx);
		//var_dump($temp_idx, $groupname);
		//var_dump(empty($_POST[$groupname]));
		//echo "<br>";
		//if(empty($_POST[$groupname]))
		if(False)
			continue;
		else
		{
			$label_value = $_POST[$groupname];
			$imgid = $imgids[$temp_idx];
			date_default_timezone_set('Asia/Shanghai');
			$label_time = date('Y-m-d H:i:s');
			$sql_userlabel_update = "update user_label set label_time='$label_time', label_value='$label_value' where(userid='$uid' and imgid='$imgid' and attr='$attr');";
			$sql_imglabel_update = "update img_label set $attr='$label_value' where(Id='$imgid');";
			/*
			echo $sql_userlabel_update;
			echo $sql_imglabel_update;
			echo "<br>";
			 */
			if((($conn->query($sql_userlabel_update)) and ($conn->query($sql_imglabel_update))) == TRUE)
			{
				//echo "imgid = $imgid Successfully update.";
				//echo "<br>";
			}
			else
			{
				echo "<script>alert('update sql error!'); history.go(-1);</script>";
			}
		}
	}
}

echo "<script> window.location.assign(\"labelattr.php\");</script>";
?>

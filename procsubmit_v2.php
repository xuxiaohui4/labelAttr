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
$imgid_value_0 = array();
$imgid_value_1 = array();

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
		# get the current image's id and label_value
		$imgid = $imgids[$temp_idx];
		$label_value = $_POST[$groupname];
		if ($label_value == 0)
		{
			$imgid_value_0[] = $imgid; 
		}
		else
		{
			$imgid_value_1[] = $imgid;
		}
	}
}

# update database by batch
date_default_timezone_set('Asia/Shanghai');
$label_time = date('Y-m-d H:i:s');
$imgid_value_0_tuple = implode(',', $imgid_value_0);
$imgid_value_1_tuple = implode(',', $imgid_value_1);
var_dump($imgid_value_0_tuple);
var_dump($imgid_value_1_tuple);

# update label_value 0 items
$sql_userlabel_update = "update user_label set label_time='$label_time', label_value='0' where(imgid in ($imgid_value_0_tuple) and attr='$attr');";
$sql_imglabel_update = "update img_label set $attr='0' where Id in ($imgid_value_0_tuple);";
echo $sql_userlabel_update;
echo $sql_imglabel_update;
echo "<br>";

if(sizeof($imgid_value_0) != 0)
{
	if((($conn->query($sql_userlabel_update)) and ($conn->query($sql_imglabel_update))) == TRUE)
	{
		echo "imgid = $imgid_value_0_tuple value = 0 Successfully update.";
		echo "<br>";
	}
	else
	{
		echo "update sql failed!";
		echo "<script>alert('update sql error!'); history.go(-1);</script>";
	}
}

# update label_value 1 items
$sql_userlabel_update = "update user_label set label_time='$label_time', label_value='1'  where(imgid in ($imgid_value_1_tuple) and attr='$attr');";
$sql_imglabel_update = "update img_label set $attr='1' where Id in ($imgid_value_1_tuple);";
echo $sql_userlabel_update;
echo $sql_imglabel_update;
echo "<br>";
if(sizeof($imgid_value_1) != 0)
{
	if((($conn->query($sql_userlabel_update)) and ($conn->query($sql_imglabel_update))) == TRUE)
	{
		echo "imgid = $imgid_value_1_tuple  value = 1 Successfully update.";
		echo "<br>";
	}
	else
	{
		echo "updata sql failed!";
		echo "<script>alert('update sql error!'); history.go(-1);</script>";
	}
}

echo "<script> window.location.assign(\"labelattr.php\");</script>";
?>

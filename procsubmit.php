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
$temp_imgids = $_SESSION['imgids'];
$row_capacity = $_SESSION['row_capacity'];
$num_rows = $_SESSION['num_rows'];
$num_lst_row = $_SESSION['num_lst_rows'];
 
$conn = mysqli_connect($severname, $username, $pass, $dbname);
if ($conn->connect_error)
{
    die("connect error:" . $conn->connect_error);
}
$conn->query("set names utf8");
print_r($_SESSION);
echo '<br>';
print_r($_POST);
echo"<br>";
echo"<br>";

/*
if($num_rows_form<>$num_rows_session)
	echo "<script>alert('No. of forms: $num_rows_form does not equal No. of session: $num_rows_session. Check agian please.'); history.go(-1);</script>";
 */

# get the data from labelattr.php, store them into arrays so as to import data into mysql as fast as possible
$userlabel_update_columns = array("label_time", $attr);
$imglabel_update_columns = array($attr);
$userlabel_update_items = array();
$imglabel_update_items = array();
for($i=0; $i<count($userlabel_update_columns); $i++){
	$userlabel_update_items[$userlabel_update_columns[$i]] = null;
}
for($i=0; $i<count($imglabel_update_columns); $i++){
	$imglabel_update_items[$imglabel_update_columns[$i]] = null;
}

for($k = 1; $k <= $num_rows; $k++)
{
	if($k == $num_rows)
		$row_capacity_temp = $num_lst_row;
	else
		$row_capacity_temp = $row_capacity;
	for($i = 1; $i <= $row_capacity_temp; $i++)
	{
		$temp_idx = ($k-1)*$row_capacity + $i - 1;
		$temp_imgid = $temp_imgids[$temp_idx];

		# three standard var name
		$draw_list= sprintf("draw_list%04d", $temp_idx);
		$text_box = sprintf("text_box%04d", $temp_idx);
		$radio = sprintf("radio%04d", $temp_idx);

		$temp_label_value = $_POST[$draw_list].$_POST[$text_box];
		$userlabel_update_items[$attr][$temp_imgid] = $temp_label_value;
		$imglabel_update_items[$attr][$temp_imgid] = $temp_label_value;
	}
}

date_default_timezone_set('Asia/Shanghai');
$label_time = date('Y-m-d H:i:s');
$userlabel_update_items["label_time"] = $label_time;

echo "userlabel_update_itmes: ";
var_dump($userlabel_update_items);
echo "<br>";
echo "imglabel_update_items: ";
var_dump($imglabel_update_items);

$sql_userlabel_update = "update user_label set ";
# concat the userlabel sql command
$userlabel_imgid_array = array();
for($i=0; $i<count($userlabel_update_columns); $i++){
	$temp_column = $userlabel_update_columns[$i];
	if(is_array($userlabel_update_items[$temp_column])){
		$sql_userlabel_update .= "label_value = case imgid ";
		foreach($userlabel_update_items[$temp_column] as $key => $value){
			$sql_userlabel_update .= "when ".$key." then '".$value."' ";
			$userlabel_imgid_array[] = $key;
		}
		$sql_userlabel_update .= "end ";
	}
	else
		$sql_userlabel_update .= $temp_column."='".$userlabel_update_items[$temp_column]."', "; 
}
$sql_userlabel_update .= "where imgid in (".implode(',', $userlabel_imgid_array).") and attr='".$attr."';";
echo "<br>".$sql_userlabel_update;

$sql_imglabel_update = "update img_label set ";
# concat the imglabel sql command
$imglabel_id_array = array();
for($i=0; $i<count($imglabel_update_columns); $i++){
	$temp_column = $imglabel_update_columns[$i];
	if(is_array($imglabel_update_items[$temp_column])){
		$sql_imglabel_update .= $temp_column." = case Id ";
		foreach($imglabel_update_items[$temp_column] as $key => $value){
			$sql_imglabel_update .= "when ".$key." then '".$value."' ";
			$imglabel_imgid_array[] = $key;
		}
		$sql_imglabel_update .= "end ";
	}
	else
		$sql_imglabel_update .= $temp_column." ='".$imglabel_update_items[$temp_column]."' "; 
}
$sql_imglabel_update .= "where Id in (".implode(',', $imglabel_imgid_array).");";
echo "<br>".$sql_imglabel_update;

# update database by batch
if((($conn->query($sql_userlabel_update)) and ($conn->query($sql_imglabel_update))) == TRUE)
{
	echo "<br>imgid = ".implode(',', $userlabel_imgid_array)." update successfully.";
	echo "<br>";
}
else
{
	echo "update sql failed!";
	#echo "<script>alert('update sql error!'); history.go(-1);</script>";
	}

echo "<script> window.location.assign(\"labelattr.php\");</script>";
?>

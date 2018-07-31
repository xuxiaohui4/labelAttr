<!DOCTYPE html>
<style>
/* total page style */
.container{
    width:1400px;
    margin:10px 10px 10px 10px;
}
/* table carry the images and their text-boxs, draw-lists, radios*/
.table{
    width:90%;
    background-color:#F0F8FF;
    margin:10px 5% 10px;
}
/* card-deck carray a row of the table */
.card-deck{
    width:96%;
    margin:10px 2% 10px;
    float:left;
}
/* each image-box style */
.card{
    width:24%;
    margin:0% 0.13% 0%;
    background-color:#FFFFE0;
    border-style:solid;
    border-width:thin;
    float:left;
}
/* image style */
.image{
    width:95%;
    height:100px;
    margin:5px 2.5% 0%;
}
/* draw-list style */
.draw-list{
    width:20%;
}
/* text-box style */
.text-box{
    width:100%
}
/* radio style */
.radio{
    width:70px;
    height:50px;
    margin:0% 0% 0% 10%;
}
</style>
<html lang="en">
<head>
    <title>Label Attribute</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
    -->
</head>
<body>
<div class="container">
    <?php
    header("Content-Type: text/html;charset=utf-8");
    session_start();
    $attrs = array('carColor' => array('未知','其他','黑色','白色','蓝色','绿色','红色','黄色','紫色','金色香槟色','棕色咖啡色','浅灰色银色','深灰色'), 
                   'carDirection' => array('未知','其他','前','右前','右','右后','后','左后','左','左前'),
                   'carType' => array('未知','其他','轿车','SUV','商务车','小型客车','轻客','微面','皮卡','大型客车','卡车'),
		   'plateType' => array('未知','其他','大型汽车','小型汽车','使馆汽车','领馆汽车','境外汽车','外籍汽车','普通摩托车','轻便摩托车','使馆摩托车','领馆摩托车','境外摩托车','外籍摩托车','低速车','拖拉机','挂车','教练汽车','教练摩托车','临时入境汽车','临时入境摩托车','临时行驶车','警用汽车','警用摩托车','原农机','香港入i出境','澳门入出境','武警','军队'),
		   'squarePlate' => array('有方牌','无方牌'),
		   'plateNum' => array('京', '津', '冀', '晋', '内蒙古', '辽', '吉', '黑', '沪', '苏', '浙', '皖', '闽', '赣', '鲁', '豫', '鄂', '湘', '粤', '桂', '琼', '川', '贵', '云', '渝', '藏', '陕', '甘', '青', '宁', '新', '港', '澳', '台'));

    # connect to the mysql service 
    $severname = 'localhost';
    $username = 'root';
    $pass = 'admin';
    $dbname = 'labelattr';
    $user=$psw="";
    $conn = mysqli_connect($severname, $username, $pass, $dbname);
    if ($conn->connect_error)
    {
        die("connect error:" . $conn->connect_error);
    }
    $conn->query("set names utf8");

    # get $_SESSION['userId', 'attr', 'username']
    $uid = $_SESSION["userId"];
    $att = $_SESSION['attr'];
    $row_capacity = 4;
    $form_rows_num = 10;
    $total_images_pertime = $row_capacity*$form_rows_num;
    # table user_label should be built in advance without attr assigned kind but not value
    $sql_user_label = "SELECT imgid FROM user_label where (userid='$uid' AND attr='$att' AND label_value is NULL) order by imgid ASC limit $total_images_pertime";
    $data_user_label = $conn->query($sql_user_label);
    $num_user_label = mysqli_num_rows($data_user_label);
    if ($num_user_label == 0)
    {
	echo "<script>alert('No attribute to write. Please rechoose attributes!'); history.go(-1);</script>";
    }
    
    # get the images data by imgids and show it for data-makers to write
    $sql_img_label = "SELECT Id, image_name, $att, data_source FROM img_label where(Id in (select x.imgid from($sql_user_label) as x))";
    $data_img_label = $conn->query($sql_img_label);
    $num_img_label = mysqli_num_rows($data_img_label);
    # assert num_img_label equals num_user_label
    if ($num_img_label<>$num_user_label)
    {
	echo "<script>alert('No. of user_label rows: $num_img_label does not equal No. of img_label rows: $num_user_label. MySQL serious error!!!'); history.go(-1);</script>";
    }
    if ($data_img_label->num_rows > 0) {
        // 输出数据
	while($temp= $data_img_label->fetch_assoc()) 
	{
            $query_items[] = $temp;
        }
    }
    //echo "query_items: ";
    //var_dump($query_items);
    echo "<br>";
    $num_rows = (int)(count($query_items) / $row_capacity + 1);
    $num_lst_row = count($query_items) % $row_capacity;
    $imgids = array();

    # read file and store it in array
    $file = '/var/www/html/dataset.txt';
    $file_content = file_get_contents($file);
    $plateNumlist = explode("\n", $file_content);
    $plateNumDir = array();

    for($i=0; $i < (count($plateNumlist)-1); $i++)
    {
	    $value = $plateNumlist[$i]; 
	    $key = explode(' ', $value); 
	    $plateNumDir[$key[0]] = $key[1]; 
    }
    //echo "total Num. of plateNumDir: ".count($plateNumDir)."<br>";

    #form title
    echo '<div class="table">';
    echo "<h2>Label attribute: ".$att."</h2>";
    echo "<h3>username: ".$_SESSION['username']."&nbsp&nbsp imageid: ".$query_items[0]['Id'].'-'.end($query_items)['Id']."</h3>";
    # indicate user the task info
    $temp_sql = "select Id from user_label where userid=".$uid." and attr='".$att."';";
    $data_user_label_temp = $conn->query($temp_sql);
    $num_total_data= mysqli_num_rows($data_user_label_temp);
    $temp_sql = "select Id from user_label where userid='$uid' and attr='".$att."' and label_value is not NULL;";
    $data_user_label_temp = $conn->query($temp_sql);
    $num_label_data= mysqli_num_rows($data_user_label_temp);
    echo "<h4>Progress instruction: ".$num_label_data."/".$num_total_data."<h4>";
    echo '</div>';

    #form area
    echo '<form action="procsubmit.php" method="post">';
    for ($k = 1; $k <= $num_rows; $k++)
    {
	# width and height below are the params of each row, they should not be less than image width and height(involve the height of buttons)
	echo '<div class="card-deck">';
	if($k == $num_rows)
		$row_capacity_temp = $num_lst_row;
	else
		$row_capacity_temp = $row_capacity;
	for($i=1; $i <= $row_capacity_temp; $i++)
	{
	    #each image have an card area, width and height is as below
	    echo '<div class="card">';
	    $temp_idx = ($k-1)*$row_capacity+$i-1;
	    $img_name = $query_items[$temp_idx]['image_name'];
	    $temp_imgid = $query_items[$temp_idx]['Id'];
	    $imgids[] = $temp_imgid;
	    $img_readsrc = '../'.str_ireplace('\'','/',$query_items[$temp_idx]['data_source']).'/'.$img_name;
	    echo '<img class="image" src='.$img_readsrc.' ><br>';
	    $draw_list= sprintf("draw_list%04d", $temp_idx);
	    $text_box = sprintf("text_box%04d", $temp_idx);
	    $radio = sprintf("radio%04d", $temp_idx);
	    $imgsrc_dir = str_ireplace('\'','/',$query_items[$temp_idx]['data_source']).'/'.$img_name;
	    $platePred = $plateNumDir[$imgsrc_dir];
	    
	    //this part is the form of draw-list
	    $attnum = count($attrs["$att"]);
	    echo '<div class="draw-list">';
	    echo '<select name='.$draw_list.' style="font-size:25px;width:150%">';
	    echo '<option value=""></option>';
	    for ($j = 1; $j <= $attnum; $j++)
	    {
		    if($attrs[$att][($j-1)] == substr($platePred, 0, 3))
			    echo '<option value='.$attrs[$att][($j-1)].' selected="selected">'.$attrs[$att][($j-1)].'</option>';
		    else
			    echo '<option value='.$attrs[$att][($j-1)].'>'.$attrs[$att][($j-1)].'</option>';
	    }
	    echo '</select>';
	    echo '</div>';

	    // this part is the text-box
	    $numPred = substr($platePred, 3);
	    echo '<div class="text-box">';
	    echo '<input type="text" name='.$text_box.' value='.$numPred.' style="font-size:35px;width:50%">';
	    echo '</div>';

	    //this is the button forum
	    /*
	    for ($j = 1; $j <= $attnum; $j++)
	    {
		    echo '<label><input type="radio" name='.$radio.' value='.($j-1).' checked="checked">'.$attrs[$att][($j-1)].'</input></label>';
	    }
	     */
	    echo '</div>';

	}
	echo '</div>';
    }

    $_SESSION['imgids'] = $imgids;
    $_SESSION['row_capacity'] = $row_capacity;
    $_SESSION['num_rows'] = $num_rows;
    $_SESSION['num_lst_rows'] = $num_lst_row;
    
    #this is the submit button
    echo '<div class="submit-button" style="width:10%;margin:0% 38% 1%;float:left;">';
    echo '<input type="submit" value="提交">';
    echo '</div>';
    echo '</form>';
    ?>
</div>
</body>
</html>

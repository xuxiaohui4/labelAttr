<!DOCTYPE html>
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
<div class="container" style="width:1400px;margin:10px 10px 10px 10px;">
    <?php
    header("Content-Type: text/html;charset=utf-8");
    session_start();
    $attrs = array('carColor' => array('未知','其他','黑色','白色','蓝色','绿色','红色','黄色','紫色','金色香槟色','棕色咖啡色','浅灰色银色','深灰色'), 
                   'carDirection' => array('未知','其他','前','右前','右','右后','后','左后','左','左前'),
                   'carType' => array('未知','其他','轿车','SUV','商务车','小型客车','轻客','微面','皮卡','大型客车','卡车'),
		   'plateType' => array('未知','其他','大型汽车','小型汽车','使馆汽车','领馆汽车','境外汽车','外籍汽车','普通摩托车','轻便摩托车','使馆摩托车','领馆摩托车','境外摩托车','外籍摩托车','低速车','拖拉机','挂车','教练汽车','教练摩托车','临时入境汽车','临时入境摩托车','临时行驶车','警用汽车','警用摩托车','原农机','香港入i出境','澳门入出境','武警','军队'),
	           'squarePlate' => array('有方牌','无方牌'));

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
    $form_rows_num = 50;
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
            $imgnames[] = $temp;
        }
    }
    $num_rows = (int)(count($imgnames) / $row_capacity + 1);
    $num_lst_row = count($imgnames) % $row_capacity;
    $imgids = array();

    #form title
    echo '<div class="container_name" style="width:90%;background-color:#F0F8FF;margin:10px 5% 10px;">';
    echo "<h2>Label attribute: ".$att."</h2>";
    echo "<h3>username: ".$_SESSION['username']."&nbsp&nbsp imageid: ".$imgnames[0]['Id'].'-'.end($imgnames)['Id']."</h3>";
    # indicate user the task info
    $temp_sql = "select Id from user_label where userid='$uid';";
    $data_user_label_temp = $conn->query($temp_sql);
    $num_total_data= mysqli_num_rows($data_user_label_temp);
    $temp_sql = "select Id from user_label where userid='$uid' and label_value is not NULL;";
    $data_user_label_temp = $conn->query($temp_sql);
    $num_label_data= mysqli_num_rows($data_user_label_temp);
    echo "<h4>Progress instruction: ".$num_label_data."/".$num_total_data."<h4>";
    echo '</div>';

    #form area
    echo '<form action="procsubmit_v2.php" method="post">';
    for ($k = 1; $k <= $num_rows; $k++)
    {
	# width and height below are the params of each row, they should not be less than image width and height(involve the height of buttons)
	echo '<div class="card-deck" style="width:96%;margin:10px 2% 10px;float:left;">';
	if($k == $num_rows)
		$row_capacity_temp = $num_lst_row;
	else
		$row_capacity_temp = $row_capacity;
	for($i=1; $i <= $row_capacity_temp; $i++)
	{
	    #each image have an card area, width and height is as below
	    echo '<div class="card" style="width:24%;margin:0% 0.13% 0%;background-color:#FFFFE0;border-style:solid;border-width:thin;float:left">';
	    $temp_idx = ($k-1)*$row_capacity+$i-1;
	    $img_name = $imgnames[$temp_idx]['image_name'];
	    $imgids[] = $imgnames[$temp_idx]['Id'];
	    $src = '../'.str_ireplace('\'','/',$imgnames[$temp_idx]['data_source']).'/'.$img_name;
	    echo '<img class="card-img-left" src='.$src.' style="width:95%;height:300px;margin:5px 2.5% 0%"><br>';
	    $attnum = count($attrs["$att"]);
	    $groupname = sprintf("group%04d", $temp_idx);

	    /* this part is the form of draw-list, and is not adopt
	    echo '<div class="button" style="width:50%;margin:0% 25% 0%;">';
	    echo '<select name='.$groupname.'>';
	    echo '<option value=""></option>';
		    for ($j = 1; $j <= $attnum; $j++)
		    {
			    echo '<option value='.($j-1).'>'.$attrs[$att][($j-1)].'</option>';
		    }
	    echo '</select>';
	    echo '</div>';
	    */

	    //this is the button forum
	    for ($j = 1; $j <= $attnum; $j++)
	    {
		    echo '<label><input type="radio" name='.$groupname.' value='.($j-1).' style="width:70px;height:50px;margin:0% 0% 0% 10%" checked="checked">'.$attrs[$att][($j-1)].'</input></label>';
	    }
	    echo '</div>';
	}
	echo '</div>';
    }

    $_SESSION['imgids'] = $imgids;
    $_SESSION['row_capacity'] = $row_capacity;
    $_SESSION['num_rows'] = $num_rows;
    $_SESSION['num_lst_rows'] = $num_lst_row;
    
    #this is the submit button
    echo '<div class="card-deck" style="width:10%;margin:0% 38% 1%;float:left;">';
    echo '<input type="submit" value="提交">';
    echo '</div>';
    echo '</form>';
    ?>
</div>
</body>
</html>

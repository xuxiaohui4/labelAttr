<?php
/**
 * Created by PhpStorm
 * Modified by xiaohui
 * User: xiaohui
 * Date: 2018/7/10
 */
session_start();
parse_str(file_get_contents("php://input"), $_POST);
$severname = 'localhost';
$adminname = 'root';
$pass = 'admin';
$dbname = 'labelattr';
$user=$psw="";
$conn = mysqli_connect($severname, $adminname, $pass, $dbname);
if ($conn->connect_error)
{
    die("connect error:" . $conn->connect_error);
}

# get $_POST['username', 'passwd', 'attrs'] from index.html
$username = $_POST['username'];
$psw = $_POST['passwd'];


$sql = "SELECT Id, passwd FROM users where (username='$username')";
$data = $conn->query($sql);

$num = mysqli_num_rows($data);
$row = mysqli_fetch_array($data);
#var_dump($row);
if ($num >= 1)
{
    $psw_right = $row['passwd'];
    if ($psw_right == $psw)
    {
        $_SESSION['userId'] = $row['Id'];
        $_SESSION['attr'] = $_POST['attrs'];
        $_SESSION['username'] = $username;
        echo "<script> window.location.assign(\"labelattr.php\");</script>";
    }
    else
    {
        echo "<script>alert('password error!');window.history.go(-1)</script>";
    }
}
else
{
    echo "<script>alert('unknown user!');window.history.go(-1)</script>";
}
?>

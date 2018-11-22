<?php
require_once("db.php");
if(!isset($_SESSION))
{
    session_start();

}
$user_id=$_SESSION['id'];
$query = "SELECT * FROM `admin` WHERE id = ?";
$res = getData($con,$query,[$user_id]);
if(count($res) != 1)
{
    header('Location:login.php');
    die();
}

?>
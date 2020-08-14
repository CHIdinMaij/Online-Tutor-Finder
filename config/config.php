<?php
ob_start();//turns on output buffer to work with data which is saved at Output buffer
//to retain all valid formatted credentials
session_start();
$timezone = date_default_timezone_set("Asia/Kolkata");
$con = mysqli_connect("localhost","root","","social");

if(mysqli_connect_errno())
{
    echo "failed to connect" . mysqli_connect_errno();
}
?>
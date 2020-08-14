<?php
include("../../config/config.php");
include("../classes/User.php");
include("../classes/Post.php");

$limit = 9;
//number of post to be loaded per call
$posts = new Post($con,$_REQUEST['userLoggedIn']);
$posts->loadPostsFriends($_REQUEST,$limit);
?>
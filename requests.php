<?php 
include("includes/header.php");

?>

<div class="main_column" id="main_column">
    <h4>Friend Requests</h4>
    <?php 
    $query = mysqli_query($con,"SELECT * FROM friend_requests WHERE user_to='$userLoggedIn' ");
    //echo $userLoggedIn;
    if(mysqli_num_rows($query)==0)
    {
        echo 'No Requests At this Time';
    }
    else
    {
        while($row=mysqli_fetch_array($query))
        {
            $user_from = $row['user_from'];
            $user_from_obj = new User($con,$user_from);
            ?>
            <h5><?php echo $user_from_obj->getFirstAndLastName() . " sent You a Friend Request";?></h5>
            
            <?php
            $user_from_friend_array = $user_from_obj->getFriendArray();
            
            $accept_request_button = "accept_request" .$user_from;
            $ignore_request_button = "ignore_request" .$user_from;

            if(isset($_POST[$accept_request_button]))
            {
            
                $add_friend_query = mysqli_query($con,"UPDATE users SET friend_array=CONCAT(friend_array,'$user_from,') WHERE username='$userLoggedIn'");
                //echo $add_friend_query;
                $add_friend_query = mysqli_query($con,"UPDATE users SET friend_array=CONCAT(friend_array,'$userLoggedIn,') WHERE username='$user_from'");

                $delete_query = mysqli_query($con,"DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
                echo "You are Now Friends!!!!!";
               header("Location: requests.php");
            }
            if(isset($_POST[$ignore_request_button]))
            {
                $delete_query = mysqli_query($con,"DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
                echo "Request Ignored!!!!!";
                header("Location: requests.php");
            }
            ?>
            <form action="requests.php" method="POST">
            <input type="submit" name="accept_request<?php echo $user_from ;?>" id="accept_button" value="Accept">
            <input type="submit" name="ignore_request<?php echo $user_from ;?>" id="ignore_button" value="Ignore">

            </form>
            <?php
        }
    }
    ?>

</div>
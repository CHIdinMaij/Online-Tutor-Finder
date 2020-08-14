<?php
include("includes/header.php");

$message_obj = new Message($con,$userLoggedIn);
//to reset all the session variables
//session_destroy();
if(isset($_GET['profile_username']))
{
    //making available the data  whose profile we are currently in 
    $username = $_GET['profile_username'];
    $user_details_query = mysqli_query($con,"SELECT * FROM users WHERE username='$username'");
    $user_array = mysqli_fetch_array($user_details_query);
    $num_friends = (substr_count($user_array['friend_array'],","))-1;
}

if(isset($_POST['remove_friend']))
{
    $user = new User($con,$userLoggedIn);
    $user->removeFriend($username);
}
if(isset($_POST['add_friend']))
{
    $user = new User($con,$userLoggedIn);
    $user->sendRequest($username);
}
if(isset($_POST['respond_request']))
{
    header("Location: requests.php");
}

if(isset($_POST['post_message']))
{
    if(isset($_POST['message_body']))
    {
        $body = mysqli_real_escape_string($con,$_POST['message_body']);
        $date = date("Y-m-d H:i:s");
        $message_obj->sendMessage($username,$body,$date);
    }
    $link = '#profileTabs a[href="#messages_div"]';
    echo "<script>
    $(function(){
        $('".$link."').tab('show');
    });
    </script>
    ";
}
?>
<style>
    .wrapper
    {
        margin-left:0px;
        padding-left: 0px;
        
    }
</style>

<div class='profile_left'>
    <img src='<?php echo $user_array['profile_pic']; ?>' >
    <div class='profile_info'>
        <p><?php echo "Posts: ". $user_array['num_posts'];?></p>
        <p><?php echo "Likes: ". $user_array['num_likes'];?></p>
        <p><?php echo "Friends: ". $num_friends;?></p>
    </div>
    <form action="<?php echo $username;?>" method="POST">
    <?php $profile_user_object = new User($con,$username);
        if($profile_user_object->isClosed())
        {
            header("Location: user_closed.php");
        }

        $logged_in_user_obj = new User($con,$userLoggedIn);
        //checking if diff person is on anothers profile
        if($userLoggedIn!=$username)
        {
            //checking the person whom i am stalking is friend or not
            if($logged_in_user_obj->isFriend($username))
            {
                echo '<input type="submit" name="remove_friend" class="danger" value="Remove Friend" ><br>';
            }
            else if($logged_in_user_obj->didRecievedRequest($username))
            {
                echo '<input type="submit" name="respond_request" class="warning" value="Respond To Request" ><br>';

            }
            else if($logged_in_user_obj->didSendRequest($username))
            {
                echo '<input type="submit" name="" class="default" value="Request Send" ><br>';
            }
            else
            {
                echo '<input type="submit" name="add_friend" class="success" value="Add Friend" ><br>';
            }
        }
    ?>
    

    </form>
    <input type="submit" class="deep_blue" data-toggle="modal" data-target="#post_form" value="Post Something!">
        <?php 
            if($userLoggedIn!=$username)
            {
                echo '<div class="profile_info_bottom">';
                echo $logged_in_user_obj->getMutualFriends($username) . "mutual friends";
                echo '</div>';

            }
        ?>
</div>
        <div class="profile_main_column column">

        <ul class="nav nav-tabs"  id="profileTabs">
        <li  class="active"><a href="#newsfeed_div" aria-controls="newsfeed_div" role="tab" data-toggle="tab">NewsFeed  </a></li>
        <li ><a href="#messages_div" aria-controls="messages_div" role="tab" data-toggle="tab">Messages</a></li>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active" id="newsfeed_div">
                <div class="posts_area"></div>
                <img id='loading' src="assets/images/icons/loading.gif">
            </div>


            

            <div role="tabpanel" class="tab-pane fade" id="messages_div">
                
            <?php 
                
                $message_obj = new Message($con,$userLoggedIn);
                        echo "<h4> You and <a href='".$username."'>".$profile_user_object->getFirstAndLastName() . "</a></h4><hr><br>";
                        echo "<div class='loaded_messages' id='scroll_messages'>";
                        echo $message_obj->getMessages($username);
                        echo "</div>";
                
            ?>

                <div class="message_post">
                    <form action="" method="POST">
                            
                        {
                                <textarea name='message_body' id='message_textarea'></textarea>
                                <input type='submit' name='post_message' id='message_submit' value='Send'>
                        }
                        
                    </form>

                </div>

                <script>
                        var div = document.getElementById("scroll_messages");
                        div.scrollTop = div.scrollHeight;
                </script>


            </div>
        </div>

     
        </div>
       
      

        
        </div>
    


<!-- Modal -->
<div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Post Something</h4>
      </div>
      <div class="modal-body">
        <p>This will appear on the users profile page and also their newsfeed for your friends to see</p>
        
        <!here is the form from which we r gonna fetch data for post done in profile>
        <form class="profile_post" action="" methos="POST">
            <div class="form-group">
                <textarea class="form-control" name="post_body"></textarea>
                <input type="hidden" name="user_from" value="<?php echo $userLoggedIn;?> ">
                <input type="hidden" name="user_to" value="<?php echo $username;?> ">

            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" name="post_button" id="submit_profile_post">Post</button>
      </div>
    </div>
  </div>
</div>

<script>

    //Ajax is used to make database calls without reloading the page
    //values assigning from php variable to javascript variable
    var userLoggedIn = '<?php echo $userLoggedIn; ?>';
    var profileUsername = '<?php echo $username; ?>';
    $(document).ready(function()
    {
        $('#loading').show();
        //original ajax request for loading posts first
        $.ajax({
            //in below file all database related coding sohould be present
            url: "includes/handlers/ajax_load_profile_posts.php", 
            type: "POST",
            data: "page=1&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername, //we send it as attribute to above url file it can be send as string,object,array
            cache:false,
            //whenever ajax call returns some data it automatically get passed in to the below function
            //so below argument data is returned by url file we can name it other value
            success: function(data)
            {
                $('#loading').hide();
                $('.posts_area').html(data);
            }
        });

        $(window).scroll(function()
        {
            //alert('hello');

            var height = $('.posts_area').height(); //div containing posts
            var scroll_top = $(this).scrollTop();
            var page = $('.posts_area').find('.nextPage').val();
            var noMorePosts = $('.posts_area').find('.noMorePosts').val();
            if((document.body.scrollHeight==document.body.scrollTop + window.innerHeight)&& noMorePosts=='false')
            {
                $('#loading').show();

                var ajaxReq  =  $.ajax({
                    url: "includes/handlers/ajax_load_profile_posts.php",
                    type: "POST",
                    data: "page="+page+"&userLoggedIn=" + userLoggedIn+ "&profileUsername=" + profileUsername,
                    cache:false,

                    success: function(response)
                    {
                        $('.posts_area').find('.nextPage').remove();
                        $('.posts_area').find('.noMorePosts').remove();
                        $('#loading').hide();
                        $('.posts_area').append(response);
                    }
                });
            }//End if

            return false;
        });//End (window).scrollfunction()

    });
</script>
    <!coming from header.php file>

    </div>
    </body>
</html>
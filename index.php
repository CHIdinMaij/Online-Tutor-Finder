<?php
include("includes/header.php");

//to reset all the session variables
//session_destroy();

if(isset($_POST['post']))
{
    $post = new Post($con,$userLoggedIn);
    $post->submitPost($_POST['post_text'],'none');
    //after submitting post if we refresh it wants to resubmit the post 
    //this is general behavior of web browser 
    //we get a duplicate copy the post in database to prevent this
    //so we are redirecting it to index page explicitly
    header("Location: index.php");
}
?>

    <div class="user_details column">
        
        <a href="<?php echo $userLoggedIn; ?>"> <img src="<?php echo $user['profile_pic']; ?> " > </a>
        <div class="user_details_left_right">
                <a href='<?php echo $userLoggedIn; ?>' >
                <?php
                echo $user['first_name']. ' '. $user['last_name'];
                ?>
                </a>
                <br>
                <?php
                echo "Posts: ". $user['num_posts']."<br>";
                echo "Likes: ". $user['num_likes'];
                ?>
        </div>

    </div>    
        <div class="main_column column">
            <form class="post_form" action="index.php" method="POST">
            <textarea name="post_text" id="post_text" placeholder="Got something to say?"></textarea>
            <input type="submit" name="post" id="post_button" value="POST">
            </form>

            <?php
            //$user_obj = new User($con,$userLoggedIn);
            //echo $userLoggedIn;
            //echo $user_obj->getFirstAndLastName();
            //$post = new Post($con,$userLoggedIn);
            //$post->loadPostsFriends();
            ?>
            <div class="posts_area"></div>
            <img id='loading' src="assets/images/icons/loading.gif">
        </div>

        <div class="user_details column">
            <h4>Trendings</h4>
        <div class="trends">
            <?php
            $query = mysqli_query($con,"SELECT * FROM trends ORDER BY hits DESC LIMIT 5");
            foreach($query as $row)
            {
                $word = $row['title'];
                $word_dot = strlen($word)>=14?"...":"";
                $trimmed_word = str_split($word,14);
                $trimmed_word = $trimmed_word[0];
                echo "<div style='padding:1 px;width:250px;margin-bottom:5px;'>";
                echo $trimmed_word.$word_dot;
                echo "<br></div>";
            }
            ?>

        </div>
    </div>


    
    <!coming from header.php file>
    </div>



    <script>

        //Ajax is used to make database calls without reloading the page
        //values assigning from php variable to javascript variable
        var userLoggedIn = '<?php echo $userLoggedIn; ?>';
        $(document).ready(function()
        {
            $('#loading').show();
            //original ajax request for loading posts first
            $.ajax({
                //in below file all database related coding sohould be present
                url: "includes/handlers/ajax_load_posts.php", 
                type: "POST",
                data: "page=1&userLoggedIn=" + userLoggedIn, //we send it as attribute to above url file it can be send as string,object,array
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
                        url: "includes/handlers/ajax_load_posts.php",
                        type: "POST",
                        data: "page="+page+"&userLoggedIn=" + userLoggedIn,
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

    </body>
</html>
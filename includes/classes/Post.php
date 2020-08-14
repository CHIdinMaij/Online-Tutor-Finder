<?php

class Post{

    private $user_obj;
    private $con;

    //constructor for User class
    public function __construct($con,$user)
    {
        $this->con = $con;
        $this->user_obj = new User($con,$user);
    }
    public function submitPost($body,$user_to)
    {
        $body = strip_tags($body); //removes html tags
        $body = mysqli_real_escape_string($this->con,$body);
        //to maintain all line breaks given by user
        // here we are searching '\r\n' carriage return follwed by a new line
        //a enter is represented by '\r\n';
        $body = str_replace('\r\n','\n',$body);
        //replaces newline('\n') with break(<br>)
        $body = nl2br($body);
        //if someone post only with blank spaces it should be avoided
        $check_empty = preg_replace('/\s+/','',$body);
        if($check_empty!="")
        {
            //checking is it a link or not
            $body_array = preg_split('/\s+/',$body);
            
            foreach($body_array as $key => $value)
            {
                if(strpos($value,"www.youtube.com/watch?v=")!== false)
                {
                    $link = preg_split("!&!",$value);
                    $value = preg_replace("!watch\?v=!","embed/",$link[0]);
                    $value = "<br><iframe width=\'420\' height=\'315\' src=\'".$value."\'></iframe><br>";
                    $body_array[$key] = $value;
                }
            }
            $body = implode(" ",$body_array);
            
            ///current data and time
            $date_added= date("Y-m-d H:i:s");
            //getting username;
            $added_by = $this->user_obj->getUsername();
            //if user in on own profile ,user to is 'none'
            if($user_to==$added_by)
            {
                $user_to='none';
            }
            //insert post
            $query = mysqli_query($this->con,"INSERT INTO posts VALUES('','$body','$added_by','$user_to','$date_added','no','no','0')");
            //storing post id to update likes and comments
            $returned_id = mysqli_insert_id($this->con);
            //latest Id on which operation is done

            //Insert Notification
            if($user_to!='none')
            {
                //Those who is the cause of the event notification object is will be created 
                //using the persons name then will send to appropriate public mentioning 
                //him in the arguments of insertNotification function
                $notification = new Notification($this->con,$added_by);
                $notification->insertNotification($returned_id,$user_to,"profile_post");
            }

            //update post count
            $num_posts = $this->user_obj->getNumPosts();
            $num_posts++;
            $update_query = mysqli_query($this->con,"UPDATE users SET num_posts='$num_posts' WHERE username='$added_by' ");
            $StopWord = "";
            $stopwords = preg_split("/[\s,]+/",$StopWord);
            
            $no_punctuation = preg_replace("/[^a-zA-Z 0-9] + /","",$body);
            if(strpos($no_punctuation,"height")===false && strpos($no_punctuation,"width")===false &&
            strpos($no_punctuation,"http")===false)
            {
                $no_punctuation = preg_split("/[\s,]+/",$no_punctuation);

                foreach($stopwords as $value)
                {
                    foreach($no_punctuation as $key => $value2)
                    {
                        if(strtolower($value)==strtolower($value2))
                        $no_punctuation[$key]="";
                    }
                }
                foreach($no_punctuation as $value)
                {
                    $this->calculateTrend(ucfirst($value));
                }
            } 
        }

    }

    public function calculateTrend($term)
    {
        if($term!="")
        {
            $query = mysqli_query($this->con,"SELECT * FROM trends WHERE title='$term' ");
            if(mysqli_num_rows($query)==0)
            {
                $insert_query = mysqli_query($this->con,"INSERT INTO trends VALUES('$term','1')");
            }
            else
            {
                $insert_query = mysqli_query($this->con,"UPDATE  trends SET hits=hits+1 WHERE title='$term'");
            }
        }
    }

    public function loadPostsFriends($data,$limit)
    {
        $page = $data['page'];
        $userLoggedIn = $this->user_obj->getUsername();
        //echo $userLoggedIn;
        if($page==1)
        {
            $start = 0;
        }
        else
        {
            $start = ($page-1)*$limit;
        }
        $str="";//string to return ;
        $data_query = mysqli_query($this->con,"SELECT * FROM posts WHERE deleted='no' ORDER BY id DESC");
        
        if(mysqli_num_rows($data_query)>0)
        {

            $num_iteration = 0;//number of results checked out not necesarrily posted
            $count = 0;
        
            while($row=mysqli_fetch_array($data_query))
            {
                $id= $row['id'];
                //echo $id." ";

                $body = $row['body'];
                //echo $body;
                $added_by = $row['added_by'];
                $date_time = $row['date_added'];
                //preparing user_to string
                $check_comments = mysqli_query($this->con,"SELECT * FROM comments WHERE post_id='$id'");
                $check_comments_num = mysqli_num_rows($check_comments);
                if($row['user_to']=="none")
                {
                    $user_to = "";
                }
                else
                {
                    $user_to_obj = new User($this->con,$row['user_to']);
                    $user_to_name = $user_to_obj->getFirstAndLastName();
                    //to be resolved}
                    $user_to ="to". "<a href='" . $row['user_to'] ." '>".$user_to_name . "</a>";
                }
                
                //all post are coming from database so whose account is closed
                //we wont show their posts
                $added_by_obj = new User($this->con,$added_by);
                if($added_by_obj->isClosed())
                {
                    continue;
                }
                $user_logged_obj = new User($this->con,$userLoggedIn);
                /*if($user_logged_obj->isFriend($added_by)==false)
                {
                    continue;
                }*/
                $num_iteration++;
                if($num_iteration<$start) //starting point is yet not reached
                    continue;

                //Once limit is recahed(mentioned in ajaxloadposts) will break the loop

                if($count>$limit)
                {
                break;
                }
                else
                {
                    $count++;
                }
                                //danger php tags are broken just to insert some js

                ?>
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script> 

                <script>
                    function toggle<?php echo $id;?>()
                    {
                        //the event which is actually calling this function
                        var target = $(event.target);
                        if(!target.is("a")&&!target.is("button"))
                        {
                            var element = document.getElementById("toggleComment<?php echo $id; ?>");
                            if(element.style.display =='block')
                            element.style.display = 'none';
                            else
                            element.style.display='block';
                        }
                    
                    }

                   /* function postdel<?php echo $id;?>()
                    {
                        //the event which is actually calling this function
                       
                        var target = $(event.target);
                        if(!target.is("a"))
                        {
                            bootbox.confirm("Are you sure you want to delete this post? ",function(result)
                            {
                                

                            });
                        }
                    
                    }*/
                    
                    $(document).ready(function()
                    {
                        $('#post<?php echo $id;?>').on('click',function()
                        {
                          
                            bootbox.confirm("Are you sure you want to delete this post? ",function(result)
                            {
                                $.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id;?>", {result:result});
                                if(result)
                                {
                                    location.reload();
                                }
                            });
                        });
                    });

               
               
                    </script>
                <?php

                //adding delete button
                if($userLoggedIn==$added_by)
                {
                    
                    $delete_button = "<button class= 'delete_button btn-danger' id='post$id' >X</button>";
                }
                else
                {
                    $delete_button = "";
                }
                $user_details_query = mysqli_query($this->con,"SELECT first_name,last_name,profile_pic FROM users WHERE username='$added_by' ");
                $user_row = mysqli_fetch_array($user_details_query);
                $first_name = $user_row['first_name'];
                $last_name = $user_row['last_name'];
                $profile_pic = $user_row['profile_pic'];

                //Timeframe
                $date_time_row = date("Y-m-d H:i:s");
                $start_date = new DateTime($date_time);//Time of post
                $end_date = new DateTime($date_time_row);//Current time
                $interval = $start_date->diff($end_date);//difference bteween above two
                if($interval->y>=1)
                {
                    if($interval==1)
                    $time_message = "1" . "year ago ";
                    else
                    $time_message = $interval->y . "years ago";
                }
                else if($interval->m>=1)
                {
                    if($interval->d==0)
                    $days ="ago";
                    else if($interval->d==1)
                    $days = "day ago";
                    else
                    $days = $interval->d . "days ago";

                    if($interval->m==1)
                    {
                        $time_message = $interval->m . "month" .$days;
                    }
                    else
                    {
                        $time_message = $interval->m . "months" .$days;
                    }
                }
                else if($interval->d>=1)
                {
                    if($interval->d==1)
                    $time_message = "Yesterday";
                    else
                    $time_message = $interval->d . "days ago";
                }
                else if($interval->h>=1)
                {
                    if($interval->h==1)
                    $time_message = $interval->h. "hour ago";
                    else
                    $time_message = $interval->h . "hours ago";
                }
                else if($interval->i>=1)
                {
                    if($interval->i==1)
                    $time_message = $interval->i. "minute ago";
                    else
                    $time_message = $interval->i . "minutes ago";
                }
                else
                {
                    if($interval->s<30)
                    {
                    
                        $time_message =  "just now";
                    }
                    else
                    {
                        $time_message = $interval->s . "seconds ago";
                    }
                }
                

                $str .=  "<div class='status_post' onClick='javascript:toggle$id()' >
                            <div class='post_profile_pic'>
                            <img src='$profile_pic' width='50'>
                            </div>
                            <div class='posted_by' style='color:#ACACAC;'>
                            <a href='$added_by'>$first_name $last_name </a> $user_to &nbsp; &nbsp; &nbsp; &nbsp;
                            $time_message

                            $delete_button
                            </div>

                            <div id='post_body'>
                            $body<br>
                            </div>
                            <br>
                            <div class='newsfeedPostOptions'>
                            <p>
                            Comments($check_comments_num) 
                            </p>
                            <iframe src='like.php?post_id=$id' ></iframe>
                            
                            </div>
                            <div class='post_comment' id='toggleComment$id' style='display:none;' >
                                <iframe src='comment_frame.php?post_id=$id' id='comment_iframe'></iframe>
                            </div>
                            </div>
                            <hr>
                
                            ";
            
                
            }
            ?>
            <script>

            </script>
            <?php
            ?>
       

        
            <?php

            if($count>$limit)
            $str .= "<input type='hidden' class='nextPage' value='" .($page + 1) . "'>
            <input type='hidden' class ='noMorePosts' value='false'>";
            else
            {
                $str .= "
                <input type='hidden' class ='noMorePosts' value='true'>
                <p style='text-align: centre;'>No more Posts to show</p>";
            }

            

        }
        //here echo is equivalent to return the string
        echo $str;
    }
    public function loadProfilePosts($data,$limit)
    {
        $page = $data['page'];
        $profileUser = $data['profileUsername'];
        $userLoggedIn = $this->user_obj->getUsername();
        //echo $userLoggedIn;
        if($page==1)
        {
            $start = 0;
        }
        else
        {
            $start = ($page-1)*$limit;
        }
        $str="";//string to return ;
        $data_query = mysqli_query($this->con,"SELECT * FROM posts WHERE deleted='no' AND ((added_by='$profileUser' AND user_to='none') OR user_to='$profileUser') ORDER BY id DESC");
        
        if(mysqli_num_rows($data_query)>0)
        {

            $num_iteration = 0;//number of results checked out not necesarrily posted
            $count = 0;
        
            while($row=mysqli_fetch_array($data_query))
            {
                $id= $row['id'];
                //echo $id." ";

                $body = $row['body'];
                //echo $body;
                $added_by = $row['added_by'];
                $date_time = $row['date_added'];
                //preparing user_to string
                $check_comments = mysqli_query($this->con,"SELECT * FROM comments WHERE post_id='$id'");
                $check_comments_num = mysqli_num_rows($check_comments);
            
                
                //all post are coming from database so whose account is closed
                //we wont show their posts
               
                $user_logged_obj = new User($this->con,$userLoggedIn);
                /*if($user_logged_obj->isFriend($added_by)==false)
                {
                    continue;
                }*/
                $num_iteration++;
                if($num_iteration<$start) //starting point is yet not reached
                    continue;

                //Once limit is recahed(mentioned in ajaxloadposts) will break the loop

                if($count>$limit)
                {
                break;
                }
                else
                {
                    $count++;
                }
                                //danger php tags are broken just to insert some js

                ?>
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script> 

                <script>
                    function toggle<?php echo $id;?>()
                    {
                        //the event which is actually calling this function
                        var target = $(event.target);
                        if(!target.is("a"))
                        {
                            var element = document.getElementById("toggleComment<?php echo $id; ?>");
                            if(element.style.display =='block')
                            element.style.display = 'none';
                            else
                            element.style.display='block';
                        }
                    
                    }

               
                    $(document).ready(function()
                    {
                        $('#post<?php echo $id;?>').on('click',function()
                        {
                            <?php "here";?>
                            bootbox.confirm("Are you sure you want to delete this post? ",function(result)
                            {
                                $.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id?>",{result:result});
                                if(result)
                                {
                                    location.reload();
                                }
                            });
                        });
                    });
                    </script>
                <?php

                //adding delete button
                if($userLoggedIn==$added_by)
                {
                    $delete_button = "<button class='delete_button btn-danger' id='post$id' onClick='javascript:post$id()' >X</button>";
                }
                else
                {
                    $delete_button = "";
                }
                $user_details_query = mysqli_query($this->con,"SELECT first_name,last_name,profile_pic FROM users WHERE username='$added_by' ");
                $user_row = mysqli_fetch_array($user_details_query);
                $first_name = $user_row['first_name'];
                $last_name = $user_row['last_name'];
                $profile_pic = $user_row['profile_pic'];

                //Timeframe
                $date_time_row = date("Y-m-d H:i:s");
                $start_date = new DateTime($date_time);//Time of post
                $end_date = new DateTime($date_time_row);//Current time
                $interval = $start_date->diff($end_date);//difference bteween above two
                if($interval->y>=1)
                {
                    if($interval==1)
                    $time_message = "1" . "year ago ";
                    else
                    $time_message = $interval->y . "years ago";
                }
                else if($interval->m>=1)
                {
                    if($interval->d==0)
                    $days ="ago";
                    else if($interval->d==1)
                    $days = "day ago";
                    else
                    $days = $interval->d . "days ago";

                    if($interval->m==1)
                    {
                        $time_message = $interval->m . "month" .$days;
                    }
                    else
                    {
                        $time_message = $interval->m . "months" .$days;
                    }
                }
                else if($interval->d>=1)
                {
                    if($interval->d==1)
                    $time_message = "Yesterday";
                    else
                    $time_message = $interval->d . "days ago";
                }
                else if($interval->h>=1)
                {
                    if($interval->h==1)
                    $time_message = $interval->h. "hour ago";
                    else
                    $time_message = $interval->h . "hours ago";
                }
                else if($interval->i>=1)
                {
                    if($interval->i==1)
                    $time_message = $interval->i. "minute ago";
                    else
                    $time_message = $interval->i . "minutes ago";
                }
                else
                {
                    if($interval->s<30)
                    {
                    
                        $time_message =  "just now";
                    }
                    else
                    {
                        $time_message = $interval->s . "seconds ago";
                    }
                }
                

                $str .=  "<div class='status_post' onClick='javascript:toggle$id()' >
                            <div class='post_profile_pic'>
                            <img src='$profile_pic' width='50'>
                            </div>
                            <div class='posted_by' style='color:#ACACAC;'>
                            <a href='$added_by'>$first_name $last_name </a> &nbsp; &nbsp; &nbsp; &nbsp;
                            $time_message

                            $delete_button
                            </div>

                            <div id='post_body'>
                            $body<br>
                            </div>
                            <br>
                            <div class='newsfeedPostOptions'>
                            <p>
                            Comments($check_comments_num) 
                            </p>
                            <iframe src='like.php?post_id=$id' ></iframe>
                            
                            </div>
                            <div class='post_comment' id='toggleComment$id' style='display:none;' >
                                <iframe src='comment_frame.php?post_id=$id' id='comment_iframe'></iframe>
                            </div>
                            </div>
                            <hr>
                
                            ";
            
                
            }
            ?>
       

        
            <?php

            if($count>$limit)
            $str .= "<input type='hidden' class='nextPage' value='" .($page + 1) . "'>
            <input type='hidden' class ='noMorePosts' value='false'>";
            else
            {
                $str .= "
                <input type='hidden' class ='noMorePosts' value='true'>
                <p style='text-align: centre;'>No more Posts to show</p>";
            }

            

        }
        //here echo is equivalent to return the string
        echo $str;
    }

    public function getSinglePost($post_id)
    {
        
        $userLoggedIn = $this->user_obj->getUsername();
        $opened_query = mysqli_query($this->con,"UPDATE notifications SET opened='yes' AND link LIKE '%$post_id'");
        $str="";//string to return ;
        $data_query = mysqli_query($this->con,"SELECT * FROM posts WHERE deleted='no' AND id='$post_id'");
        
        if(mysqli_num_rows($data_query)>0)
        {

            $row=mysqli_fetch_array($data_query);

            
                $id= $row['id'];
                //echo $id." ";

                $body = $row['body'];
                //echo $body;
                $added_by = $row['added_by'];
                $date_time = $row['date_added'];
                //preparing user_to string
                $check_comments = mysqli_query($this->con,"SELECT * FROM comments WHERE post_id='$id'");
                $check_comments_num = mysqli_num_rows($check_comments);
                if($row['user_to']=="none")
                {
                    $user_to = "";
                }
                else
                {
                    $user_to_obj = new User($this->con,$row['user_to']);
                    $user_to_name = $user_to_obj->getFirstAndLastName();
                    //to be resolved}
                    $user_to ="to". "<a href='" . $row['user_to'] ." '>".$user_to_name . "</a>";
                }
                
                //all post are coming from database so whose account is closed
                //we wont show their posts
                $added_by_obj = new User($this->con,$added_by);
                if($added_by_obj->isClosed())
                {
                    return ;
                }
                $user_logged_obj = new User($this->con,$userLoggedIn);
                ?>
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script> 

                <script>
                    function toggle<?php echo $id;?>()
                    {
                        //the event which is actually calling this function
                        var target = $(event.target);
                        if(!target.is("a")&&!target.is("button"))
                        {
                            var element = document.getElementById("toggleComment<?php echo $id; ?>");
                            if(element.style.display =='block')
                            element.style.display = 'none';
                            else
                            element.style.display='block';
                        }
                    
                    }
                    </script>
                <?php

             
                $user_details_query = mysqli_query($this->con,"SELECT first_name,last_name,profile_pic FROM users WHERE username='$added_by' ");
                $user_row = mysqli_fetch_array($user_details_query);
                $first_name = $user_row['first_name'];
                $last_name = $user_row['last_name'];
                $profile_pic = $user_row['profile_pic'];

                //Timeframe
                $date_time_row = date("Y-m-d H:i:s");
                $start_date = new DateTime($date_time);//Time of post
                $end_date = new DateTime($date_time_row);//Current time
                $interval = $start_date->diff($end_date);//difference bteween above two
                if($interval->y>=1)
                {
                    if($interval==1)
                    $time_message = "1" . "year ago ";
                    else
                    $time_message = $interval->y . "years ago";
                }
                else if($interval->m>=1)
                {
                    if($interval->d==0)
                    $days ="ago";
                    else if($interval->d==1)
                    $days = "day ago";
                    else
                    $days = $interval->d . "days ago";

                    if($interval->m==1)
                    {
                        $time_message = $interval->m . "month" .$days;
                    }
                    else
                    {
                        $time_message = $interval->m . "months" .$days;
                    }
                }
                else if($interval->d>=1)
                {
                    if($interval->d==1)
                    $time_message = "Yesterday";
                    else
                    $time_message = $interval->d . "days ago";
                }
                else if($interval->h>=1)
                {
                    if($interval->h==1)
                    $time_message = $interval->h. "hour ago";
                    else
                    $time_message = $interval->h . "hours ago";
                }
                else if($interval->i>=1)
                {
                    if($interval->i==1)
                    $time_message = $interval->i. "minute ago";
                    else
                    $time_message = $interval->i . "minutes ago";
                }
                else
                {
                    if($interval->s<30)
                    {
                    
                        $time_message =  "just now";
                    }
                    else
                    {
                        $time_message = $interval->s . "seconds ago";
                    }
                }
                

                $str .=  "<div class='status_post' onClick='javascript:toggle$id()' >
                            <div class='post_profile_pic'>
                            <img src='$profile_pic' width='50'>
                            </div>
                            <div class='posted_by' style='color:#ACACAC;'>
                            <a href='$added_by'>$first_name $last_name </a> $user_to &nbsp; &nbsp; &nbsp; &nbsp;
                            $time_message

                            
                            </div>

                            <div id='post_body'>
                            $body<br>
                            </div>
                            <br>
                            <div class='newsfeedPostOptions'>
                            <p>
                            Comments($check_comments_num) 
                            </p>
                            <iframe src='like.php?post_id=$id' ></iframe>
                            
                            </div>
                            <div class='post_comment' id='toggleComment$id' style='display:none;' >
                                <iframe src='comment_frame.php?post_id=$id' id='comment_iframe'></iframe>
                            </div>
                            </div>
                            <hr>
                
                            ";


       

            

        }
        //here echo is equivalent to return the string
        echo $str;
    }
}
?>
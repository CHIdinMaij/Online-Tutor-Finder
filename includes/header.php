
<?php
//this is designed in such a way that every page we open will have the same top bar
require 'config/config.php';
include("includes/classes/User.php");
include("includes/classes/Post.php");
include("includes/classes/Message.php");
include("includes/classes/Notification.php");
if(isset($_SESSION['username']))
{
    $userLoggedIn = $_SESSION['username'];
    $user_details_query = mysqli_query($con,"SELECT * FROM users WHERE username='$userLoggedIn' ");
    $user = mysqli_fetch_array($user_details_query);
    
}
else
{
    //if not logged in redirect to registration page
    header("Location: register.php");
}

?>
<html>
    <head>
        <title>My Site</title>
        <!adding jquery>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script> 
        <!adding downloaded bootstrap file js>
        <script src="assets/js/bootstrap.js"></script>
        <script src="assets/js/bootbox.min.js"></script>
        <script src="assets/js/demo.js"></script>
        <script src="assets/js/jquery.jcrop.js"></script>
        <script src="assets/js/jcrop_bits.js"></script>
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
        <!adding downloaded bootstrap file css>
        <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
        <!to avoid or to override any styling given in above file we are including this>
        <link rel="stylesheet" type="text/css" href="assets/css/style.css">
        <link rel="stylesheet" href="assets/css/jquery.Jcrop.css" typr="text/css">

       
    </head>
    <body>

        <div class="top_bar" >
            <div class="logo">
                <a href="index.php">Myfeed!</a>
            </div>

            <div class="search">
                <form action="search.php" method="GET" name="search_form">
                    <input type="text" onkeyup="getLivesearchUsers(this.value,'<?php echo $userLoggedIn;?>')" name="q" placeholder="Search..." autocomplete="off" id="search_text_input">

                    <div class="button_holder">
                        <img src="assets/images/icons/magnifying_glass.png">
                    </div>
                </form>

                <div class="search_results">

                </div>

                <div class="search_results_footer_empty">

                </div>
            </div>

            <nav>
                <?php 
                    //Unread messages
                    $messages = new Message($con,$userLoggedIn);
                    $num_messages = $messages->getUnreadNumber();

                    //Unread notifications
                    $notifications = new Notification($con,$userLoggedIn);
                    $num_notifications = $notifications->getUnreadNumber();

                    //Unchecked friendsRequest
                    $user_obj = new User($con,$userLoggedIn);
                    $num_requests = $user_obj->getNumberOfFriendRequests();
                    
                ?>
                <a href="<?php echo $userLoggedIn ?>"><?php echo $userLoggedIn ?> </a>

                
                    <a href="<?php echo $userLoggedIn ?>"><i class="fa fa-home fa-lg"></i> </a>  
            
                <a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn;?>','message')">
                <i class="fa fa-envelope fa-lg">
                <?php
                if($num_messages>0)
                echo "<span class='notification_badge' id='unread_message'>".$num_messages."</span>";
                ?>
                </i>
                </a>
                
                <a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn;?>','notification')">
                <i class="fa fa-bell-o fa-lg">
                <?php
                if($num_notifications>0)
                echo "<span class='notification_badge' id='unread_notification'>".$num_notifications."</span>";
                ?>
                </i></a>
                <a href="requests.php"><i class="fa fa-users fa-lg">
                <?php
                if($num_requests>0)
                echo "<span class='notification_badge' id='unread_requests'>".$num_requests."</span>";
                ?>
                </i></a>
                <a href="settings.php"><i class="fa fa-cog fa-lg"></i></a>
                <a href="includes/handlers/logout.php"><i class="fa fa-sign-out fa-lg"></i></a>
            </nav>

            <div class="dropdown_data_window" style="height:0px; border:none;"></div>
            <input type="hidden" id="dropdown_data_type" value="">

        </div>

        <script>

//Ajax is used to make database calls without reloading the page
//values assigning from php variable to javascript variable
var userLoggedIn = '<?php echo $userLoggedIn; ?>';
$(document).ready(function()
{
    
    $('.dropdown_data_window').scroll(function()
    {
        //alert('hello');

        var inner_height = $('.dropdown_data_window').innerHeight(); //div containing posts
        var scroll_top = $('.dropdown_data_window').scrollTop();
        var page = $('.dropdown_data_window').find('.noMoreDropdownData').val();
        var noMoreData = $('.dropdown_data_window').find('.noMoreDropdownData').val();
        if((scroll_top+inner_height >= $('.dropdown_data_window')[0].scrollHeight)&& noMoreData=='false')
        {
            var pageName ;//Holds name of page to send ajax request
            var type=$('#dropdown_data_type').val();

            if(type=='notification')
            pageName = "ajax_load_notification.php";
            else if(type=='message')
            pageName = "ajax_load_messages.php";

            var ajaxReq  =  $.ajax({
                url: "includes/handlers/" + pageName ,
                type: "POST",
                data: "page="+page+"&userLoggedIn=" + userLoggedIn,
                cache:false,

                success: function(response)
                {
                    $('.dropdown_data_window').find('.nextPageDropdownData').remove();
                    $('.dropdown_data_window').find('.noMoreDropdownData').remove();
                   
                    $('.dropdown_data_window').append(response);
                }
            });
        }//End if

        return false;
    });//End (window).scrollfunction()

});
</script>

        <div class="wrapper">
    

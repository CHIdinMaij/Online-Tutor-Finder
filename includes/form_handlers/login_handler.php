<?php
//to retain all valid formatted credentials
    if(isset($_POST['login_button']))
    {
        //echo "okay";
        //sanitize email into session variable
        $email = filter_var($_POST['log_email'],FILTER_SANITIZE_EMAIL);
        //echo $email;
        $_SESSION['log_email']=$email;
        //encrypting password
        $password = md5($_POST['log_password']);
        $check_database_query = mysqli_query($con,"SELECT * FROM users WHERE password = '$password' AND email='$email'  ");
        $check_login_query = mysqli_num_rows($check_database_query);
        if($check_login_query==1)
        {
            //echo '1st loop';
            $row = mysqli_fetch_array($check_database_query);//return all data array corresponding to given section
            $username = $row['username'];
            $user_closed_query = mysqli_query($con,"SELECT * FROM users WHERE EMAIL='$email' AND 'user_closed'='YES' ");
            //echo mysqli_num_rows($user_closed_query);
            //echo $row['user_closed'];
            if($row['user_closed']=='YES')
            {
                //echo 'control';
                $reopen_account = mysqli_query($con,"UPDATE users SET user_closed='NO' WHERE email='$email' AND user_closed='YES' ");
            }
            $_SESSION['username'] = $username;
            header("Location: index.php"); //to redirect the index page
            exit();
        }
        else
        {
            array_push($error_array,"Email or Password is invalid<br>");
        }
    }
?>
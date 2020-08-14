<?php
//to retain all valid formatted credentials


$fname ="";
$lname ="";
$email = "";
$email2="";
$password="";
$password2="";
$date="";
$error_array=array();

//if register button is pressed then only start accessing the form
if(isset($_POST['register_button']))
{
    //First name
    $fname = strip_tags($_POST['reg_fname']); //strip tag is used to getdrid of unnecessary tags entered by user
    $fname = str_replace(' ','',$fname);//replaces all spaces by nospaces
    $fname = ucfirst(strtolower($fname));//lowercase whole string then uppercase first letter
    $_SESSION['reg_fname'] = $fname; //storing data in session variable using ids
    //Last name
    $lname = strip_tags($_POST['reg_lname']); //strip tag is used to getdrid of unnecessary tags entered by user
    $lname = str_replace(' ','',$lname);//replaces all spaces by nospaces
    $lname = ucfirst(strtolower($lname));//lowercase whole string then uppercase first letter
    $_SESSION['reg_lname'] = $lname;//storing data in session variable using ids
    //email
    $email = strip_tags($_POST['reg_email']); //strip tag is used to getdrid of unnecessary tags entered by user
    $email = str_replace(' ','',$email);//replaces all spaces by nospaces
    $email = ucfirst(strtolower($email));//lowercase whole string then uppercase first letter
    $_SESSION['reg_email'] = $email;//storing data in session variable using ids
    //confirm email
    $email2 = strip_tags($_POST['reg_email2']); //strip tag is used to getdrid of unnecessary tags entered by user
    $email2 = str_replace(' ','',$email2);//replaces all spaces by nospaces
    $email2 = ucfirst(strtolower($email2));//lowercase whole string then uppercase first letter
    $_SESSION['reg_email2'] = $email2;//storing data in session variable using ids
    //password
    $password = strip_tags($_POST['reg_password']); //strip tag is used to getdrid of unnecessary tags entered by user
    $_SESSION['reg_password'] = $password;//storing data in session variable using ids
    //confirm password
    $password2 = strip_tags($_POST['reg_password2']); //strip tag is used to getdrid of unnecessary tags entered by user
    $_SESSION['reg_password2'] = $password2;//storing data in session variable using ids

    $date=Date("Y-m-d");//to get current dates    
    //echo $email;
    if($email==$email2)
    {
        //to check given email fromat is valid or not
        if(filter_var($email,FILTER_VALIDATE_EMAIL))
        {
            $email = filter_var($email,FILTER_VALIDATE_EMAIL);
            //check if email already exists or not
            $e_check = mysqli_query($con,"SELECT email FROM users WHERE email='$email'");
            //count the number of rows returned for particular email
            $num_rows = mysqli_num_rows($e_check);
            if($num_rows>0)
            {
                array_push($error_array,"Email already registered<br>");
            }
        }
        else
        {
            array_push($error_array,"invalid format<br>");
        }
    }
    else
    {
        array_push($error_array,"Emails dont match<br>");
    }

    //validating first name
    if(strlen($fname)>25||strlen($fname)<2)
    {
        array_push($error_array,"Your First name should be between 2 and 25 charecters<br>");
    }
    if(strlen($lname)>25||strlen($lname)<2)
    {
    
        array_push($error_array,"Your Last name should be between 2 and 25 charecters<br>");

    }
    if($password!=$password2)
    {
        
        array_push($error_array,"Passwords are not same<br>");

    }
    else
    {
        if(preg_match('/[^A-Za-z0-9]/',$password))
        array_push($error_array,"password can only contain english charecter<br>");
    }
    if(strlen($password)>30||strlen($password)<2)
    {
        array_push($error_array,"Your password should be between 2 and 30 charecters<br>");
    }

    if(empty($error_array))
    {
        //echo "okay";
        //before sending data chunck to database we r encrypting
        $password = md5($password);
        //generating unique usernames
        $username = $fname . '_' . $lname;
        $check_user = mysqli_query($con,"SELECT username FROM users WHERE username='username'");
        $i = 0;
        
        while(mysqli_num_rows($check_user)!=0)
        {
            $i++;
            $username = $username . '_' . $i;
            $check_user = mysqli_query($con,"SELECT username FROM users WHERE username='username'");
        }
        //giving a profilepic
        $rand = rand(1,2);
        if($rand==1)
        $profile_pic="assets/images/profile_pic/default/head_wet_asphalt.png";
        else
        $profile_pic="assets/images/profile_pic/default/head_wisteria.png";

        //echo $profile_pic;

        //pushing data into database
        $query = mysqli_query($con," INSERT INTO users VALUES ('','$fname','$lname','$username','$email',
        '$password','$date','$profile_pic','0','0','NO',',' ) " );
        //',' for friend array system

        array_push($error_array,"<span style='color: #14c800;' >You are all set,Login Now</span><br>");
        //clear all credentials by clearing session variable
        $_SESSION['reg_fname']="";
        $_SESSION['reg_lname']="";
        $_SESSION['reg_email']="";
        $_SESSION['reg_email2']="";
        $_SESSION['reg_password']="";
        $_SESSION['reg_password2']="";
    }
}

?>
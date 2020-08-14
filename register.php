<?php
//to retain all valid formatted credentials

//abstracted file to reuse
require 'config/config.php';
require 'includes/form_handlers/register_handler.php';
//declaring register_handler we can use variables created in register_handler at logi_handler
require 'includes/form_handlers/login_handler.php';

?>

<html>
    <title>SwirlFeed</title>
    <link rel="stylesheet" type="text/css" href="assets\css\register_style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script> 
    <script src="assets/js/register.js"></script>
    <body>
        <?php
        //when user clicking register button but giving invalid credentials
        //error messages should be in that page so explicitly we are defining
            if(isset($_POST['register_button']))
            {
                echo '
                <script>
                $(document).ready(function(){
                    $("#first").hide();
                    $("#second").show();
                });
                </script>
                ';
            }

        ?>
        <div class="wrapper">

            <div class="login_box">
                    <div class="login_header">
                        <h1>SwirlFeed!</h1>
                        Login or Sign up Below!!
                    </div>

                    <div id="first">
                        <form action="register.php" method="POST">
                            <input type="email" name="log_email" placeholder="Email Address"  value="<?php if(isset($_SESSION['log_email']))
                            {
                                echo $_SESSION['log_email'];
                            } ?>" required>
                            <br>
                            <input type="password" name="log_password" placeholder="Password">
                            <br>
                            <input type="submit" name="login_button" value="Login">
                            <?php if(in_array("Email or Password is invalid<br>",$error_array))
                            echo "Email or Password is invalid<br>";?>
                            <br>
                            <a href="#" id="signup" class="signup">Need an account?Register Here</a>
                        </form>
                    </div>
                
                <div id="second">

                        <form action="register.php" method="POST">
                            <!we can embed php to show the credentials if it is given>
                            <input type="text" name="reg_fname" placeholder="First Name"
                            value="<?php if(isset($_SESSION['reg_fname']))
                            {
                                echo $_SESSION['reg_fname'];
                            } ?>" required>
                            <br>

                            <?php if(in_array("Your First name should be between 2 and 25 charecters<br>",$error_array))
                            echo "Your First name should be between 2 and 25 charecters<br>";?>

                            <input type="text" name="reg_lname" placeholder="Last Name" 
                            value="<?php if(isset($_SESSION['reg_lname']))
                            {
                                echo $_SESSION['reg_lname'];
                            } ?>" required>
                            <br>

                            <?php if(in_array("Your Last name should be between 2 and 25 charecters<br>",$error_array))
                            echo "Your Last name should be between 2 and 25 charecters<br>";?>

                            <input type="text" name="reg_email" placeholder="Email"
                            value="<?php if(isset($_SESSION['reg_email']))
                            {
                                echo $_SESSION['reg_email'];
                            } ?>" required>
                            <br>

                            <?php if(in_array("Email already registered<br>",$error_array))
                            echo "Email already registered<br>";
                            else if(in_array("invalid format<br>",$error_array))
                            echo "invalid format<br>";
                            else if(in_array("Emails dont match<br>",$error_array))
                            echo "Emails dont match<br>";?>

                            <input type="text" name="reg_email2" placeholder="Confirm Email"
                            value="<?php if(isset($_SESSION['reg_email2']))
                            {
                                echo $_SESSION['reg_email2'];
                            } ?>"  required>
                            <br>
                            

                            <input type="password" name="reg_password" placeholder="Password"  required>
                            <br>
                            <?php if(in_array("Your password should be between 2 and 30 charecters<br>",$error_array))
                            echo "Your password should be between 2 and 30 charecters<br>";
                            else if(in_array("password can only contain english charecter<br>",$error_array))
                            echo "password can only contain english charecter<br>";
                            else if(in_array("Passwords are not same<br>",$error_array))
                            echo "Passwords are not same<br>";?>
                            <input type="password" name="reg_password2" placeholder="Confirm Password"  required>
                            <br>
                        
                            <input type="submit" name="register_button" value="Register">
                            <br>
                            <?php if(in_array("<span style='color: #14c800;' >You are all set,Login Now</span><br>",$error_array))
                            echo "<span style='color: #14c800;' >You are all set,Login Now</span><br>";?>

                            <a href="#" id="signin" class="signin">Already have an account? Sign in here</a>

                        </form>

                </div>
            
                
            </div>
        </div>
    </body>
</html>


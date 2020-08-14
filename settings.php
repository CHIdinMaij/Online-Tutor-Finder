<?php
include("includes/header.php");
include("includes/form_handlers/settings_handler.php");
?>

<div class="main_column column">
    <h4>Account Settings</h4>
    <?php
    echo "<img src='" . $user['profile_pic'] ."' class='small_profile_pic'>";
    ?>
    <br>
    <a href="upload.php">Upload New Profile Picture</a> <br><br><br>

    Modify the values and click 'Update Details'
    <?php
    $user_data_query = mysqli_query($con,"SELECT first_name,last_name,email FROM users WHERE username='$userLoggedIn'");
    $row = mysqli_fetch_array($user_data_query);
    $first_name = $row['first_name'];
    $last_name = $row['last_name'];
    $email = $row['email'];
    ?>
    <form action="" method="POST">
        FirstName:<input type="text" name="first_name" value="<?php echo $first_name;?>" id='settings_input'>
        <br>
        LastName:<input type="text" name="last_name" value="<?php echo $last_name;?>" id='settings_input'>
        <br>
        Email:<input type="text" name="email" value="<?php echo  $email ;?>" id='settings_input'>
        <br>
        <?php
        echo $message;
        ?>
        <input type='submit' value='Update' name='update_details' id='save_details' class='info settings_submit'>
        <br>
    </form>

    <h4>Change Password</h4>
    <form action="settings.php" method="POST">
        Old Password:<input type="password" name="old_password" id='old_pass' >
        <br>
        New Password:<input type="password" name="new_password_1" id='settings_input'>
        <br>
        Re Enter New Password:<input type="password" name="new_password_2" id='settings_input'>
        <br>
        <input type='submit' value='Update' name='update_password' id='save_details' class='info settings_submit'>
        <br>

        <?php
        echo $password_message;
        ?>
    </form>

    <h4>Close Account</h4>
    <form action="close_account.php" method="POST">
        <input type='submit' name='close_account' id='close_account' value='Close Account' class='danger settings_submit'>
    </form>

</div>

<script>
    console.log(document.getElementById('old_pass').val );
</script>
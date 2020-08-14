<?php
require '../../config/config.php';
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script> 


<?php
if(isset($_GET['post_id']))
{
    $post_id = $_GET['post_id'];
}
if(isset($_POST['result']))
{
    
  
    if($_POST['result']=='true')
    {
        
        ?>
    <script>
        document.write("ok");
    </script>
    <?php
        $query = mysqli_query($con,"UPDATE posts SET deleted='yes' WHERE id='$post_id' ");
    }
}
?>
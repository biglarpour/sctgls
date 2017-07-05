<?php
session_start();
require_once 'class.user.php';
$user_login = new USER();

if($user_login->is_logged_in()!="")
{
	$user_login->redirect('home.php');
}

if(isset($_POST['btn-login']))
{
	$email = trim($_POST['txtemail']);
	$upass = trim($_POST['txtupass']);
	
	if($user_login->login($email,$upass))
	{
		$user_login->redirect('home.php');
	}
}
?>

<head>
<!-- Bootstrap -->
<link href="login/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="login/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
<link href="login/assets/styles.css" rel="stylesheet" media="screen">
 <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<script src="login/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
</head>
<div class="container">
    <?php
    if(isset($_GET['inactive']))
    {
        ?>
        <div class='alert alert-error'>
            <button class='close' data-dismiss='alert'>&times;</button>
            <strong>Sorry!</strong> This Account is not Activated Go to your Inbox and Activate it.
        </div>
        <?php
    }
    ?>
    <form class="form-signin" method="post">
    <?php
    if(isset($_GET['error']))
    {
        ?>
        <div class='alert alert-success'>
            <button class='close' data-dismiss='alert'>&times;</button>
            <strong>Wrong Details!</strong>
        </div>
        <?php
    }
    ?>
    <input type="email" class="input-block-level" placeholder="Email address" name="txtemail" required />
    <input type="password" class="input-block-level" placeholder="Password" name="txtupass" required />
    <button class="btn btn-large btn-primary" type="submit" name="btn-login">log in</button>
    <a class="fgetpass" href="fpass.php">Lost your Password? </a>
  </form>

</div> <!-- /container -->
<script src="login/bootstrap/js/jquery-1.9.1.min.js"></script>
<script src="login/bootstrap/js/bootstrap.min.js"></script>
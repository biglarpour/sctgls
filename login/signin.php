<?php
session_start();
require_once 'login/class.user.php';
$user_login = new USER();

if($user_login->is_logged_in()!="")
{
	$user_login->redirect('/scoutinggoals/home/');
}

if(isset($_POST['btn-login']))
{
	$email = trim($_POST['txtemail']);
	$upass = trim($_POST['txtupass']);
	if($user_login->login($email,$upass))
	{
		$user_login->redirect('/scoutinggoals/home/');
	}
    if($user_login->inactive === true)
    {
        $login_inactive = <<< INACTIVE
        <div class='alert alert-error'>
            <button class='close' data-dismiss='alert'>&times;</button>
            <strong>Sorry!</strong> This Account is not Activated Go to your Inbox and Activate it.
        </div>
INACTIVE;
    }
    if($user_login->error === true)
    {
        $login_error = <<< ERROR
        <div class='alert alert-success'>
            <button class='close' data-dismiss='alert'>&times;</button>
            <strong>Wrong Details!</strong>
        </div>
ERROR;
    }
}


$LOGIN_HTML = <<< HTML
<head>
<!-- Bootstrap -->
<link href="login/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="login/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
<link href="login/assets/styles.css" rel="stylesheet" media="screen">
 <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<script src="js/libs/modernizr-2.6.2-respond-1.1.0.min.js"></script>
</head>
<div class="container">
    {$login_inactive}
    <form class="form-signin" method="post">
    {$login_error}
    <input type="email" class="input-block-level" placeholder="Email address" name="txtemail" required />
    <input type="password" class="input-block-level" placeholder="Password" name="txtupass" required />
    <button class="btn btn-large btn-primary" type="submit" name="btn-login">log in</button>
    <li class="fgetpass">
        <a href="#fpass">Lost your Password? </a>
    </li>
  </form>

</div> <!-- /container -->
<script src="login/bootstrap/js/jquery-1.9.1.min.js"></script>
<script src="login/bootstrap/js/bootstrap.min.js"></script>

HTML;
?>
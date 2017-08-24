<?php
session_start();
require_once '../login/class.user.php';
$user_login = new USER();

if($user_login->is_logged_in()!="" and $user_login->is_admin()!="")
{
    $user_login->redirect('/scoutinggoals/admin/home/');
}

if(isset($_POST['btn-login']))
{
    $email = trim($_POST['txtemail']);
    $upass = trim($_POST['txtupass']);
    if($user_login->login($email,$upass,"admin"))
    {
        $user_login->redirect('/scoutinggoals/admin/home/');
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
<link href="/scoutinggoas/login/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="/scoutinggoas/login/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
<link href="/scoutinggoas/login/assets/styles.css" rel="stylesheet" media="screen">
 <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<script src="/scoutinggoas/js/libs/modernizr-2.6.2-respond-1.1.0.min.js"></script>
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
<script src="/scoutinggoas/login/bootstrap/js/jquery-1.9.1.min.js"></script>
<script src="/scoutinggoas/login/bootstrap/js/bootstrap.min.js"></script>

HTML;
?>
<html class="no-js lt-ie9 lt-ie8 lt-ie7">
<html class="no-js lt-ie9 lt-ie8">
<html class="no-js lt-ie9">
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title>Scouting Goals</title>
    <meta name="description" content="Boys scouts application to help you achieve you goals">
    <meta name="keywords" content="">

    <!-- Mobile viewport -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">

    <link rel="shortcut icon" href="/scoutinggoas/images/favicon.ico"  type="image/x-icon">

    <!-- CSS-->
    <!-- Google web fonts. You can get your own bundle at http://www.google.com/fonts. Don't forget to update the CSS accordingly!-->
    <link href='https://fonts.googleapis.com/css?family=Droid+Serif|Ubuntu' rel='stylesheet' type='text/css'>

    <link rel="stylesheet" href="/scoutinggoas/css/normalize.css">
    <link rel="stylesheet" href="/scoutinggoas/js/flexslider/flexslider.css">
    <link rel="stylesheet" href="/scoutinggoas/css/basic-style.css">

    <!-- end CSS-->

    <!-- JS-->
    <script src="/scoutinggoas/js/libs/modernizr-2.6.2.min.js"></script>
    <!-- end JS-->

</head>

<body id="home">
<?php include 'header.php';?>
<!--Main area-->
<div class="hero-wrapper" >
    <section id="main" class="clearfix">
        <article id="login" class="grid_12 default">
            <h1 class="hero-msg-1">Admin Login</h1>
            <div id="log-in">
                <?php echo $LOGIN_HTML; ?>
            </div>
            <img  class="hero-img"  width="100%" src="/scoutinggoals/images/admin_bg_min.jpg" />
        </article><!-- end login -->
    </section><!-- end main area -->
</div>
<?php include '../footer.php';?>

<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="js/libs/jquery-1.9.0.min.js">\x3C/script>')</script>

<script defer src="/scoutinggoas/js/flexslider/jquery.flexslider-min.js"></script>

<!-- fire ups - read this file!  -->
<script src="/scoutinggoas/js/main.js"></script>

</body>
</html>
<?php
session_start();
require_once '../login/class.user.php';
$user_home = new USER();

if(!$user_home->is_logged_in())
{
    $user_home->redirect('/scoutinggoals');
}
$stmt = $user_home->runQuery("SELECT * FROM users WHERE userID=:uid");
$stmt->execute(array(":uid"=>$_SESSION['userSession']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$master_roles = array("scout_master");
$scout_roles = array("scout_member");
if (in_array($row['role_type'], $scout_roles)){
    require_once 'scout/index.php';
    $role_html = $SCOUT_HTML;
}
elseif (in_array($row['role_type'], $master_roles)){
    require_once 'master/index.php';
    $role_html = $MASTER_HTML;
}
else {
    throw New Exception("Failed to find a valid role for current user. Please contact your account manager.");
}
?>
<!DOCTYPE html>
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

    <link rel="shortcut icon" href="/scoutinggoals/images/favicon.ico"  type="image/x-icon">

    <!-- CSS-->
    <!-- Google web fonts. You can get your own bundle at http://www.google.com/fonts. Don't forget to update the CSS accordingly!-->
    <link href='https://fonts.googleapis.com/css?family=Droid+Serif|Ubuntu' rel='stylesheet' type='text/css'>

    <link rel="stylesheet" href="/scoutinggoas/css/normalize.css">
    <link rel="stylesheet" href="/scoutinggoas/js/flexslider/flexslider.css">
    <link rel="stylesheet" href="/scoutinggoas/css/basic-style.css">

    <!-- end CSS-->

    <!-- JS-->
    <script src="/scoutinggoals/js/libs/modernizr-2.6.2.min.js"></script>
    <!-- end JS-->

</head>

<body id="home">
<?php echo $role_html;?>
<?php include '../footer.php';?>

<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="js/libs/jquery-1.9.0.min.js">\x3C/script>')</script>

<script defer src="/scoutinggoas/js/flexslider/jquery.flexslider-min.js"></script>

<!-- fire ups - read this file!  -->
<script src="/scoutinggoas/js/main.js"></script>

</body>
</html>

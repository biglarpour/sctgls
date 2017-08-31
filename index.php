<?php require_once 'login/signin.php';?>
<?php require_once 'login/signup.php';?>
<?php require_once 'login/fpass.php';?>
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

<link rel="shortcut icon" href="images/favicon.ico"  type="image/x-icon">

<!-- CSS-->
<!-- Google web fonts. You can get your own bundle at http://www.google.com/fonts. Don't forget to update the CSS accordingly!-->
<link href='https://fonts.googleapis.com/css?family=Droid+Serif|Ubuntu' rel='stylesheet' type='text/css'>

<link rel="stylesheet" href="css/normalize.css">
<link rel="stylesheet" href="js/flexslider/flexslider.css">
<link rel="stylesheet" href="css/basic-style.css">

<!-- end CSS-->
    
<!-- JS-->
<script src="js/libs/modernizr-2.6.2.min.js"></script>
<!-- end JS-->

</head>

<body id="home">
    <?php include 'header.php';?>
    <!--Main area-->
    <div class="hero-wrapper" >
        <section id="main" class="clearfix">
                    <article id="login" class="grid_12 default">
                        <div class="hero-msg">
                            <h1 class="hero-msg-1">Let us help you</h1>
                            <h1 class="hero-msg-2">advance your organization</h1>
                        </div>
                        <div id="log-in">
                            <?php echo $LOGIN_HTML; ?>
                        </div>
                        <img  class="hero-img"  width="100%" src="images/night_camp.jpg" />
                    </article><!-- end login -->
                    <article id="signup" class="grid_12">
                        <div id="sign-up">
                            <?php echo $SIGNUP_HTML; ?>
                        </div>
                        <img class="hero-img" width="100%" src="images/night_camp.jpg" />
                    </article><!-- end signup -->
                    <article id="fpass" class="grid_12">
                        <div id="f_pass">
                            <?php echo $FPASS_HTML; ?>
                        </div>
                        <img class="hero-img" width="100%" src="images/night_camp.jpg" />
                    </article><!-- end signup -->
        </section><!-- end main area -->
    </div>





    <!-- content area -->
    <div class="wrapper">
        <section id="quote" class="wide-content">
            <div class="grid_4">
                <h1>"I want the scouts to do the planning"</h1>
                <h3>-Aaron Schultze,</h3><p>Scoutmaster</p>
            </div>
            <div class="grid_4">
                <img src="images/logofav.png" />
            </div>
            <div class="grid_4">
                <p>filler text about super cool scouting goals.  filler text about super cool scouting goals.  filler text about super cool scouting goals.  filler text about super cool scouting goals.  filler text about super cool scouting goals.  filler text about super cool scouting goals.  filler text about super cool scouting goals.  filler text about super cool scouting goals.
                </p>
            </div>
        </section>

    <!-- content area -->
        <section id="content" class="wide-content">
          <div class="row">
            <div class="grid_12">
                <img width="100%" src="images/home_forrest.jpg" />
            </div>
          </div><!-- end row -->
        </section><!-- end content area -->

    </div><!-- #end div .wrapper -->


    <?php include 'footer.php';?>

<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="js/libs/jquery-1.9.0.min.js">\x3C/script>')</script>

<script defer src="js/flexslider/jquery.flexslider-min.js"></script>

<!-- fire ups - read this file!  -->   
<script src="js/main.js"></script>

</body>
</html>
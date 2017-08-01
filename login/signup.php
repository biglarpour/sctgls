<?php
session_start();
require_once 'class.user.php';

$reg_user = new USER();

if($reg_user->is_logged_in()!="")
{
	$reg_user->redirect('/scoutinggoals/home/');
}


if(isset($_POST['btn-signup']))
{
	$firstn = trim($_POST['txtfname']);
	$lastn = trim($_POST['txtlname']);
	$bdate = trim($_POST['birthdate']);
	$role = trim($_POST['roles']);
	$uname = trim($_POST['txtuname']);
	$email = trim($_POST['txtemail']);
	$upass = trim($_POST['txtpass']);
	$mastersId = trim($_POST['mastersid']);
	$code = md5(uniqid(rand()));
	
	$stmt = $reg_user->runQuery("SELECT * FROM users WHERE userEmail=:email_id");
	$stmt->execute(array(":email_id"=>$email));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	
	if($stmt->rowCount() > 0)
	{
		$msg = "
		      <div class='alert alert-error'>
				<button class='close' data-dismiss='alert'>&times;</button>
					<strong>Sorry !</strong>  email allready exists , Please Try another one
			  </div>
			  ";
	}
	else
	{
		if($reg_user->register($firstn,$lastn,$bdate,$role,$uname,$email,$upass,$code,$mastersId))
		{			
			$id = $reg_user->lasdID();		
			$key = base64_encode($id);
			$id = $key;
			
			$message = "					
						Hello $uname,
						<br /><br />
						Welcome to Scouting Goals!<br/>
						To complete your registration  please , just click following link<br/>
						<br /><br />
						<a href='https://biglarpour.com/scountinggoals/login/verify.php?id=$id&code=$code'>Click HERE to Activate :)</a>
						<br /><br />
						Thanks,";

			$subject = "Confirm Registration";

			$reg_user->send_mail($email,$message,$subject);
			$msg = "
					<div class='alert alert-success'>
						<button class='close' data-dismiss='alert'>&times;</button>
						<strong>Success!</strong>  We've sent an email to $email.
                    Please click on the confirmation link in the email to create your account.
			  		</div>
					";
		}
		else
		{
			echo "sorry , Query could no execute...";
		}
	}
}
$SIGNUP_HTML = <<< HTML
<!DOCTYPE html>
<html>
  <head>
    <title>Signup | Coding Cage</title>
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
  <body id="login">
    <div class="container">
	{$msg}
      <form class="form-signin" method="post">
        <h2 class="form-signin-heading">Sign Up</h2>
        <input type="text" class="input-block-level" placeholder="First Name" name="txtfname" required />
        <input type="text" class="input-block-level" placeholder="Last Name" name="txtlname" required />
        <input type="date" class="input-block-level" placeholder="Birthday" name="birthdate" required />
        <select id="role_state" name="roles">
            <option value="" disabled selected>Select you Scout Role</option>
            <option value="scout_member">Scout Member</option>
            <option value="scout_master">Scout Master</option>
        </select>
        <input id="mastersId" type="text" class="input-block-level" placeholder="Scout Master's ID" name="mastersid" style="display: none;"/>
        <input type="text" class="input-block-level" placeholder="Username" name="txtuname" required />
        <input type="email" class="input-block-level" placeholder="Email address" name="txtemail" required />
        <input type="password" class="input-block-level" placeholder="Password" name="txtpass" required />
        <button class="btn btn-large btn-primary" type="submit" name="btn-signup">Sign Up</button>
      </form>

    </div> <!-- /container -->
    <script src="login/bootstrap/js/jquery-1.9.1.min.js"></script>
    <script src="login/bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>
HTML;
?>
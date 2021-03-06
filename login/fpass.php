<?php
session_start();
require_once 'class.user.php';
$user = new USER();

if($user->is_logged_in()!="")
{
	$user->redirect('/scoutinggoals/home/');
}
$msg = "<div class='alert alert-info'>
			Please enter your email address. You will receive a link to create a new password via email.!
		</div>";
if(isset($_POST['btn-submit']))
{
	$email = $_POST['txtemail'];
	
	$stmt = $user->runQuery("SELECT userID FROM users WHERE userEmail=:email LIMIT 1");
	$stmt->execute(array(":email"=>$email));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	if($stmt->rowCount() == 1)
	{
		$id = base64_encode($row['userID']);
		$code = md5(uniqid(rand()));
		
		$stmt = $user->runQuery("UPDATE users SET tokenCode=:token WHERE userEmail=:email");
		$stmt->execute(array(":token"=>$code,"email"=>$email));
		
		$message= "
				   Hello, $email
				   <br /><br />
				   A pssword reset was initiated for your Scouting Goals account,
				   <br /><br />
				   If you believe this is to be an error, please ignore this message, if you wish to finalize you password reset, please click the link below.
				   <br /><br />
				   <a href='https://biglarpour.com/scountinggoals/login/resetpass.php?id=$id&code=$code'>click here to reset your password</a>
				   <br /><br />
				   If you are having issues with the link above, you can copy and paste the following URL into your browser:
                   https://biglarpour.com/scountinggoals/login/resetpass.php?id=$id&code=$code
				   <br /><br />
				   ";
		$subject = "Scouting Goals Password Reset";
		
		$user->send_mail($email,$message,$subject);
		
		$msg = "<div class='alert alert-success'>
					<button class='close' data-dismiss='alert'>&times;</button>
					We've sent an email to $email.
                    Please click on the password reset link in the email to generate new password. 
			  	</div>";
	}
	else
	{
		$msg = "<div class='alert alert-danger'>
					<button class='close' data-dismiss='alert'>&times;</button>
					<strong>Sorry!</strong>  this email was not found.
			    </div>";
	}
}
$FPASS_HTML = <<< HTML
<!DOCTYPE html>
<html>
  <head>
    <title>Forgot Password</title>
    <!-- Bootstrap -->
    <link href="login/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="login/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
    <link href="login/assets/styles.css" rel="stylesheet" media="screen">
     <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
  </head>
  <body id="login">
    <div class="container">

      <form class="form-signin" method="post">
        <h2 class="form-signin-heading">Forgot Password</h2>
		{$msg}
        <input type="email" class="input-block-level" placeholder="Email address" name="txtemail" required />
        <button class="btn btn-danger btn-primary" type="submit" name="btn-submit">Generate new Password</button>
      </form>

    </div> <!-- /container -->
    <script src="login/bootstrap/js/jquery-1.9.1.min.js"></script>
    <script src="login/bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>
HTML;
?>
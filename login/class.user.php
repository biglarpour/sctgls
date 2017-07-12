<?php

require_once 'dbconfig.php';

class USER
{	

	private $conn;
	
	public function __construct()
	{
		$database = new Database();
		$db = $database->dbConnection();
		$this->conn = $db;
    }
	
	public function runQuery($sql)
	{
		$stmt = $this->conn->prepare($sql);
		return $stmt;
	}
	
	public function lasdID()
	{
		$stmt = $this->conn->lastInsertId();
		return $stmt;
	}
	
	public function register($fname,$lname,$bdate,$role,$uname,$email,$upass,$code)
	{
		try
		{							
			$password = md5($upass);
			$stmt = $this->conn->prepare("INSERT INTO tbl_users(firstname,lastname,birthdate,role_type,userName,userEmail,userPass,tokenCode)
			                                             VALUES(:first_name,:last_name,:birthdate,:role,:user_name, :user_mail, :user_pass, :active_code)");
			$stmt->bindparam(":first_name",$fname);
			$stmt->bindparam(":last_name",$lname);
			$stmt->bindparam(":birthdate",$bdate);
			$stmt->bindparam(":role",$role);
			$stmt->bindparam(":user_name",$uname);
			$stmt->bindparam(":user_mail",$email);
			$stmt->bindparam(":user_pass",$password);
			$stmt->bindparam(":active_code",$code);
			$stmt->execute();	
			return $stmt;
		}
		catch(PDOException $ex)
		{
			echo $ex->getMessage();
		}
	}
	
	public function login($email,$upass)
	{
		try
		{
			$stmt = $this->conn->prepare("SELECT * FROM tbl_users WHERE userEmail=:email_id");
			$stmt->execute(array(":email_id"=>$email));
			$userRow=$stmt->fetch(PDO::FETCH_ASSOC);
			
			if($stmt->rowCount() == 1)
			{
				if($userRow['userStatus']=="Y")
				{
					if($userRow['userPass']==md5($upass))
					{
						$_SESSION['userSession'] = $userRow['userID'];
						return true;
					}
					else
					{
						$this->redirect("?error");
						exit;
					}
				}
				else
				{
					$this->redirect("?inactive");
					exit;
				}	
			}
			else
			{
				$this->redirect("?error");
				exit;
			}		
		}
		catch(PDOException $ex)
		{
			echo $ex->getMessage();
		}
	}
	
	
	public function is_logged_in()
	{
		if(isset($_SESSION['userSession']))
		{
			return true;
		}
	}
	
	public function redirect($url)
	{
	echo '<script type="text/javascript">
			   window.location = "' .$url. '"
		  </script>';
	}
	
	public function logout()
	{
		session_destroy();
		$_SESSION['userSession'] = false;
	}
	
	function send_mail($email,$message,$subject)
	{						
		require_once('mailer/class.phpmailer.php');
		$mail = new PHPMailer();
		$mail->SMTPDebug  = 0;
		$mail->SMTPAuth   = true;                  
		$mail->SMTPSecure = "ssl";
		$mail->Host       = "smtp.gmail.com";      
		$mail->Port       = 465;
		$mail->AddAddress($email);
		$mail->Username= "4boc4do@gmail.com";
		$mail->Password= "tD4k2fNvqs9XsGbT";
		$mail->SetFrom('abo@biglarpour.com','Scouting Goals');
		$mail->AddReplyTo('abo@biglarpour.com','Scouting Goals');
		$mail->Subject    = $subject;
		$mail->MsgHTML($message);
		$mail->Send();
	}	
}
<?php

require_once 'dbconfig.php';

class USER
{	

	private $conn;
	public $error;
	public $inactive;
	public $last_due_date;
	public $last_rank_id;
	public $last_rank_task;

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
	
	public function register($fname,$lname,$bdate,$role,$uname,$email,$upass,$code,$masterId)
	{
        $scout_master_id = null;
        if (!empty($masterId)){
            try {
	            $scout_master = $this->runQuery("select * from users
                                                      where users.tokenCode = :master_id");
	            $scout_master->execute(array(":master_id"=>$masterId));
	            if ($scout_master->rowCount() == 1){
	                $scout_master_row = $scout_master->fetch(PDO::FETCH_ASSOC);

                    $scout_master_id = $scout_master_row['userID'];
                }
            }
            catch (Exception $ex)
            {
                echo $ex->getMessage();
            }
        }
		try
		{							
			$password = md5($upass);
			$stmt = $this->conn->prepare("INSERT INTO users(firstname,lastname,birthdate,role_type,userName,userEmail,userPass,tokenCode, masters_id)
			                                             VALUES(:first_name,:last_name,:birthdate,:role,:user_name, :user_mail, :user_pass, :active_code, :masters_id)");
			$stmt->bindparam(":first_name",$fname);
			$stmt->bindparam(":last_name",$lname);
			$stmt->bindparam(":birthdate",$bdate);
			$stmt->bindparam(":role",$role);
			$stmt->bindparam(":user_name",$uname);
			$stmt->bindparam(":user_mail",$email);
			$stmt->bindparam(":user_pass",$password);
			$stmt->bindparam(":active_code",$code);
			$stmt->bindparam(":masters_id",$scout_master_id);
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
			$stmt = $this->conn->prepare("SELECT * FROM users WHERE userEmail=:email_id");
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
						$this->error = true;
					}
				}
				else
				{
					$this->inactive = true;
				}
			}
			else
			{
				$this->error = true;
			}
		}
		catch(PDOException $ex)
		{
			echo $ex->getMessage();
		}
	}

    public function user_tasks(){
	    $limit = 10;

        $user_tasks = $this->runQuery("SELECT * FROM users
                                            JOIN users_rank_tasks ON users_rank_tasks.user_id = users.userID
                                            WHERE users.userID=:uid
                                            AND users_rank_tasks.status != 'complete'
                                            order by users_rank_tasks.id");
        $user_tasks->execute(array(":uid"=>$_SESSION['userSession']));
        $rank_task_rows = array();
        $completed_user_tasks = $this->runQuery("SELECT users_rank_tasks.rank_task_id FROM users
                                            JOIN users_rank_tasks ON users_rank_tasks.user_id = users.userID
                                            WHERE users.userID=:uid
                                            AND users_rank_tasks.status = 'complete'
                                            order by users_rank_tasks.id");
        $completed_user_tasks->execute(array(":uid"=>$_SESSION['userSession']));
        if ($user_tasks->rowCount() < 1){
            $rank_tasks = $this->runQuery("SELECT * FROM rank_task
                                                order by rank_task.id
                                                limit ".$limit);
            $rank_tasks->execute(array());
            while ($rank_task_row = $rank_tasks->fetch(PDO::FETCH_ASSOC)){
                array_push($rank_task_rows, $this->generate_html_rank_row($rank_task_row, NULL));
            }
        }
        else {
            $index = 0;
            while ($user_task_row = $user_tasks->fetch(PDO::FETCH_ASSOC)) {
                if ($index > $limit){
                    return $rank_task_rows;
                }
                $rank_task_id = $user_task_row['rank_task_id'];
                $rank_task = $this->runQuery("SELECT * FROM rank_task
                                                   WHERE rank_task.id=:rank_task_id");
                $rank_task->execute(array(":rank_task_id"=>$rank_task_id));
                $rank_task_row = $rank_task->fetch(PDO::FETCH_ASSOC);
                if ($rank_task->rowCount() == 1) {
                    while (!empty($this-> last_rank_task) && $this-> last_rank_task['next_task_id'] != $rank_task_row['id']){
                        $next_rank_task = $this->runQuery("SELECT * FROM rank_task
                                                   WHERE rank_task.id=:rank_task_id");
                        $next_rank_task->execute(array(":rank_task_id"=>$this-> last_rank_task['next_task_id']));
                        $next_rank_task_row = $next_rank_task->fetch(PDO::FETCH_ASSOC);
                        if ($rank_task->rowCount() == 1) {
                            array_push($rank_task_rows, $this->generate_html_rank_row($next_rank_task_row, NULL));
                            $index ++;
                        }
                    }
                    array_push($rank_task_rows, $this->generate_html_rank_row($rank_task_row, $user_task_row));
                    $index ++;
                }
            }
            $last_rank_task = $this->runQuery("SELECT * FROM rank_task
                                                    WHERE rank_task.id > :last_task_id");
            $last_rank_task->execute(array(":last_task_id"=>$this-> last_rank_task['id']));
            $completed_task_ids = array();
            while ($completed_task_row = $completed_user_tasks->fetch(PDO::FETCH_ASSOC)){
                array_push($completed_task_ids, $completed_task_row['rank_task_id']);
            }
            while ($rank_task_row = $last_rank_task->fetch(PDO::FETCH_ASSOC)){
                if ($index > $limit){
                    return $rank_task_rows;
                }
                if (in_array($rank_task_row['id'], $completed_task_ids)){
                    continue;
                }
                array_push($rank_task_rows, $this->generate_html_rank_row($rank_task_row, NULL));
                $index ++;
            }
        }
        return $rank_task_rows;
    }

    public function generate_html_rank_row($rank_task_row, $user_rank_task_row){
        $rank_id = $rank_task_row['rank_id'];
        $rank_header = "";
        $rank_name = "N/A";
        $rank = $this->runQuery("SELECT * FROM rank
                                      WHERE rank.id=:task_rank_id");
        $rank->execute(array(":task_rank_id"=>$rank_id));
        $rank_row = $rank->fetch(PDO::FETCH_ASSOC);
        if ($rank->rowCount() == 1){
            $rank_name = ucwords($rank_row['rank_name']);
            if ($rank_id != $this->last_rank_id){
                    $this->last_rank_id = $rank_id;
                    $rank_header = '<tr><th class="h1">'.$rank_name.'</th></tr>';
                }
            }
        $minimum_minutes = $rank_task_row['minimum_minutes'];
        $rank_abv_id = $rank_task_row['rank_alias_id'];
        $checked = "";
        $status = "Incomplete";
        $due_date = "N/A";
        if ($minimum_minutes == 0){
            $minimum_minutes = 4320;
        }
        $rank_task = $rank_task_row['task'];
        $category = ucwords($rank_task_row['category']);
        if (!empty($user_rank_task_row)){
            $status = ucwords($user_rank_task_row['status']);
            $due_date = $user_rank_task_row['due_date'];
            $checked = 'onclick="return false; "checked';
        }
        if ($status == "Incomplete"){
            if (!empty($this->last_due_date)){
                $time = $this->last_due_date;
                $time->add(new DateInterval('PT' . $minimum_minutes . 'M'));
                $this->last_due_date = $time;
                $due_date = $time->format('Y-m-d');
            }
            else{
                $time = new DateTime();
                $time->add(new DateInterval('PT' . $minimum_minutes . 'M'));
                $this->last_due_date = $time;
                $due_date = $time->format('Y-m-d');
            }
        }
        $this-> last_rank_task = $rank_task_row;
        $rank_html = <<< HTML
                    {$rank_header}
                     <tr>
                        <td><input onchange='openJournalModal(this, "{$rank_abv_id}", "{$rank_name}", "{$due_date}");' type='checkbox' {$checked}/></td>
                        <td class="tooltip" data-head="Rank ID">{$rank_abv_id}<span class="tooltiptext">{$rank_name}</td>
                        <td data-head="Current Rank Tasks" class="lalign">{$rank_task}</td>
                        <td data-head="Category">{$category}</td>
                        <td data-head="Due Date">{$due_date}</td>
                        <td data-head="status">{$status}</td>
                      </tr>     
HTML;
        return $rank_html;
    }

    public function record_task_entry($entry, $rank_alias_id, $due_date){
        {
            try
            {
                $rank_task = $this->runQuery("SELECT * FROM rank_task
                                                  WHERE rank_task.rank_alias_id=:rank_alias_id");
                $rank_task->execute(array(":rank_alias_id"=>$rank_alias_id));
                $rank_task_row = $rank_task->fetch(PDO::FETCH_ASSOC);
                if ($rank_task->rowCount() == 1){
                    $rank_task_id = $rank_task_row['id'];
                }
                else{
                    throw new PDOException("Failed to find a valid rank task based on your entry.");
                }
                $status = "pending";
                $stmt = $this->conn->prepare("INSERT INTO users_rank_tasks(user_id,rank_task_id,journal_entry,status,due_date)
			                                             VALUES(:userUID,:rank_task_id,:entry,:status,:due_date)");
                $stmt->bindparam(":userUID",$_SESSION['userSession']);
                $stmt->bindparam(":rank_task_id",$rank_task_id);
                $stmt->bindparam(":entry",$entry);
                $stmt->bindparam(":status",$status);
                $stmt->bindparam(":due_date",$due_date);
                $stmt->execute();
                return true;
            }
            catch(PDOException $ex)
            {
                return $ex->getMessage();
            }
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
		header("Location: $url");
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
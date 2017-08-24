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
	
	public function login($email,$upass,$role_type=null)
	{
	    $role_query = "";
	    if (!empty($role_type)){
	        $role_query = " AND role_type='" . $role_type ."'";
        }
        $query = "SELECT * FROM users WHERE userEmail=:email_id" . $role_query;
		try
		{
			$stmt = $this->conn->prepare($query);
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
            echo $query;
			echo $ex->getMessage();
		}
	}

	public function update_user($max_task_display){
	    try {
            $stmt = $this->runQuery("UPDATE users SET max_task_display=:max_task_display WHERE userID=:uid");
            $stmt->execute(array(":max_task_display"=>$max_task_display,":uid"=>$_SESSION['userSession']));
            return $stmt;
        }
        catch (PDOException $ex){
	        echo $ex->getMessage();
        }
    }

    public function user_tasks($limit){
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
        $completed_task_ids = array();
        while ($completed_task_row = $completed_user_tasks->fetch(PDO::FETCH_ASSOC)){
            array_push($completed_task_ids, $completed_task_row['rank_task_id']);
        }
        if ($user_tasks->rowCount() < 1){
            $completed_tasks = 1;
            if (!empty($completed_task_ids)){
                $completed_tasks = "id not in (". implode(", ", $completed_task_ids) . ")";
            }
            $rank_tasks = $this->runQuery("SELECT * FROM rank_task
                                                WHERE " . $completed_tasks . "
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


    public function user_review_tasks($limit){
        $scout_users = $this->runQuery("SELECT * FROM users
                                             WHERE users.masters_id = :user_id");
        $scout_users->execute(array(":user_id"=>$_SESSION['userSession']));
        $scout_tasks = array();
        if (!$scout_users->rowCount() < 1){
            while ($scout_user_row=$scout_users->fetch(PDO::FETCH_ASSOC)){
                $user_tasks = $this->runQuery("SELECT users.firstname, users.lastname, users_rank_tasks.id,
                                                           rank_task.rank_id, users_rank_tasks.status, users_rank_tasks.journal_entry, 
                                                           users_rank_tasks.due_date, rank_task.rank_alias_id,
                                                           rank_task.task, rank_task.category FROM users
                                                    JOIN users_rank_tasks ON users_rank_tasks.user_id = users.userID
                                                    JOIN rank_task on rank_task.id = users_rank_tasks.rank_task_id
                                                    WHERE users.userID=:uid
                                                    AND users_rank_tasks.status in ('pending', 'need more info')
                                                    order by users_rank_tasks.id
                                                    LIMIT ".$limit);
                $user_tasks->execute(array(":uid"=>$scout_user_row['userID']));
                if (!$user_tasks->rowCount() < 1){
                    array_push($scout_tasks, $this->generate_scout_title($scout_user_row));
                    while ($user_task_row=$user_tasks->fetch(PDO::FETCH_ASSOC)){
                        array_push($scout_tasks, $this->generate_scout_review_html($user_task_row));
                    }
                }
            }
        }
        return $scout_tasks;
    }

    public function generate_scout_title($user){
        $user_name = ucwords($user['firstname'] . " " . $user['lastname']);
        return <<< SCOUTTITLE
                <tr>
                    <th class="h1">{$user_name}</th>
                </tr>
SCOUTTITLE;
    }

    public function generate_scout_review_html($user_task_row){
        $rank_id = $user_task_row['rank_id'];
        $rank_name = "N/A";
        $rank = $this->runQuery("SELECT * FROM rank
                                      WHERE rank.id=:task_rank_id");
        $rank->execute(array(":task_rank_id"=>$rank_id));
        $rank_row = $rank->fetch(PDO::FETCH_ASSOC);
        if ($rank->rowCount() == 1){
            $rank_name = ucwords($rank_row['rank_name']);
        }
        $rank_abv_id = $user_task_row['rank_alias_id'];
        $due_date = $user_task_row['due_date'];
        $rank_task = $user_task_row['task'];
        $category = ucwords($user_task_row['category']);
        $status = ucwords($user_task_row['status']);
        $users_name = ucwords($user_task_row['firstname'] . " " . $user_task_row['lastname']);
        return <<< SCOUTREVIEW
                     <tr>
                        <td><input onchange='openReviewModal(this, "{$rank_abv_id}", "{$users_name}", "{$user_task_row['id']}", "{$user_task_row['journal_entry']}");' type='checkbox'/></td>
                        <td class="tooltip" data-head="Rank ID">{$rank_abv_id}<span class="tooltiptext">{$rank_name}</span></td>
                        <td data-head="Current Rank Tasks" class="lalign">{$rank_task}</td>
                        <td data-head="Category">{$category}</td>
                        <td data-head="Due Date">{$due_date}</td>
                        <td data-head="status">{$status}</td>
                      </tr>
SCOUTREVIEW;
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

    public function record_task_review($user_rank_task_id, $review_status, $review_comment){
        $user_rank_task = $this->runQuery("SELECT * FROM users_rank_tasks 
                                                WHERE users_rank_tasks.id = :user_rank_task_id");
        $user_rank_task->execute(array(':user_rank_task_id'=>$user_rank_task_id));
        if ($user_rank_task->rowCount() == 1){
            $user_rank_task_row = $user_rank_task->fetch(PDO::FETCH_ASSOC);
            if ($review_status == "approve"){
                $review_status_clean = "complete";
            }
            elseif ($review_status == "more_info"){
                $review_status_clean = "need more info";
            }
            else{
                return "Invalid review status picked!";
            }
            try{

                $stmt = $this->conn->prepare("UPDATE users_rank_tasks SET users_rank_tasks.status = '" . $review_status_clean . "' WHERE id = " . $user_rank_task_row['id']);
                $stmt->execute();
                return true;
            }
            catch(PDOException $ex){
                return $ex->getMessage();
            }
        }

    }

    public function get_events($masterId){
        if (!empty($masterId)){
            $user_id = $masterId;
        }
        else {
            $user_id = $_SESSION['userSession'];
        }
        $event_list = $this->runQuery("SELECT * from user_events
                                              WHERE user_id = :user_id");
        $event_list->execute(array(":user_id"=>$user_id));
        $return_list = array();
        while ($event_row=$event_list->fetch(PDO::FETCH_ASSOC)){
            array_push($return_list, $this->generate_event($event_row));
        }
        return $return_list;
    }

    public function generate_event($event_row){
        $event_type = $event_row["event_type"];
        $event_description = $event_row["event_description"];
        $event_date = $event_row["event_date"];
        return <<< EVENT
{
              name: '{$event_type}',
              description: '{$event_description}',
              date: '{$event_date}'
            }
EVENT;
    }

    public function add_new_event($event_date, $even_type, $event_description){
        $event_exists = $this->runQuery("SELECT * from user_events
                                              WHERE user_id = :user_id
                                              AND event_type = :event_type
                                              AND event_date = :event_date");
        $event_exists->execute(array(":user_id"=>$_SESSION['userSession'],
                                     ":event_type"=>$even_type,
                                     ":event_date"=>$event_date));
        if ($event_exists->rowCount() > 0){
            return "The event type " . $even_type . "for " . $event_date . "has already booked.";
        }
        else {
            try
            {
                $stmt = $this->conn->prepare("INSERT INTO user_events(user_id,event_type,event_description,event_date)
			                                             VALUES(:user_id,:event_type,:event_description,:event_date)");
                $stmt->bindparam(":user_id",$_SESSION['userSession']);
                $stmt->bindparam(":event_type",$even_type);
                $stmt->bindparam(":event_description",$event_description);
                $stmt->bindparam(":event_date",$event_date);
                $stmt->execute();
                return null;
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

	public function is_admin()
	{
        $stmt = $this->runQuery("SELECT * FROM users WHERE userID=:uid");
        $stmt->execute(array(":uid"=>$_SESSION['userSession']));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
		if($row['role_type'] == 'admin')
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
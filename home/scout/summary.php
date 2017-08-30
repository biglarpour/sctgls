<?php
/**
 * $user loaded from master/index.php
 */
if(!$userObj->is_logged_in())
{
    $userObj->redirect('/scoutinggoals');
}
if($userObj->is_logged_in()!="") {
//    RANKS
    $ranks_stmt = $userObj->runQuery("SELECT * from rank");
    $ranks_stmt->execute(array());
    $rank_stmt = $userObj->runQuery("SELECT rank_task.rank_id from users_rank_tasks
                                             JOIN rank_task on rank_task.id = users_rank_tasks.rank_task_id
                                             WHERE users_rank_tasks.user_id =:user_id
                                             ORDER BY users_rank_tasks.id DESC 
                                             LIMIT 1");
    $rank_stmt->execute(array(":user_id"=>$_SESSION['userSession']));
    $current_rank_row = $rank_stmt->fetch(PDO::FETCH_ASSOC);
    $current_rank = 1;
    if (!empty($current_rank_row)){
        $current_rank = $current_rank_row['rank_id'];
    }
    $ranks_html_list = array();
    $current_rank_html = "";
    while ($rank_row=$ranks_stmt->fetch(PDO::FETCH_ASSOC)){
        $completed = "";
        if ($rank_row['id'] <= $current_rank){
            $completed = "completed";
        }
        $rank_name = ucwords($rank_row['rank_name']);
        $short_rank_name = str_replace(array('second', 'first'), array('2nd', '1st'), $rank_row['rank_name']);
        if ($rank_row['id'] == $current_rank){
            $current_rank_html = <<< CUR_RANK
<h2>CURRENT RANK: {$rank_name}</h2>
CUR_RANK;
        }
        $rank_html = <<< RANK
<a href="" onclick="openSummaryModal(this);return false;" data-title="{$rank_name}" class="summary-btn {$completed}">{$short_rank_name}</a>
RANK;
        array_push($ranks_html_list, $rank_html);
    }
    $ranks_html = implode("\n", $ranks_html_list);
//    RANK_TASKS
    $rank_tasks = $userObj->runQuery("SELECT rank_task.rank_alias_id, rank_task.task, users_rank_tasks.status
                                           FROM rank_task
                                           LEFT JOIN users_rank_tasks on rank_task.id = users_rank_tasks.rank_task_id and users_rank_tasks.user_id =:user_id
                                           WHERE rank_task.rank_id =:rank_id");
    $rank_tasks->execute(array(':rank_id'=>$current_rank,
                               ":user_id"=>$_SESSION['userSession']));
    $rank_tasks_html_list = array();
    $task_index = 0;
    $next_tasks_html_list = array();
    while ($rank_task_row = $rank_tasks->fetch(PDO::FETCH_ASSOC)){
        $rank_tasks_compeleted = "";
        $rank_task_description = htmlentities($rank_task_row['task'], ENT_QUOTES);
        $rank_task_description = preg_replace( "/\r|\n/", "<br>", $rank_task_description);
        if (empty($rank_task_row['status'])){
            $rank_task_status = "Incomplete";
            if ($task_index < 5){
                $next_html = <<< NEXT_TASK
<a href="" onclick="openSummaryModal(this);return false;" data-title="{$rank_task_row['rank_alias_id']}" data-body="{$rank_task_description}" class="summary-btn-sqr">
  <div class="wrap">
    <p>{$rank_task_description}</p>
  </div>
</a>
NEXT_TASK;
                array_push($next_tasks_html_list, $next_html);
                $task_index++;

            }
        }
        elseif ($rank_task_row['status'] == 'complete'){
            $rank_tasks_compeleted = 'completed';
            $rank_task_status = $rank_task_row['status'];
        }
        else {
            $rank_task_status = $rank_task_row['status'];
        }
        $rank_task_body = $rank_task_description . "<br>" . $rank_task_status;
        $rank_task_html = <<< RANK_TASK
<a href="" onclick="openSummaryModal(this);return false;" data-title="{$rank_task_row['rank_alias_id']}" data-body="{$rank_task_body}" class="summary-btn-small {$rank_tasks_compeleted}">{$rank_task_row['rank_alias_id']}</a>
RANK_TASK;
        array_push($rank_tasks_html_list, $rank_task_html);

    }
    $rank_tasks_html = implode("\n", $rank_tasks_html_list);
    $next_tasks_html = implode("\n", $next_tasks_html_list);
//    UP COMING EVENTS
    $upcoming_events = $userObj->get_upcoming_events($user['masters_id']);
    $upcoming_events_html_list = array();
    for ($i=0 ;$i < count($upcoming_events); $i++) {
        $event_row = $upcoming_events[$i];
        $event_title = "Upcoming Event on " . $event_row['event_date'];
        $event_description = htmlspecialchars($event_row["event_description"], ENT_QUOTES, $double_encode=false);
        $event_description = preg_replace( "/\r|\n/", "<br>", $event_description);
        $event_html = <<< EVENT
<a href="" onclick="openSummaryModal(this);return false;" data-title="{$event_title}" data-body="{$event_description}" class="summary-btn-sqr">
  <div class="event wrap">
    <h4>{$event_row['event_date']}</h4>
    <p>{$event_description}</p>
  </div>
</a>
EVENT;
        array_push($upcoming_events_html_list, $event_html);
    }
    $upcoming_events_html = implode("\n", $upcoming_events_html_list);
    $SUMMARY_HTML = <<< HTML
<div id="summary-content">
    <div class="current-rank">
        {$current_rank_html}
        {$ranks_html}
    </div>
    <div>
        <h2>REQUIRED BADGES</h2>
        {$rank_tasks_html}
    </div>
    <div>
        <h2>WHAT TO DO NEXT</h2>
        {$next_tasks_html}
    </div>
    <div>
        <h2>UPCOMING EVENTS</h2>
        {$upcoming_events_html}
    </div>
</div>
<!-- Modal content -->
<div id="summaryModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <a href="" onclick="return false;"><span class="summary-modal-close">&times;</span></a>
            <h2 id="summary_title">Summary</h2>
        </div>
        <div class="modal-body">
            <p id="summary_body_text"></p>
        </div>
        <div class="modal-footer">
            <button class="summary_submit" name="btn-summary">Close</button>
        </div>
    </div>
</div>
HTML;

}
?>
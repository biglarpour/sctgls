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
<a href="" onclick="openSummaryModal('{$rank_name}');return false;" class="summary-btn {$completed}">{$short_rank_name}</a>
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
    while ($rank_task_row = $rank_tasks->fetch(PDO::FETCH_ASSOC)){
        $rank_tasks_compeleted = "";
        if (empty($rank_task_row['status'])){
            $rank_task_status = "Incomplete";
        }
        elseif ($rank_task_row['status'] == 'complete'){
            $rank_tasks_compeleted = 'completed';
            $rank_task_status = $rank_task_row['status'];
        }
        else {
            $rank_task_status = $rank_task_row['status'];
        }
        $rank_task_body = $rank_task_row['task'] . "<br>" . $rank_task_status;
        $rank_task_html = <<< RANK_TASK
<a href="" onclick="openSummaryModal('{$rank_task_row['rank_alias_id']}', '{$rank_task_body}');return false;" class="summary-btn-small {$rank_tasks_compeleted}">{$rank_task_row['rank_alias_id']}</a>
RANK_TASK;
        array_push($rank_tasks_html_list, $rank_task_html);

    }
    $rank_tasks_html = implode("\n", $rank_tasks_html_list);
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
        <a href="" onclick="openSummaryModal('Visit Museum');return false;" class="summary-btn-sqr">
          <div class="wrap">
            <p>Visit Museum</p>
          </div>
        </a>
        <a href="" onclick="openSummaryModal('Understand and agree to live by the Scout Oath, Scout Law, motto, slogan, and the outdoor Cod');return false;" class="summary-btn-sqr">
          <div class="wrap">
            <p>Understand and agree to live by the Scout Oath, Scout Law, motto, slogan, and the outdoor Cod</p>
          </div>
        </a>
        <a href="" onclick="openSummaryModal('Teach another scout how to treat for shock');return false;" class="summary-btn-sqr">
          <div class="wrap">
            <p>Teach another scout how to treat for shock</p>
          </div>
        </a>
        <a href="" onclick="openSummaryModal('Set up a tent');return false;" class="summary-btn-sqr">
          <div class="wrap">
            <p>Set up a tent</p>
          </div>
        </a>
        <a href="" onclick="openSummaryModal('Identify 10 local animals');return false;" class="summary-btn-sqr">
          <div class="wrap">
            <p>Identify 10 local animals</p>
          </div>
        </a>
    </div>
    <div>
        <h2>UPCOMING EVENTS</h2>
        <a href="" onclick="openSummaryModal('2017-08-11 Event We are going to teach everyone how to tie a knot');return false;" class="summary-btn-sqr">
          <div class="event wrap">
            <h4>2017-08-11</h4>
            <p>We are going to teach everyone how to tie a knot</p>
          </div>
        </a>
        <a href="" onclick="openSummaryModal('2017-08-11 Meetup At the park around the corner');return false;" class="summary-btn-sqr">
          <div class="event wrap">
            <h4>2017-08-11</h4>
            <p>At the park around the corner</p>
          </div>
        </a>
        <a href="" onclick="openSummaryModal('2017-08-12 Camping Start the camping adventures');return false;" class="summary-btn-sqr">
          <div class="event wrap">
            <h4>2017-08-12</h4>
            <p>Start the camping adventures</p>
          </div>
        </a>
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
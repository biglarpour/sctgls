<?php
/**
 * $userObj and $user loaded from master/index.php
 */
if(!$userObj->is_logged_in())
{
    $userObj->redirect('/scoutinggoals');
}
if(isset($_POST['btn-record-task']))
{
    $journal_entry = trim($_POST['journal_entry']);
    $rank_alias_id = trim($_POST['rank_alias_id']);
    $rank_due_date = trim($_POST['rank_due_date']);
    $response = $userObj->record_task_entry($journal_entry, $rank_alias_id, $rank_due_date);
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}
if($userObj->is_logged_in()!="") {
    $tbody = implode("\n", $userObj->user_tasks($user['max_task_display']));
    $SCOUT_TASK_HTML = <<< HTML
<body>
 <div id="table-wrapper">
  <table id="keywords" cellspacing="0" cellpadding="0">
    <thead>
      <tr>
        <th></th>
        <th><span>Rank ID</span></th>
        <th><span>Current Rank Tasks</span></th>
        <th><span>Category</span></th>
        <th><span>Due Date</span></th>
        <th><span>Status</span></th>
      </tr>
    </thead>
    <tbody>
      {$tbody}
    </tbody>
  </table>
 </div> 
 
<div id="journalModal" class="modal">

  <!-- Modal content -->
    <div class="modal-content">
        <div class="modal-header">
            <span class="journal-modal-close">&times;</span>
            <h2 id="modal_title">Journal</h2>
        </div>
        <div class="modal-body">
            <form class="form-rank-task" method="post">
                <textarea rows='4' name="journal_entry" class="boxsizingBorder" placeholder="Write a few lines here about your assignment for the current badge that you've completed. Some badges require you the write a summary of your fitness, finance, environment study, but we encourage you to do it for every task." ></textarea>
                <input id="rank_alias_id" type="hidden" name="rank_alias_id" >
                <input id="rank_due_date" type="hidden" name="rank_due_date" >
                <div class="modal-footer">
                    <button class="journal_submit" name="btn-record-task">Submit Journal</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>

HTML;
}
?>
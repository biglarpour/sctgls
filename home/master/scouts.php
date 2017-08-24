<?php
/**
 * $user loaded from master/index.php
 */
if(!$userObj->is_logged_in())
{
    $userObj->redirect('/scoutinggoals');
}
if(isset($_POST['btn-record-review']))
{
    $review_status = trim($_POST['reviewStatus']);
    $user_rank_task_id = trim($_POST['user_rank_task_id']);
    $review_comment = trim($_POST['review_comment']);
    $response = $userObj->record_task_review($user_rank_task_id, $review_status, $review_comment);
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}
if($userObj->is_logged_in()!="") {
    $tbody = implode("\n", $userObj->user_review_tasks($user['max_task_display']));
    $SCOUT_TASK_HTML = <<< HTML
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
 
<div id="reviewModal" class="modal">

  <!-- Modal content -->
    <div class="modal-content">
        <div class="modal-header">
            <a href="" onclick="return false;"><span class="review-modal-close">&times;</span></a>
            <h2 id="modal_title">Review</h2>
        </div>
        <div class="modal-body">
            <form id="form-rank-review" method="post">
                <label id="journal_entry"></label>
                <input id="user_rank_task_id" type="hidden" name="user_rank_task_id" >
                <div class="approveStatus">
                    <input name="reviewStatus" type="checkbox" value="approve">Approve
                </div>
                <div class="moreInfo" >
                    <input name="reviewStatus" id="requestTextArea" type="checkbox" value="more_info">Request More information
                </div>
                <textarea id="reviewTextArea" style="display: none;" rows='4' name="review_comment"  class="boxsizingBorder" placeholder="Write a comment back to Scout"></textarea>
                <div class="modal-footer">
                    <button class="review_submit" name="btn-record-review">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

HTML;
}
?>
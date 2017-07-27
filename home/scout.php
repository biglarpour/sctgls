<?php
session_start();
require_once '../login/class.user.php';
$user = new USER();
if(!$user->is_logged_in())
{
    $user->redirect('/scoutinggoals');
}

if($user->is_logged_in()!="") {
    $tbody = implode("\n", $user->user_tasks());
    $SCOUT_HTML = <<< HTML
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
            <h2>Journal</h2>
        </div>
        <div class="modal-body">
            <textarea rows='4' class="boxsizingBorder" placeholder="Write a few lines here about your assignment for the current badge that you've completed. Some badges require you the write a summary of your fitness, finance, environment study, but we encourage you to do it for every task." ></textarea>
        </div>
        <div class="modal-footer">
            <button class="journal_submit">Submit Journal</button>
        </div>
    </div>
</div>

</body>

HTML;
}
?>
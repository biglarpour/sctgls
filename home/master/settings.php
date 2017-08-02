<?php
/**
 * $userObj and $user loaded from master/index.php
 */
if(!$userObj->is_logged_in())
{
    $userObj->redirect('/scoutinggoals');
}
if(isset($_POST['btn-settings-save']))
{
    $max_task_display = trim($_POST['max_task_display']);
    $response = $userObj->update_user($max_task_display);
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}
if(isset($_POST['btn-reset-password']))
{
    $id = base64_encode($user['userID']);
    $code = $user['tokenCode'];
    header("Location: https://biglarpour.com/scountinggoals/login/resetpass.php?id=$id&code=$code");
    exit();
}
if($userObj->is_logged_in()!="") {
    $master_token = $user['tokenCode'];
    $max_task_display = $user['max_task_display'];
    $SETTINGS_HTML = <<< HTML
<body>
<form class="form-settings" method="post">
 <div id="table-wrapper">
  <table id="keywords" cellspacing="0" cellpadding="0">
    <thead>
    </thead>
    <tbody>
      <tr>
        <td>Master's Token</td>
        <td>{$master_token}</td>
      </tr>
      <tr>
        <td>Max number of tasks to display per scout</td>
        <td><input type="number" name="max_task_display" value="{$max_task_display}"</td>
      </tr>
      <tr><td><button class="settings_save" name="btn-settings-save">Save</button></td></tr>
      <tr><td><button class="reset_password" name="btn-reset-password">Reset Password</button></td></tr>
    </tbody>
  </table>
 </div> 
</form>
</body>
HTML;
}

?>

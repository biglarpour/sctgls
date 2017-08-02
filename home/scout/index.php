<?php
session_start();
require_once '../login/class.user.php';
$userObj = new USER();

if(!$userObj->is_logged_in())
{
    $userObj->redirect('/scoutinggoals');
}
$stmt = $userObj->runQuery("SELECT * FROM users WHERE userID=:uid");
$stmt->execute(array(":uid"=>$_SESSION['userSession']));
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$first_name = $user['firstname'];
$last_name = $user['lastname'];
require_once 'scout.php';
require_once 'settings.php';

include 'header.php';
$header = ob_get_contents();
ob_end_clean();
$SCOUT_HTML = <<< HTML
{$header}
<!--Main area-->
<div class="hero-wrapper" >
    <section id="main" class="clearfix">
        <article id="summary" class="grid_12 default">
            <h1 class="hero_user_name">{$first_name} {$last_name}</h1>
            <img  class="hero-summary-img"  width="100%" src="/scoutinggoals/images/summary_main.png" />
        </article><!-- end summary -->
        <article id="scout" class="grid_12">
            <h1 class="hero_scout_user_name">{$first_name} {$last_name}</h1>
            {$SCOUT_TASK_HTML}
            <img  id="hero-scout-img"  width="100%" src="/scoutinggoals/images/summary_main.png" />
        </article>
        <article id="settings" class="grid_12">
            <h1 class="hero_scout_user_name">{$first_name} {$last_name}</h1>
            {$SETTINGS_HTML}
            <img  id="hero-scout-img"  width="100%" src="/scoutinggoals/images/summary_main.png" />
        </article><!-- end troop -->
    </section><!-- end main area -->
</div>
HTML;
?>

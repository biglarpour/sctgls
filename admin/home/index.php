<?php
session_start();
require_once '../../login/class.user.php';
$user_home = new USER();

if(!$user_home->is_logged_in() or !$user_home->is_admin())
{
    $user_home->redirect('/scoutinggoals/admin/');
}
$row_list = array();
if(isset($_POST['btn-save_ranks'])){
    $cnt = count($_POST['id']);
    for($i=0;$i<$cnt;$i++){
        $camping = null;
        $outing = null;
        if (!empty($_POST['camping'][$i])){
            $camping = 1;
        }
        if (!empty($_POST['outing'][$i])){
            $outing = 1;
        }
        try {
            $stmt = $user_home->runQuery("UPDATE rank_task SET rank_alias_id = :rank_alias_id,
                                                                    task = :task, 
                                                                    category = :category, 
                                                                    camping = :camping, 
                                                                    outing = :outing, 
                                                                    minimum_minutes = :minimum_minutes, 
                                                                    next_task_id = :next_task_id WHERE id=:id");
            $stmt->execute(array(
                    ":id"=>$_POST['id'][$i],
                    ":rank_alias_id"=>$_POST['rank_alias_id'][$i],
                    ":task"=>$_POST['task'][$i],
                    ":category"=>$_POST['category'][$i],
                    ":camping"=>$camping,
                    ":outing"=>$outing,
                    ":minimum_minutes"=>$_POST['minimum_minutes'][$i],
                    ":next_task_id"=>$_POST['next_task_id'][$i],
        ));
        }
        catch(PDOException $ex){
            echo $ex->getMessage();
        }
    };
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}
elseif (isset($_POST['btn-add-new'])){
    $camping = null;
    $outing = null;
    if (!empty($_POST['camping'])){
        $camping = 1;
    }
    if (!empty($_POST['outing'])){
        $outing = 1;
    }
    $rank_stmt = $user_home->runQuery("SELECT id from rank where rank_name=:rank_name");
    $rank_stmt->execute(array(":rank_name"=>$_POST['rank_name']));
    if (!$rank_stmt->rowCount() == 1){
        echo "Failed to find a valid rank with rank name " . $_POST['rank_name'] ;
        return;
    }
    $rank_row = $rank_stmt->fetch(PDO::FETCH_ASSOC);
    try {
        $stmt = $user_home->runQuery("INSERT INTO rank_task(rank_id, rank_alias_id,task,category,camping,outing,minimum_minutes,next_task_id)
                                                  VALUES(:rank_id,:rank_alias_id,:task,:category,:camping,:outing,:minimum_minutes,:next_task_id)");

        $stmt->execute(array(
            ":rank_id"=>$rank_row['id'],
            ":rank_alias_id"=>$_POST['rank_alias_id'],
            ":task"=>$_POST['task'],
            ":category"=>$_POST['category'],
            ":camping"=>$camping,
            ":outing"=>$outing,
            ":minimum_minutes"=>$_POST['minimum_minutes'],
            ":next_task_id"=>$_POST['next_task_id'],
        ));
    }
    catch(PDOException $ex){
        echo $ex->getMessage();
    }
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

function generate_row_html($sql_row, $ranks_list){
    $ranks_html = array();
    for($i=0;$i<count($ranks_list);$i++){
        $rank_row = $ranks_list[$i];
        $rank_name_upper = ucfirst($rank_row['rank_name']);
        $selected = "";
        if ($rank_row['id'] == $sql_row['rank_id']){
            $selected = "selected";
        }
        $rank_html = <<< RANK_HTML
<option value="{$rank_row['rank_name']}" {$selected}>{$rank_name_upper}</option>
RANK_HTML;

        array_push($ranks_html, $rank_html);
    }
    $ranks_html_str = implode("\n", $ranks_html);
    $camping_checked = "";
    if (!empty($sql_row['camping'])){
        $camping_checked = "checked";
    }
    $outing_checked = "";
    if (!empty($sql_row['camping'])){
        $outing_checked = "checked";
    }
    return <<< ROW_HTML
<tr>
    <td class="rank_task_checkbox" data-head="id">
        <input type="checkbox" onchange="unlockRankTask(this,'rank_task_{$sql_row["rank_alias_id"]}')" name="id[]" value="{$sql_row["id"]}" />
        <span>{$sql_row["id"]}</span>
    </td>
    <td data-head="rank">
        <select id="rank" name="rank" disabled class='rank_task_{$sql_row["rank_alias_id"]}'> 
            {$ranks_html_str}
        </select>
    </td>
    <td data-head="rank_alias_id"><textarea style="width:80px;" disabled name="rank_alias_id[]"  class='rank_task_{$sql_row["rank_alias_id"]}'>{$sql_row["rank_alias_id"]}</textarea></td>
    <td data-head="task"><textarea style="width:235px;" disabled name="task[]" rows="3" class='rank_task_{$sql_row["rank_alias_id"]}'>{$sql_row["task"]}</textarea></td>
    <td data-head="category"><textarea style="width:150px;" disabled name="category[]"  class='rank_task_{$sql_row["rank_alias_id"]}'>{$sql_row["category"]}</textarea></td>
    <td data-head="camping"><input type="checkbox" disabled name="camping[]"  class='rank_task_{$sql_row["rank_alias_id"]}' {$camping_checked}></td>
    <td data-head="outing"><input type="checkbox" disabled name="outing[]"  class='rank_task_{$sql_row["rank_alias_id"]}' {$outing_checked}></td>
    <td data-head="minimum_minutes"><input style="width:100px;" type="number" disabled name="minimum_minutes[]"  class='rank_task_{$sql_row["rank_alias_id"]}' value="{$sql_row["minimum_minutes"]}"/></td>
    <td data-head="next_task_id"><input style="width:60px;" type="number" disabled name="next_task_id[]"  class='rank_task_{$sql_row["rank_alias_id"]}' value="{$sql_row["next_task_id"]}"/></td>
</tr>
ROW_HTML;

}

$rs_stmt = $user_home->runQuery("SELECT * FROM rank_task ORDER BY next_task_id");
$rs_stmt->execute(array());

$r_stmt = $user_home->runQuery("SELECT * FROM rank");
$r_stmt->execute(array());
$ranks_sql_list = array();

while ($rank_row = $r_stmt->fetch(PDO::FETCH_ASSOC)) {
    array_push($ranks_sql_list, $rank_row);
}
$last_rank_task_id = null;
while ($row = $rs_stmt->fetch(PDO::FETCH_ASSOC)){
    array_push($row_list, generate_row_html($row, $ranks_sql_list));
    $last_rank_task_id = $row['id'];
}

?>
<!DOCTYPE html>
<html class="no-js lt-ie9 lt-ie8 lt-ie7">
<html class="no-js lt-ie9 lt-ie8">
<html class="no-js lt-ie9">
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title>Scouting Goals</title>
    <meta name="description" content="Boys scouts application to help you achieve you goals">
    <meta name="keywords" content="">

    <!-- Mobile viewport -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">

    <link rel="shortcut icon" href="/scoutinggoals/images/favicon.ico"  type="image/x-icon">

    <!-- CSS-->
    <!-- Google web fonts. You can get your own bundle at http://www.google.com/fonts. Don't forget to update the CSS accordingly!-->
    <link href='https://fonts.googleapis.com/css?family=Droid+Serif|Ubuntu' rel='stylesheet' type='text/css'>

    <link rel="stylesheet" href="/scoutinggoas/css/normalize.css">
    <link rel="stylesheet" href="/scoutinggoas/js/flexslider/flexslider.css">
    <link rel="stylesheet" href="/scoutinggoas/css/basic-style.css">

    <!-- end CSS-->

    <!-- JS-->
    <script src="/scoutinggoals/js/libs/modernizr-2.6.2.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="js/libs/jquery-1.9.0.min.js">\x3C/script>')</script>
    <!-- end JS-->

</head>

<body id="home">
<?php include 'header.php';?>
<div class="admin-wrapper" >
    <section id="main" class="clearfix">
        <article id="admin_rank_tasks" class="grid_12 default">
            <form class="form-admin-rank" method="post">
                <div id="table-wrapper">
                    <table id="keywords" cellspacing="0" cellpadding="0">
                        <thead>
                            <th>id</th>
                            <th>rank</th>
                            <th>rank_alias_id</th>
                            <th>task</th>
                            <th>category</th>
                            <th>camping</th>
                            <th>outing</th>
                            <th>minimum_minutes</th>
                            <th>next_task_id</th>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <button class="btn btn-large btn-primary" type="submit" name="btn-save_ranks">SAVE</button>
                            </td>
                            <td>
                                <button class="btn btn-large btn-primary" onclick="openNewRankTaskModal();return false;" name="btn-save_ranks">ADD NEW</button>
                            </td>
                        </tr>
                        <?php echo implode("\n", $row_list); ?>
                        <tr>
                            <td>
                                <button class="btn btn-large btn-primary" type="submit" name="btn-save_ranks">SAVE</button>
                            </td>
                            <td>
                                <button class="btn btn-large btn-primary" onclick="openNewRankTaskModal();return false;" name="btn-save_ranks">ADD NEW</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </form>
            <img  id="hero-scout-img" class="admin-image"  width="100%" src="/scoutinggoals/images/admin_bg.jpg" />
        </article><!-- end rank tasks -->
    </section><!-- end main area -->
</div>

<div id="newRankTaskModal" class="modal">

    <!-- Modal content -->
    <div class="modal-content">
        <div class="modal-header">
            <a href="" onclick="return false;"><span class="nrt-modal-close">&times;</span></a>
            <h2 id="modal_title">Add New Rank Task Record</h2>
        </div>
        <div class="modal-body">
            <form id="form-new-rank_task" method="post">
                <div class="new-rank-wrapper">
                    <table id="keywords" cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr>
                                <td>
                                    <label>Rank</label>
                                </td>
                                <td>
                                    <select id="rank_name" name="rank_name">
                                        <option value="" disabled selected>Select Rank</option>
                                        <option value="scout" >Scout</option>
                                        <option value="tenderfoot" >TenderFoot</option>
                                        <option value="second class" >Second Class</option>
                                        <option value="first class" >First Class</option>
                                        <option value="star" >Star</option>
                                        <option value="life" >Life</option>
                                        <option value="eagle" >Eagle</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><label>Rank Alias Id</label></td>
                                <td>
                                    <input type="text" name="rank_alias_id" placeholder="Rank Alias ID" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Task Description</label>
                                </td>
                                <td>
                                    <textarea type="text"  class="boxsizingBorder" name="task" rows="2" placeholder="Task Description" ></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Category</label>
                                </td>
                                <td>
                                    <input type="text" name="category"  placeholder="Category"/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Camping</label>
                                </td>
                                <td>
                                    <input type="checkbox"  name="camping"/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Outing</label>
                                </td>
                                <td>
                                    <input type="checkbox"  name="outing"/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Minimum Time in minutes</label>
                                </td>
                                <td>
                                    <input type="number"  name="minimum_minutes"/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Next Task Id</label>
                                </td>
                                <td>
                                    <input type="number"  name="next_task_id" id="next_task_id" value='<?php echo $last_rank_task_id + 2 ?>'/>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>
                                    <div class="modal-footer">
                                        <button class="add_new_rank_task" name="btn-add-new">SAVE</button>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>


<?php include '/scoutinggoals/footer.php';?>

<!-- jQuery -->


<script defer src="/scoutinggoas/js/flexslider/jquery.flexslider-min.js"></script>

<!-- fire ups - read this file!  -->
<script src="/scoutinggoas/js/main.js"></script>

</body>
</html>

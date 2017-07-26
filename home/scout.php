<?php

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
      <tr>
        <th class="h1">Scout</th>
      </tr>
      <tr>
        <td><input onchange='openJournalModal(this, "S8");' type='checkbox' checked/></td>
        <td data-head="Rank ID">S8</td>
        <td data-head="Current Rank Tasks" class="lalign">Describe the Scout badge.</td>
        <td data-head="Category">Joining</td>
        <td data-head="Due Date">07/15/2017</td>
        <td data-head="status">Pending</td>
      </tr>
      <tr>
        <td><input onchange='openJournalModal(this, "S9");' type='checkbox'/></td>
        <td data-head="Rank ID">S9</td>
        <td data-head="Current Rank Tasks" class="lalign">Complete the pamphlet exercises. With your parent or guardian, complete the exercises in the pamphlet "How to Protect Your Children from Child Abuse: A Parent's Guide".</td>
        <td data-head="Category">Joining</td>
        <td data-head="Due Date">07/31/2017</td>
        <td data-head="status">Incomplete</td>
      </tr>
      <tr>
        <td><input onchange='openJournalModal(this, "S10");' type='checkbox'/></td>
        <td data-head="Rank ID">S10</td>
        <td data-head="Current Rank Tasks" class="lalign">Participate in a Scoutmaster conference. Turn in your Boy Scout application and health history form signed by your parent or guardian, then participate in a Scoutmaster conference.</td>
        <td data-head="Category">Joining</td>
        <td data-head="Due Date">08/03/2017</td>
        <td data-head="status">Incomplete</td>
      </tr>
      <tr>
        <th class="h1">Tenderfoot</th>
      </tr>
      <tr>
        <td><input onchange='openJournalModal(this, "T1");' type='checkbox'/></td>
        <td data-head="Rank ID">T1</td>
        <td data-head="Current Rank Tasks" class="lalign">Present yourself to your leader, properly dressed, before going on an overnight camping trip. Show the camping gear you will use. Show the right way to pack and carry it.</td>
        <td data-head="Category">Camping</td>
        <td data-head="Due Date">09/01/2017</td>
        <td data-head="status">Incomplete</td>
      </tr>
      <tr>
        <td><input onchange='openJournalModal(this, "T2");' type='checkbox'/></td>
        <td data-head="Rank ID">T2</td>
        <td data-head="Current Rank Tasks" class="lalign">Spend at least one night on a patrol or troop campout. Sleep in a tent you have helped pitch.</td>
        <td data-head="Category">Camping</td>
        <td data-head="Due Date">09/15/2017</td>
        <td data-head="status">Incomplete</td>
      </tr>
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

?>
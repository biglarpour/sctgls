<?php

$TROOP_HTML = <<< HTML
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
        <th></th>
        <td class="h1">Scout</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <th><input onchange='handleChange(this, "S8");' type='checkbox'/></th>
        <td>S8</td>
        <td class="lalign">Describe the Scout badge.</td>
        <td>Joining</td>
        <td>07/15/2017</td>
        <td>Incomplete</td>
      </tr>
      <tr>
        <th><input onchange='handleChange(this, "S9");' type='checkbox'/></th>
        <td>S9</td>
        <td class="lalign">Complete the pamphlet exercises. With your parent or guardian, complete the exercises in the pamphlet "How to Protect Your Children from Child Abuse: A Parent's Guide".</td>
        <td>Joining</td>
        <td>07/31/2017</td>
        <td>Incomplete</td>
      </tr>
      <tr>
        <th><input onchange='handleChange(this, "S10");' type='checkbox'/></th>
        <td>S10</td>
        <td class="lalign">Participate in a Scoutmaster conference. Turn in your Boy Scout application and health history form signed by your parent or guardian, then participate in a Scoutmaster conference.</td>
        <td>Joining</td>
        <td>08/03/2017</td>
        <td>Incomplete</td>
      </tr>
      <tr>
        <th></th>
        <td class="h1">Tenderfoot</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <th><input onchange='handleChange(this, "T1");' type='checkbox'/></th>
        <td>T1</td>
        <td class="lalign">Present yourself to your leader, properly dressed, before going on an overnight camping trip. Show the camping gear you will use. Show the right way to pack and carry it.</td>
        <td>Camping</td>
        <td>09/01/2017</td>
        <td>Incomplete</td>
      </tr>
    </tbody>
  </table>
 </div> 
 
 <style>
/* The Modal (background) */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    padding-top: 100px; /* Location of the box */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
    position: relative;
    background-color: #fefefe;
    margin: auto;
    padding: 0;
    border: 1px solid #888;
    width: 80%;
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
    -webkit-animation-name: animatetop;
    -webkit-animation-duration: 0.4s;
    animation-name: animatetop;
    animation-duration: 0.4s
}

/* Add Animation */
@-webkit-keyframes animatetop {
    from {top:-300px; opacity:0} 
    to {top:0; opacity:1}
}

@keyframes animatetop {
    from {top:-300px; opacity:0}
    to {top:0; opacity:1}
}

/* The Close Button */
.close {
    color: white;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}

.modal-header {
    padding: 2px 16px;
    background-color: #3EC3C6;
    color: white;
}

.modal-body {
    margin: 10px 5px;
    padding: 2px 16px;
    }

.modal-footer {
    padding: 2px 16px;
    background-color: #3EC3C6;
    color: white;
}
.boxsizingBorder {
    width: 100%;
    -webkit-box-sizing: border-box;
       -moz-box-sizing: border-box;
            box-sizing: border-box;
}
.journal_submit {
    height: 2em;
    margin: 5px;
}

</style>
<div id="myModal" class="modal">

  <!-- Modal content -->
    <div class="modal-content">
        <div class="modal-header">
            <span class="modal-close">&times;</span>
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
<script>
// Get the modal
var modal = document.getElementById('myModal');

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("modal-close")[0];
var submit_journal = document.getElementsByClassName("journal_submit")[0];

// When the user clicks the button, open the modal 
function handleChange(cb, rankID) {
  if(cb.checked == true){
   modal.style.display = "block";
  }else{
   modal.style.display = "none";
  }
}
// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}
submit_journal.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>

HTML;

?>
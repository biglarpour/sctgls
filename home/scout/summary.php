<?php
/**
 * $user loaded from master/index.php
 */
if(!$userObj->is_logged_in())
{
    $userObj->redirect('/scoutinggoals');
}
if($userObj->is_logged_in()!="") {
    $SUMMARY_HTML = <<< HTML
<div id="summary-content">
    <div class="current-rank">
        <h2>CURRENT RANK: Scout</h2>
        <a href="" onclick="openSummaryModal('Scout');return false;" class="summary-btn completed">scout</a>
        <a href="" onclick="openSummaryModal('Tender Foot');return false;" class="summary-btn">tender ft</a>
        <a href="" onclick="openSummaryModal('2nd Class');return false;" class="summary-btn">2nd class</a>
        <a href="" onclick="openSummaryModal('1st Class');return false;" class="summary-btn">1st class</a>
        <a href="" onclick="openSummaryModal('Star');return false;" class="summary-btn">star</a>
        <a href="" onclick="openSummaryModal('Life');return false;" class="summary-btn">life</a>
        <a href="" onclick="openSummaryModal('Eagle');return false;" class="summary-btn">eagle</a>
    </div>
    <div>
        <h2>REQUIRED BADGES</h2>
        <a href="" onclick="openSummaryModal('Scout');return false;" class="summary-btn-small completed">S1</a>
        <a href="" onclick="openSummaryModal('Tender Foot');return false;" class="summary-btn-small completed">S2</a>
        <a href="" onclick="openSummaryModal('2nd Class');return false;" class="summary-btn-small completed">S3</a>
        <a href="" onclick="openSummaryModal('1st Class');return false;" class="summary-btn-small completed">S4</a>
        <a href="" onclick="openSummaryModal('Star');return false;" class="summary-btn-small">S5</a>
        <a href="" onclick="openSummaryModal('Life');return false;" class="summary-btn-small">S6</a>
        <a href="" onclick="openSummaryModal('Eagle');return false;" class="summary-btn-small">S7</a>
        <a href="" onclick="openSummaryModal('Eagle');return false;" class="summary-btn-small">S8</a>
        <a href="" onclick="openSummaryModal('Eagle');return false;" class="summary-btn-small">S9</a>
        <a href="" onclick="openSummaryModal('Eagle');return false;" class="summary-btn-small">S10</a>
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
            <p></p>
        </div>
        <div class="modal-footer">
            <button class="summary_submit" name="btn-summary">Close</button>
        </div>
    </div>
</div>
HTML;

}
<?php
/**
 *
 */



$CALENDAR_HTML = <<< 'HTML'
<link rel="stylesheet" href="/scoutinggoals/js/calendar/css/semantic.ui.min.css">
<link rel="stylesheet" href="/scoutinggoals/js/calendar/css/prism.css" />
<link rel="stylesheet" href="/scoutinggoals/js/calendar/css/calendar-style.css" />
<link rel="stylesheet" href="/scoutinggoals/js/calendar/css/style.css" />
<link rel="stylesheet" href="/scoutinggoals/js/calendar/css/pignose.calendar.css" />
<script defer src="/scoutinggoals/js/calendar/js/jquery.latest.min.js"></script>
<script defer src="/scoutinggoals/js/calendar/js/moment.latest.min.js"></script>
<script defer src="/scoutinggoals/js/calendar/js/semantic.ui.min.js"></script>
<script defer src="/scoutinggoals/js/calendar/js/prism.min.js"></script>
<script defer src="/scoutinggoals/js/calendar/js/pignose.calendar.full.min.js"></script>
<script type="text/javascript">
//<![CDATA[
$(function() {
    function onClickHandler(date, obj) {
        /**
         * @date is an array which be included dates(clicked date at first index)
         * @obj is an object which stored calendar interal data.
         * @obj.calendar is an element reference.
         * @obj.storage.activeDates is all toggled data, If you use toggle type calendar.
         * @obj.storage.events is all events associated to this date
         */

        var $calendar = obj.calendar;
        var $box = $calendar.parent().siblings('.box').show();
        var text = 'You choose date ';

        if(date[0] !== null) {
            text += date[0].format('YYYY-MM-DD');
        }

        if(date[0] !== null && date[1] !== null) {
            text += ' ~ ';
        } else if(date[0] === null && date[1] == null) {
            text += 'nothing';
        }

        if(date[1] !== null) {
            text += date[1].format('YYYY-MM-DD');
        }

        $box.text(text);
    }

    // Default Calendar
    $('.scout-calendar').pignoseCalendar({
        select: onClickHandler
    });
});
//]]>
</script>
<div id="schedules" class="article">
  <h3><span>Scout Calendar</span></h3>
  <div class="scout-calendar"></div>
  <div class="box"></div>
</div>
HTML;

?>
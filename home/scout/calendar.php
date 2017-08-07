<?php
/**
 * $user loaded from master/index.php
 */
if(!$userObj->is_logged_in())
{
    $userObj->redirect('/scoutinggoals');
}
if($userObj->is_logged_in()!="") {
    $echedule_events = implode(",", $userObj->get_events($user['masters_id']));
    $CALENDAR_HTML = <<< HTML
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
    
            var calendar = obj.calendar;
            var box = calendar.parent().siblings('.box').show();
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
    
            box.text(text);
        }
    
        // Default Calendar
        $('.scout-calendar').pignoseCalendar({
            scheduleOptions: {
              colors: {
                camping: '#2fabb7',
                conference: '#5c6270',
                cooking: '#e29726',
                meetup: '#ef8080',
                event: '#bab9b8'
              }
            },
            schedules: [{$echedule_events}],
            select: function (date, context) {
              var date_formatted = date[0] === null ? 'null' : date[0].format('YYYY-MM-DD');
              var message = `You selected ` + date_formatted +
                                   `<br />
                                   <br />
                                   <strong>Events for this date</strong>
                                   <br />
                                   <div class="schedules-date"></div>`;
              var target = context.calendar.parent().next().show().html(message);
              for (var idx in context.storage.schedules) {
                var schedule = context.storage.schedules[idx];
                if (typeof schedule !== 'object') {
                  continue;
                }
                background_color = "inherit"
                if (typeof context.context.settings.scheduleOptions.colors[schedule.name] !== 'undefined'){
                    background_color = context.context.settings.scheduleOptions.colors[schedule.name];
                }
                target.find('.schedules-date').append('<div style="width:100%;"><span style="background-color:' + background_color + '" class="ui label default">' + schedule.name + '</span><span>' + schedule.description + '</span></div>');
              }
            }
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
}
?>

// Fireup the plugins
$(document).ready(function(){
	
	// initialise  slideshow
	 $('.flexslider').flexslider({
        animation: "slide",
        slideshow: false,
        controlNav: false,
        directionNav: false,
        start: function(slider){
          $('body').removeClass('loading');
        }
      });

});
/**
 * Handles toggling the navigation menu for small screens.
 */
( function() {
	var button = document.getElementById( 'topnav' ).getElementsByTagName( 'div' )[0],
	    menu   = document.getElementById( 'topnav' ).getElementsByTagName( 'ul' )[0];

	if ( undefined === button )
		return false;

	// Hide button if menu is missing or empty.
	if ( undefined === menu || ! menu.childNodes.length ) {
		button.style.display = 'none';
		return false;
	}

	button.onclick = function() {
		if ( -1 === menu.className.indexOf( 'srt-menu' ) )
			menu.className = 'srt-menu';

		if ( -1 !== button.className.indexOf( 'toggled-on' ) ) {
			button.className = button.className.replace( ' toggled-on', '' );
			menu.className = menu.className.replace( ' toggled-on', '' );
		} else {
			button.className += ' toggled-on';
			menu.className += ' toggled-on';
		}
	};
} )();

// Load page


(function($) {
	$(function() {

		var	$window = $(window),
			$body = $('body'),
			$main = $('#main'),
			$main_articles = $main.children('article');

		// Main.
        var	delay = 325;

        // Methods.
        $main._show = function(id, initial, default_id) {
            default_id = default_id || false
            if (default_id === true) {
                var $article = $main_articles.filter('.default');
            }
            else {
                var $article = $main_articles.filter('#' + id);
            }

            // No such article? Bail.
            if ($article.length === 0)
                var $article = $main_articles.filter('.default');

            // Handle lock.

            // Already locked? Speed through "show" steps w/o delays.
            if (typeof initial !== 'undefined' && initial === true) {

                // Mark as switching.
                    $body.addClass('is-switching');

                // Mark as visible.
                    $body.addClass('is-article-visible');

                // Deactivate all articles (just in case one's already active).
                    $main_articles.removeClass('active');

                // Show main, article.
                    $main.show();
                    $article.show();

                // Activate article.
                    $article.addClass('active');

                // Unmark as switching.
                    setTimeout(function() {
                        $body.removeClass('is-switching');
                    }, (initial ? 1000 : 0));

                return;

            }

            // Article already visible? Just swap articles.
            if ($body.hasClass('is-article-visible')) {

                // Deactivate current article.
                    var $currentArticle = $main_articles.filter('.active');

                    $currentArticle.removeClass('active');

                // Show article.
                    setTimeout(function() {

                        // Hide current article.
                            $currentArticle.hide();

                        // Show article.
                            $article.show();

                        // Activate article.
                            setTimeout(function() {

                                $article.addClass('active');

                                // Window stuff.
                                    $window
                                        .scrollTop(0);

                            }, 25);

                    }, delay);

            }

            // Otherwise, handle as normal.
            else {
                // Mark as visible.
                    $body.addClass('is-article-visible');
                    var $currentArticle = $main_articles.filter('.active');
                    $currentArticle.removeClass('active');

                // Show article.
                    setTimeout(function() {

                        // Hide current article.
                            $currentArticle.hide();
                        // Show main, article.
                            $main.show();
                            $article.show();

                        // Activate article.
                            setTimeout(function() {

                                $article.addClass('active');

                                // Window stuff.
                                    $window
                                        .scrollTop(0);

                            }, 25);

                    }, delay);

            }

        };

        $main._hide = function(addState) {

            var $article = $main_articles.filter('.active');

            // Article not visible? Bail.
                if (!$body.hasClass('is-article-visible'))
                    return;

            // Add state?
                if (typeof addState !== 'undefined'
                &&	addState === true)
                    history.pushState(null, null, '#');

            // Handle lock.


            // Deactivate article.
                $article.removeClass('active');

            // Hide article.
                setTimeout(function() {

                    // Hide article, main.
                        $article.hide();


                    // Unmark as visible.
                        setTimeout(function() {

                            $body.removeClass('is-article-visible');

                            // Window stuff.
                                $window
                                    .scrollTop(0);

                        }, 25);

                }, delay);


        };

    // Articles.
        $main_articles.each(function() {

            var $this = $(this);

            // Prevent clicks from inside article from bubbling.
                $this.on('click', function(event) {
                    event.stopPropagation();
                });

        });

        $("#role_state").change(function() {
            if ($(this).val() === "scout_member") {
                $("#mastersId").show();
            }else{
                $("#mastersId").hide();
            }
        });

        $("#form-rank-review input:checkbox").on('click', function() {
            // in the handler, 'this' refers to the box clicked on
            var $box = $(this);
            if ($box.is(":checked")) {
                if ($box.attr('id') === "requestTextArea"){
                    $("#reviewTextArea").show();
                }
                else {
                    $("#reviewTextArea").hide();
                }
                // the name of the box is retrieved using the .attr() method
                // as it is assumed and expected to be immutable
                var group = "input:checkbox[name='" + $box.attr("name") + "']";
                // the checked state of the group/box on the other hand will change
                // and the current value is retrieved using .prop() method
                $(group).prop("checked", false);
                $box.prop("checked", true);
            } else {
                $box.prop("checked", false);
                $("#reviewTextArea").hide();
            }
        });
        $window.on('hashchange', function(event) {

            // Empty hash?
                if (location.hash === ''
                ||	location.hash === '#') {

                    // Prevent default.
                        event.preventDefault();
                        event.stopPropagation();

                    // Hide.
                        $main._hide();

                }

            // Otherwise, check for a matching article.
                else if ($main_articles.filter(location.hash).length > 0) {

                    // Prevent default.
                        event.preventDefault();
                        event.stopPropagation();

                    // Show article.
                        $main._show(location.hash.substr(1));

                }

        });

        // Scroll restoration.
        // This prevents the page from scrolling back to the top on a hashchange.
        if ('scrollRestoration' in history)
            history.scrollRestoration = 'manual';
        else {

            var	oldScrollPos = 0,
                scrollPos = 0,
                $htmlbody = $('html,body');

            $window
                .on('scroll', function() {

                    oldScrollPos = scrollPos;
                    scrollPos = $htmlbody.scrollTop();

                })
                .on('hashchange', function() {
                    $window.scrollTop(oldScrollPos);
                });

        }

        // Initialize.

        // Hide main, articles.
        $main.hide();
        $main_articles.hide();

        // Initial article.
        if (location.hash !== ''
        &&	location.hash !== '#')
            $window.on('load', function() {
                $main._show(location.hash.substr(1), true);
            });
        else {
            $window.on('load', function() {
                $main._show("", true, default_id=true);
            });
        }

	});

})(jQuery);


// Get the Journal modal
var modal = document.getElementById('journalModal');
if (modal) {
    var span = document.getElementsByClassName("journal-modal-close")[0];
    var last_check_box = null;
// When the user clicks the button, open the modal
    function openJournalModal(cb, rankID, rankName, dueDate) {
        if(cb.checked === true){
            var rank_task_element = document.getElementById('rank_alias_id');
            var rank_due_element = document.getElementById('rank_due_date');
            var modal_title_element = document.getElementById('modal_title');
            modal.style.display = "block";
            rank_task_element.value = rankID;
            modal_title_element.innerHTML = "Journal Entry for " + rankName + " " + rankID;
            rank_due_element.value = dueDate;
            last_check_box = cb;
        }
        else{
            modal.style.display = "none";
        }
    }


// When the user clicks on <span> (x), close the journal modal
    span.onclick = function() {
        modal.style.display = "none";
        if( last_check_box ) {
            last_check_box.checked = false;
        }
    };

}

// Get the Journal modal
var reviewModal = document.getElementById('reviewModal');
if (reviewModal) {
    var reviewSpan = document.getElementsByClassName("review-modal-close")[0];
    var last_check_box = null;
// When the user clicks the button, open the modal
    function openReviewModal(cb, rankID, userName, userRankTaskID, userJournal) {
        if(cb.checked === true){
            var user_rank_task_id = document.getElementById('user_rank_task_id');
            var journal_entry = document.getElementById('journal_entry');
            var reviewTxtArea = document.getElementById('reviewTextArea');
            var modal_title_element = document.getElementById('modal_title');
            user_rank_task_id.value = userRankTaskID;
            journal_entry.innerHTML = userJournal;
            reviewTxtArea.placeholder = "Write a comment back to Scout " + userName;
            modal_title_element.innerHTML = "Review for " + userName + " on " + rankID;
            reviewModal.style.display = "block";
            last_check_box = cb;
        }
        else{
            reviewModal.style.display = "none";
        }
    }


// When the user clicks on <span> (x), close the journal modal
    reviewSpan.onclick = function() {
        reviewModal.style.display = "none";
        if( last_check_box ) {
            last_check_box.checked = false;
        }
    };

}

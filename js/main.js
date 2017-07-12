
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
		if ( -1 == menu.className.indexOf( 'srt-menu' ) )
			menu.className = 'srt-menu';

		if ( -1 != button.className.indexOf( 'toggled-on' ) ) {
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
				$main._show = function(id, initial) {

					var $article = $main_articles.filter('#' + id);
					// No such article? Bail.
						if ($article.length == 0)
							return;

					// Handle lock.

						// Already locked? Speed through "show" steps w/o delays.
							if (typeof initial != 'undefined' && initial === true) {

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
						if (typeof addState != 'undefined'
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

				$window.on('hashchange', function(event) {

					// Empty hash?
						if (location.hash == ''
						||	location.hash == '#') {

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
					if (location.hash != ''
					&&	location.hash != '#')
						$window.on('load', function() {
							$main._show(location.hash.substr(1), true);
						});
					else {
					    $window.on('load', function() {
					        $main._show("login", true);
					    });
					}

	});

})(jQuery);
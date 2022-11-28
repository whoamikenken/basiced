$('.mm-prev-btn').hide();

	var x;
	var count;
	var current;
	var percent;
	var z = [];

	init();
	getCurrentSlide();
	goToNext();
	goToPrev();
	getCount();
	buildStatus();
	deliverStatus();
	submitData();
	goBack();

	function init() {
		
		$('.mm-survey-container .mm-survey-page').each(function() {

			var item;
			var page;

			item = $(this);
			page = item.data('page');

			item.addClass('mm-page-'+page);
		});

	}

	function getCount() {

		count = $('.mm-survey-page').length;
		return count;

	}

	function goToNext() {

		$('.mm-next-btn').on('click', function() {
			goToSlide(x);
			getCount();
			current = x + 1;
			var g = current/count;
			buildProgress(g);
			var y = (count + 1);
			getButtons();
			$('.mm-survey-page').removeClass('active');
			$('.mm-page-'+current).addClass('active');
			getCurrentSlide();
			checkStatus();
			if( $('.mm-page-'+count).hasClass('active') ){
				if( $('.mm-page-'+count).hasClass('pass') ) {
					$('.mm-finish-btn').addClass('active');
				}
				else {
					$('.mm-page-'+count+' .mm-survery-content .mm-survey-item').on('click', function() {
						$('.mm-finish-btn').addClass('active');
					});
				}
			}
			else {
				$('.mm-finish-btn').removeClass('active');
				if( $('.mm-page-'+current).hasClass('pass') ) {
					$('.mm-survey-container').addClass('good');
					$('.mm-survey').addClass('okay');
				}
				else {
					$('.mm-survey-container').removeClass('good');
					$('.mm-survey').removeClass('okay');
				}
			}
			buttonConfig();
		});

	}

	function goToPrev() {

		$('.mm-prev-btn').on('click', function() {
			goToSlide(x);
			getCount();			
			current = (x - 1);
			var g = current/count;
			buildProgress(g);
			var y = count;
			getButtons();
			$('.mm-survey-page').removeClass('active');
			$('.mm-page-'+current).addClass('active');
			getCurrentSlide();
			checkStatus();
			$('.mm-finish-btn').removeClass('active');
			if( $('.mm-page-'+current).hasClass('pass') ) {
				$('.mm-survey-container').addClass('good');
				$('.mm-survey').addClass('okay');
			}
			else {
				$('.mm-survey-container').removeClass('good');
				$('.mm-survey').removeClass('okay');
			}
			buttonConfig();
		});

	}

	function buildProgress(g) {

		if(g > 1){
			g = g - 1;
		}
		else if (g === 0) {
			g = 1;
		}
		g = g * 100;
		$('.mm-survey-progress-bar').css({ 'width' : g+'%' });

	}

	function goToSlide(x) {

		return x;

	}

	function getCurrentSlide() {

		$('.mm-survey-page').each(function() {

			var item;

			item = $(this);

			if( $(item).hasClass('active') ) {
				x = item.data('page');
			}
			else {
				
			}

			return x;

		});

	}

	function getButtons() {

		if(current === 0) {
			current = y;
		}
		if(current === count) {
			$('.mm-next-btn').hide();
		}
		else if(current === 1) {
			$('.mm-prev-btn').hide();
		}
		else {
			$('.mm-next-btn').show();
			$('.mm-prev-btn').show();
		}

	}

	$('.mm-survey-q li input').each(function() {

		var item;
		item = $(this);

		$(item).on('click', function() {
			if( $('input:checked').length > 0 ) {
		    	// console.log(item.val());
		    	$('label').parent().removeClass('active');
		    	item.closest( 'li' ).addClass('active');
			}
			else {
				//
			}
		});

	});

	percent = (x/count) * 100;
	$('.mm-survey-progress-bar').css({ 'width' : percent+'%' });

	function checkStatus() {
		$('.mm-survery-content .mm-survey-item').on('click', function() {
			var item;
			item = $(this);
			item.closest('.mm-survey-page').addClass('pass');
		});
	}

	function buildStatus() {
		$('.mm-survery-content .mm-survey-item').on('click', function() {
			var item;
			item = $(this);
			item.addClass('bingo');
			item.closest('.mm-survey-page').addClass('pass');
			$('.mm-survey-container').addClass('good');
		});
	}

	function deliverStatus() {
		$('.mm-survey-item').on('click', function() {
			if( $('.mm-survey-container').hasClass('good') ){
				$('.mm-survey').addClass('okay');
			}
			else {
				$('.mm-survey').removeClass('okay');	
			}
			buttonConfig();
		});
	}

	function lastPage() {
		if( $('.mm-next-btn').hasClass('cool') ) {
			alert('cool');
		}
	}

	function buttonConfig() {
		if( $('.mm-survey').hasClass('okay') ) {
			$('.mm-next-btn button').prop('disabled', false);
		}
		else {
			$('.mm-next-btn button').prop('disabled', true);
		}
	}

	function submitData() {
		$('.mm-finish-btn').on('click', function() {
			collectData();
			$('.mm-survey-bottom').slideUp();
			$('.mm-survey-results').slideDown();
		});
	}

	function collectData() {
		
		var map = {};
		var ax = ['0','red','mercedes','3.14','3'];
		var answer = '';
		var total = 0;
		var ttl = 0;
		var g;
		var c = 0;

		$('.mm-survey-item input:checked').each(function(index, val) {
			var item;
			var data;
			var name;
			var n;

			item = $(this);
			data = item.val();
			name = item.data('item');
			n = parseInt(data);
			total += n;

			map[name] = data;

		});

		$('.mm-survey-results-container .mm-survey-results-list').html('');

		for (i = 1; i <= count; i++) {

			var t = {};
			var m = {};
			answer += map[i] + '<br>';
			
			if( map[i] === ax[i]) {
				g = map[i];
				p = 'correct';
				c = 1;
			}
			else {
				g = map[i];
				p = 'incorrect';
				c = 0;
			}

			$('.mm-survey-results-list').append('<li class="mm-survey-results-item '+p+'"><span class="mm-item-number">'+i+'</span><span class="mm-item-info">'+g+' - '+p+'</span></li>');

			m[i] = c;
			ttl += m[i];

		}

		var results;
		results = ( ( ttl / count ) * 100 ).toFixed(0);

		$('.mm-survey-results-score').html( results + '%' );

	}

	function goBack() {
		$('.mm-back-btn').on('click', function() {
			$('.mm-survey-bottom').slideDown();
			$('.mm-survey-results').slideUp();
		});
	}
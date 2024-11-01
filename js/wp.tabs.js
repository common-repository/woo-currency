(function($) {
	$(function() {

		function setActiveTab() {
			$('.wcuCurrenciesShell .nav-tab-wrapper').each(function(index, el) {

				var $links = $(this).find('[data-target]')
					,	$activeLink = $($links.filter('[data-target="' + location.hash.replace('#', '') + '"]')[0] || $links[0]);

				$links.on('click', function(event, isInit){
					event.preventDefault();
					var $this = $(this)
						,	target = $this.data('target')
						,	connectedJqTbl = $this.data('connect-tbl')
						,	isActive = $this.hasClass('nav-tab-active');

					$links.removeClass('nav-tab-active');
					$this.addClass('nav-tab-active');

					$('[data-tab]').removeClass('wcuTabContentActive');
					$('[data-tab='+ target+ ']').addClass('wcuTabContentActive');

					window.location.hash = '#'+ target;

					if(connectedJqTbl && !isInit) {
						var tbl = Membership.getJqTbl( connectedJqTbl );
						if(tbl) {
							tbl.updateWidth();
							if(isActive && tbl.get('connectForm') && tbl.get('connectForm').isVisible()) {
								tbl.get('connectForm').hideForm();
							}
						}
					}
				});

				$activeLink.trigger('click', true);
			});

			$("div.wcuTabContent").each(function(index) {
				 $(this).find(".wcuTabContentChild").removeClass('wcuTabContentChildActive');
				 $(this).find(".wcuTabContentChild:eq(0)").addClass('wcuTabContentChildActive');
				 $(this).find(".nav-tab-wrapper-child .nav-tab-child").removeClass('nav-tab-child-active');
				 $(this).find(".nav-tab-wrapper-child .nav-tab-child:eq(0)").addClass('nav-tab-child-active');
			});

			$(".nav-tab-child").click(function(){
				$(".nav-tab-child").removeClass("nav-tab-child-active");
				$(this).addClass("nav-tab-child-active");
				var target = $(this).attr("data-target-child");
				var parent = $(this).parent(".wcuTabContent");
				$(this).parents(".wcuTabContent").find(".wcuTabContentChild").removeClass("wcuTabContentChildActive");
				$(this).parents(".wcuTabContent").find(".wcuTabContentChild[data-tab-child='"+target+"']").addClass("wcuTabContentChildActive");
			});

		}


		$('.wcuCurrenciesShell').each(function() {
			var $activeLink,
				$tabContent,
				$links = $(this).find('a');

			$activeLink = $($links.filter('[href="' + location.hash.replace('/', '') + '"]')[0] || $links[0]);

			if (! $activeLink.length) {
				return;
			}

			$tabContent = $($activeLink[0].hash);

			$links.not($activeLink).each(function () {
				if(this.hash.indexOf(';') == -1 && this.hash.indexOf('?') == -1) {
					$(this.hash).hide();
				}
			});

			$(this).on('click', 'a', function(event) {

				event.preventDefault();

				if (!this.hash || this.hash === '#') {
					return false;
				}


				window.location.hash = '#/' + this.hash.replace('#', '');

				$activeLink.removeClass('active');
				$tabContent.hide();

				$activeLink = $(this);
				$activeLink.trigger('before.show.tab');
				if(this.hash.indexOf(';') != -1) return;
				$tabContent = $(this.hash);

				$activeLink.addClass('active');
				$tabContent.fadeIn();
				$activeLink.trigger('after.show.tab');

			});


			$activeLink.trigger('click');
		});

		setActiveTab();

		$(window).on('hashchange', function() {
			setActiveTab();
		});

	});

})(jQuery);

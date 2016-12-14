(function ($) {
    $.winReload = function(){
        //window.location.href = document.URL;
        location.reload();
    };
	$.ZnAboutJs = function () {
		this.scope = $(document);
		this.zinit();
		this.zn_dummy_step = 0;
		this.failed = 0;
	};

	$.ZnAboutJs.prototype = {
		zinit : function() {
			var fw = this;

			fw.init_tabs();
			// Init theme registration form
			fw.init_theme_registration();
			// Init misc
			fw.init_misc();
			// Init tooltips
			fw.init_tooltips();
			// Init plugin ajax actions
			fw.init_plugin_ajax();

		},

		init_tooltips : function(){
			$( '.zn-server-status-column-icon' ).tooltip({
				position : { my: 'center bottom', at: 'center top-10' }
			});
		},

		init_tabs : function(){

			var nav_li = $('.zn-about-navigation > li'),
				nav_links = $('.zn-about-navigation > li > a'),
				actions_area = $('#zn-about-actions');

				// Check if first or last to show next/prev or both
				var doNextprev = function(index){
					if( index == 0 ){
						actions_area.addClass('is-first').removeClass('is-last');
					}
					else if( index == (nav_li.length - 1 ) ){
						actions_area.addClass('is-last').removeClass('is-first');
					}
					else {
						actions_area.removeClass('is-first is-last');
					}
				}

			nav_li.on('click', function(e){

				var curlink = $('a', e.currentTarget).attr('href');
				$('.zn-about-header').attr('id', curlink + "-dashboard");
				// window.location.hash = '#dashboard-top';
				e.preventDefault();

				// Activate the menu
				$(e.currentTarget).addClass('active');
				$(e.currentTarget).siblings('li').removeClass('active');

				// Activate the current tab
				var tabs = $(this).closest('.zn-about-tabs-wrapper').find('.zn-about-tabs > .zn-about-tab'),
					current_tab = $( curlink );
				window.location.hash = curlink + "-dashboard";

				tabs.removeClass('active');
				current_tab.addClass('active');

			});

			// Activate
			var hash = window.location.hash;
			if (hash !== '') {
				var nodashboard = hash.replace('-dashboard', '');
				nav_li.find('a[href="' + nodashboard + '"]').parent().trigger('click');
			}

			// Init next and prev buttons
			$( '.zn-about-action-nav' ).click(function(){
				var tabs = $('.zn-about-tabs-wrapper').find('.zn-about-tabs > .zn-about-tab'),
					current_tab = tabs.filter('.active'),
					to = $(this).attr('data-to');

				// Change menu
				$('.zn-about-navigation > li').removeClass('active');
				$('.zn-about-navigation > li a[href="#'+current_tab.attr('id')+'"]').parent()[to]().addClass('active')
				// theparent;

				// Change tab
				tabs.removeClass('active');
				current_tab[to]().addClass('active');

				doNextprev( nav_li.filter('.active').index() );

			});
		},

		init_theme_registration : function(){
			$('.zn-about-register-form').submit(function(e){
				e.preventDefault();

				var //username = $('.zn-about-register-form-username', this).val(),
					api_key  = $('.zn-about-register-form-api', this).val(),
					nonce = $('#zn_nonce', this).val(),
					form = $(this),
					button = form.find( '.zn-about-register-form-submit' ),
					is_submit = false;

				if( form.hasClass('zn-submitting') ){
					return;
				}

				// Don't do anything if we don't have the values filled in
				if( /*! username.length || */ ! api_key.length || ! nonce.length ){
					$(this).addClass('zn-about-register-form--error');
					return;
				}

				var data = {
					'action': 'zn_theme_registration',
					//'username': username,
					'dash_api_key': api_key,
					'zn_nonce': nonce
				};

				$(this).addClass('zn-submitting');

                // hide the label on click
                $('.js-zn-label-tfusername', form).hide();

				// Perform the Ajax call
				jQuery.post(ajaxurl, data, function(response) {
					var alertContainer = $('#zn-register-theme-alert');
					// If we received an error, display it
					if( response.success === false ){
						if( response.data.error ){
							alertContainer.html('<div class="zn-adminNotice zn-adminNotice-error">ERROR: '+response.data.error+'</div>').show();
						}
					}
					else if( response.success === true ){
						alertContainer.html('<div class="zn-adminNotice zn-adminNotice-success">'+response.data.message+'</div>').show();
						location.reload();
					}
					else{
						alertContainer.html('<div class="zn-adminNotice zn-adminNotice-error">Something went wrong. Please try again later.</div>').show();
					}
					form.removeClass('zn-submitting');
				});

			});
		},

		init_misc : function(){
			var refreshDemosButton = $('.js-refresh-demos');
            if(typeof(refreshDemosButton) != 'undefined')
            {
                refreshDemosButton.on('click', function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    var self = $(this);
                    $.ajax({
                        'type' : 'post',
                        'dataType' : 'json',
                        'url' : ajaxurl,
                        'data' : {
                            'action': 'zn_refresh_theme_demos',
                            'zn_nonce': self.data('nonce')
                        }
                    }).done(function(response){
                        if(response && response.data)
                        {
                            if( response.data.error){
                                console.info(response.data.error);
                            }
                            else if( '1' == response.data.toLowerCase() ){
                                //console.info(window);
                                $.winReload();
                            }
                            else {
                                console.info(response.data);
                            }
                        }
                    }).fail(function(e){
                        console.error('fail: '+e);
                    });
                });
            }
		},

		init_plugin_ajax : function(){
			var fw = this;

			$( document ).on( 'click', '.zn-extension-button', function(e){
				e.preventDefault();

				// Perform the ajax call based on action
				var config = {};
					config.button			= $( this );
					// config.button			= config.button.find('.spinner');
					config.status_classes	= 'zn-active zn-inactive zn-not-installed';
					config.elm_container	= config.button.closest('.zn-extension');
					config.status_holder	= config.elm_container.find( '.zn-extension-status' );
					// config.status_text		= config.button.closest( '.zn-extension-status' );
					config.action			= config.button.data( 'action' );
					config.nonce			= config.button.data( 'nonce' );
					config.slug				= config.button.data( 'slug' );

				if( config.elm_container.hasClass('zn-addons-disabled') ){
					return false;
				}

				var data = {
					security 		: config.nonce,
					action 			: 'zn_do_plugin_action',
					plugin_action 	: config.button.data( 'action' ) 		|| false,
					slug 			: config.button.data( 'slug' ) 			|| false,
				};

				// Don't allow the user to spam the button
				if( config.button.hasClass('is-active') ) { return false; }

				// Add the loading class
				config.button.addClass( 'is-active' );

				fw.perform_ajax_call( data, config );

				return false;
			});
		},

		perform_ajax_call : function( data, config, callback ){
			// Perform the ajax call
			$.ajax({
				'type' : 'post',
				'dataType' : 'json',
				'url' : ajaxurl,
				'data' : data,
				'success' : function( response ){

					// If we received an error, display it
					if( response.data.error ){
						new $.ZnModalMessage( "ERROR: " + response.data.error );
					}

					// Update the plugin status
					config.elm_container.removeClass( config.status_classes );
					config.elm_container.addClass( response.data.status );
					config.status_holder.text( response.data.status_text );

					// Update the plugin
					config.button.data( 'action', response.data.action );
					config.button.text( response.data.action_text );

					if( typeof callback != 'undefined' ){
						callback();
					}

					config.button.removeClass( 'is-active' );
				},
				'error' : function(response){
					if( typeof callback != 'undefined' ){
						callback();
					}
					new $.ZnModalMessage( 'There was a problem performing the action.' );
					config.button.removeClass( 'is-active' );
				}
			});
		}
	};

	$(document).ready(function() {
		// Call this on document ready
		$.ZnAboutJs = new $.ZnAboutJs();
	});

})(jQuery);

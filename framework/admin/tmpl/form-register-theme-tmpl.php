<?php if(! defined('ABSPATH')){ return; } ?>
<div class="zn-registerContainer">

	<div class="znfb-row">

		<div class="znfb-col-12">
			<h3 class="zn-lead-title"><strong>Register Kallyas</strong></h3>
		</div>

		<div class="znfb-col-8">

			<?php if( ! ZN_HogashDashboard::isConnected() ){ ?>
				<div class="zn-lead-text">
					<div class="zn-adminNotice zn-adminNotice-info">
						<p><?php _e('<strong>To enjoy the full experience we strongly recommend to register Kallyas.</strong>', 'zn_framework');?></p>
						<p><?php _e('By connecting your theme with <a href="http://my.hogash.com/" target="_blank">Hogash Dashboard</a>, you will get Kallyas theme updates, sample data demo packs and notifications about cool new features.', 'zn_framework');?></p>
					</div>
					<h3><strong><?php _e('Please follow these steps:', 'zn_framework');?></strong></h3>
					<ul class="zn-dashRegister-steps">
						<li><?php _e('1) <a href="http://my.hogash.com/" target="_blank">Register to Hogash Customer Dashboard</a> with your Envato Account.', 'zn_framework');?></li>
						<li><?php _e('2) Access "<a href="http://my.hogash.com/register-products/" target="_blank">My Products</a>" section of the dashboard and make sure you have at least one purchase of Kallyas theme.', 'zn_framework');?></li>
						<li><?php _e('3) Click on the Generate Key button and than copy the Key.', 'zn_framework');?></li>
						<li><?php _e('4) Insert/paste the generated API Key you just copied, into the right side "HOGASH API KEY" form. Click the <strong>Connect</strong> button.', 'zn_framework');?></li>
					</ul>
				</div>
			<?php
			}

			else {
			?>
			<div class="zn-lead-text">
				<div class="zn-adminNotice zn-adminNotice-info">
					<p><?php _e('<strong>You have successfully activated your copy of Kallyas theme.</strong> ', 'zn_framework');?></p>
					<p><?php _e('If you plan on migrating / changing the domain of this website, please regenerate the API key into the <a href="http://my.hogash.com/" target="_blank">Hogash Dashboard</a>.', 'zn_framework');?></p>
				</div>
			</div>
			<?php } ?>

		</div>

		<div class="znfb-col-4">
			<?php
			if(isset($m) && ! empty($m))
			{
				$cssClass = ($m['success'] ? 'success' : 'error');
				?>
				<div class="zn-adminNotice zn-adminNotice-<?php echo $cssClass;?>"><p><?php echo $m['data'];?></p></div>
				<?php
			}
			?>
			<?php
			$dash_api_key = ZN_HogashDashboard::getApiKey();
			?>
			<form action="" class="zn-about-register-form zn-dashRegister-form" method="post">

				<div class="zn-dashRegister-status">
					Status:
					<?php
					if( ! ZN_HogashDashboard::isConnected() ){
						echo __('<strong class="zn-dashRegister-statusName">NOT CONNECTED</strong>', 'zn_framework');
					}
					else {
						echo __('<strong class="zn-dashRegister-statusName is-connected">CONNECTED</strong>', 'zn_framework');
					}
					?>
				</div>

				<div id="zn-register-theme-alert">
					<?php
					if(! ZN_HogashDashboard::isConnected() && ! empty($dash_api_key)){
						?>
						<div class="zn-adminNotice zn-adminNotice-error js-zn-label-tfusername">
							<?php _e('This api key is not valid.', 'zn_framework');?>
						</div>
					<?php } ?>
				</div>
				<div class="zn-about-form-field zn-dashRegister-formMain">
					<label for="hg_api_key"><?php _e('Hogash API key', 'zn_framework');?></label>

					<input type="text" id="hg_api_key" name="dash_api_key" class="zn-about-register-form-api" value="<?php echo $dash_api_key;?>" placeholder="<?php _e('XXXXX-XXXXX-XXXXX-XXXXX-XXXXX', 'zn_framework');?>">
				</div>

				<?php wp_nonce_field( 'zn_theme_registration', 'zn_nonce' ); ?>
				<input type="submit" class="zn-about-register-form-submit zn-dashRegister-formSubmit zn-about-action zn-action-green zn-action-md" value="<?php _e('Connect', 'zn_framework');?>">
			</form>
		</div>

		<div class="znfb-col-12">
			<hr class="zn-dashRegister-sep">
		</div>

		<!-- <div class="znfb-col-3">
			<a href="http://support.hogash.com/documentation/how-to-register-kallyas-theme/" target="_blank"><img src="<?php echo THEME_BASE_URI ?>/framework/admin/assets/images/register-video-thumb.jpg" alt="" class="zn-dashRegister-videoThumb"></a>
		</div> -->

		<div class="znfb-col-12">
			<div class="zn-dashRegister-infoList">
				<h4>Having problems? <a href="http://support.hogash.com/documentation/how-to-register-kallyas-theme/" target="_blank">Read the tutorial</a> </h4>
				<h3>Common issues</h3>
				<ul>
					<li><a href="http://support.hogash.com/documentation/how-to-register-kallyas-theme/#registration_benefits" target="_blank">What are the benefits of registration?</a></li>
					<li><a href="http://support.hogash.com/documentation/how-to-register-kallyas-theme/#what_is_needed" target="_blank">Why do I need to register my theme?</a></li>
					<li><a href="http://support.hogash.com/documentation/how-to-register-kallyas-theme/#how_to_verify_api_key" target="_blank">How can I verify my API Key?</a></li>
					<li><a href="http://support.hogash.com/documentation/how-to-register-kallyas-theme/#why_not_active" target="_blank">Why my API key is inactive?</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>

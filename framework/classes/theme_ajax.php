<?php if(! defined('ABSPATH')){ return; }

// Add ajax functionality
add_action( 'wp_ajax_zn_ajax_callback', 'zn_ajax_callback' );
add_action( 'wp_ajax_zn_theme_registration', 'theme_registration_hook' );
add_action( 'wp_ajax_zn_refresh_theme_demos', 'refresh_theme_demos' );



/**
 * Will check and save the user credentials needed for automatic theme updates. This works ony for single sites
 * @return string A json formatted value
 */
function theme_registration_hook()
{
	if ( ! isset( $_POST['zn_nonce'] ) || ! wp_verify_nonce( $_POST['zn_nonce'], 'zn_theme_registration' ) ) {
		wp_send_json_error( array( 'error' => 'Sorry, your nonce did not verify.' ) );
	}

    $apiKey = isset( $_POST['dash_api_key'] ) ? esc_attr($_POST['dash_api_key']) : '';

	if( ! empty( $apiKey ) )
	{
        $response = ZN_HogashDashboard::connectTheme( $apiKey );

        if(isset($response['error'])){
            wp_send_json_error( array('error' => $response['error']));
        }

        if( isset($response['success']) && $response['success']){
            ZN_HogashDashboard::updateApiKey($apiKey);
            wp_send_json_success(array( 'message' => $response['data'] ));
        }
        else {
            wp_send_json_error( array( 'error' => $response['data'] ) );
        }
	}
	wp_send_json_error( array( 'error' => __('An error occurred. Please try again in a few moments.', 'zn_framework') ) );
}

function zn_ajax_callback() {

	check_ajax_referer( 'zn_framework', 'zn_ajax_nonce' );

	$save_action = $_POST['zn_action'];

	if ( $save_action == 'zn_save_options' ) {

		// DO ACTION FOR SAVED OPTIONS
		do_action( 'zn_save_theme_options' );

		$data = json_decode( stripslashes($_POST['data']), true );

		/* REMOVE THE HIDDEN FORM DATA */
		unset($data['action']);
		unset($data['zn_action']);
		unset($data['zn_ajax_nonce']);

		$options_field = $data['zn_option_field'];

		// Combine all options
		// Get all saved options
		$saved_options = zget_option( '' , '' , true );
		$saved_options[$options_field] = $data;

		$result = znklfw_save_theme_options( $saved_options );

		if ( $result == 0 || $result ) {
				echo 'Settings successfully save';
			die();
		}
		else {
				echo 'There was a problem while saving the options';
			die();
		}

	}
	elseif ( $save_action == 'zn_add_element' ) {

		$data = $_POST;

		if ( empty( $data['zn_elem_type'] ) ) {
			return;
		}

		$value = json_decode ( base64_decode( $data['zn_json'] ), true );
		$value['dynamic'] = true;

		echo ZN()->html()->zn_render_single_option( $value );

		die();
	}
	elseif ( $save_action == 'zn_add_google_font' ) {

		$data = $_POST;

		if ( empty( $data['zn_elem_type'] ) ) {
			return;
		}

		$value = json_decode ( base64_decode( $data['zn_json'] ), true );
		if( isset( $data['selected_font'] ) ) {
			$value['selected_font'] = $data['selected_font'];
		}
		$value['dynamic'] = true;

		echo ZN()->html()->zn_render_single_option( $value );

		die();
	}
	elseif( $save_action == 'zn_process_theme_updater' ){
		ZN()->installer->update( $_POST['step'], $_POST['data'] );
		die();
	}
	elseif( $save_action == 'zn_refresh_pb' ){
		ZN()->pagebuilder->refresh_pb_data();
		znklfw_regenerate_dynamic_css();
		die();
	}
	else {
		die('Are you cheating ?');
	}
}

function refresh_theme_demos()
{
	if ( ! isset( $_POST['zn_nonce'] ) || ! wp_verify_nonce( $_POST['zn_nonce'], 'refresh_demos_list' ) ) {
		wp_send_json_error( array( 'error' => 'Sorry, your nonce did not verify.' ) );
	}

	if( ! ZN_HogashDashboard::isConnected() ){
		ZN_HogashDashboard::clearDemosList();
		wp_send_json_error( array( 'error' => 'Sorry, your need to register your theme before using this functionality.' ) );
	}

	//#! All good
	ZN_HogashDashboard::clearDemosList();
	$status = ZN_HogashDashboard::getAllDemos();
	if(is_array($status) && isset($status['error'])){
		wp_send_json_error( array( 'error' => $status['error']) );
	}
	wp_send_json_success('1');
}

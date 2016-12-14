<?php if(! defined('ABSPATH')){ return; } ?>
<?php
$m = '';

if( 'POST' == strtoupper($_SERVER['REQUEST_METHOD']) && isset($_POST) && ! empty($_POST))
{
    function __validateRequest()
    {
        if ( ! isset( $_POST['zn_nonce'] ) || ! wp_verify_nonce( $_POST['zn_nonce'], 'zn_theme_registration' ) ) {
            return array(
                'success' => false,
                'data' => __('Sorry, your nonce did not verify.', 'zn_framework')
            );
        }

        $apiKey = isset( $_POST['dash_api_key'] ) ? esc_attr($_POST['dash_api_key']) : '';

        if( ! empty( $apiKey ) )
        {
            $response = ZN_HogashDashboard::connectTheme( $apiKey );

            if(isset($response['error'])){
                return array(
                    'success' => false,
                    'data' => $response['error']
                );
            }

            if( isset($response['success']) && $response['success']){
                ZN_HogashDashboard::updateApiKey($apiKey);
                return array(
                    'success' => true,
                    'data' => __('Thank you! Your theme is now connected with the <a href="http://my.hogash.com/" target="_blank">Hogash Dashboard</a>.', 'zn_framework')
                );
            }
            else {
                return array(
                    'success' => false,
                    'data' => $response['data']
                );
            }
        }
        return array(
            'success' => false,
            'data' => __('An error occurred. Please try again in a few moments.', 'zn_framework')
        );
    }

    $m = __validateRequest();
}

include( FW_PATH . '/admin/tmpl/register-tmpl.php' );

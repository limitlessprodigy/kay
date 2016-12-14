<?php

/**
 * Theme's service. Interacts with our demo server and retrieves the list of all available demos.
 * @requires valid user
 */
class ZN_HogashDashboard
{
	const DASH_ENDPOINT_URL = 'http://my.hogash.com/';

	const THEME_CHECK_TRANSIENT = 'hg_dash_theme_check';

    const THEME_DEMOS_TRANSIENT = 'hg_dash_theme_demos';

    const THEME_API_KEY_OPTION = 'hg_dash_api_key';

    const NETWORK_MENU_SLUG = 'kdash_';


    public static function init()
    {
        add_action('network_admin_menu', array(get_class(), 'createNetworkMenu'));
    }
    public static function createNetworkMenu(){
        add_menu_page(__('Kallyas Dashboard', 'zn_framework'), __('Kallyas Dashboard', 'zn_framework'), 'create_sites', self::NETWORK_MENU_SLUG, array(get_class(), 'render_network_page'));
    }
    public static function render_network_page(){
        wp_enqueue_style( 'zn_about_style', FW_URL .'/admin/assets/css/zn_about.css', array(), ZN()->version );
        include(FW_PATH.'/admin/tmpl/network-page.php');
    }


    public static function connectTheme( $apiKey )
    {
        $response = self::request( array(
            'body' => array(
                'action' => 'register',
                'api_key' => $apiKey,
                'site_url' => esc_url(home_url('/'))
            )
        ));

        if(is_wp_error($response)){
            return array( 'error' => $response->get_error_message());
        }

        if(! isset($response['body']) || empty($response['body'])){
            return array( 'error' => __('Invalid response retrieved from server', 'zn_framework'));
        }

        return json_decode( $response['body'], true );
    }

    public static function isConnected( $useCurrent = true )
    {
        if( $useCurrent )
        {
            $t = get_site_transient( self::THEME_CHECK_TRANSIENT );
            if ( $t )
            {
                return ( $t != '0x' );
            }
        }

        if( empty($apiKey) ){
            $apiKey = self::getApiKey();
        }
        $response = self::request( array(
            'body' => array(
                'action' => 'theme_check',
                'api_key' => $apiKey,
                'site_url' => esc_url(home_url('/'))
            )
        ));

        if(is_wp_error($response) ){
            return false;
        }
        if( !is_array($response) || ! isset($response['body']) || empty($response['body']) ){
            return false;
        }

        $data = json_decode( $response['body'], true );

        if(! is_array($data) || ! isset($data['success']) || !isset($data['data'])){
            return false;
        }

        if(!$data['success']){
            return false;
        }

        if( 1 == intval($data['data']) ){
            set_site_transient( self::THEME_CHECK_TRANSIENT , '1x', DAY_IN_SECONDS );
            return true;
        }

        return false;
    }

    /**
     * Retrieve the list of all demos
     * @param string $apiKey
     * @return array
     */
	public static function getAllDemos( $apiKey = ''  )
	{
        if(empty($apiKey)){
            $apiKey = self::getApiKey();
        }

        if(! self::isConnected($apiKey)){
            return array('error' => __('You need to connect the theme with the Hogash Dashboard in order to be able to install any demo.','zn_framework'));
        }

        // Check transient
        $cache = get_site_transient( self::THEME_DEMOS_TRANSIENT );
        if(! empty($cache)){
            return $cache;
        }

        $response = self::request( array(
            'body' => array(
                'action' => 'list_demos',
                'api_key' => $apiKey,
                'site_url' => esc_url(home_url('/')),
                'theme' => 'kallyas',
            )
        ));

        if(is_wp_error($response) ){
            return array('error' => $response->get_error_message());
        }
        if( !is_array($response) || ! isset($response['body']) || empty($response['body']) ){
            return array('error' => __('1 Invalid response from server.', 'zn_framework'));
        }

        $data = json_decode( $response['body'], true );

        if(! is_array($data) || ! isset($data['success']) || !isset($data['data'])){
            return array('error' => __('2 Invalid response from server.', 'zn_framework'));
        }

        if(!$data['success']){
            return array('error' => __('3 Invalid response from server: '.$data['data'], 'zn_framework'));
        }

        if(empty($data['data'])){
            return array('error' => __('No demos retrieved.', 'zn_framework'));
        }

        $result = $data['data'];

        set_site_transient( self::THEME_DEMOS_TRANSIENT, $result, DAY_IN_SECONDS );
        return $result;
	}

    public static function getDemo( $demoName = '', $savePath = '' )
    {
        if(empty($demoName) || empty($savePath)){
            return false;
        }

        $apiKey = self::getApiKey();
        if(empty($apiKey)){
            return false;
        }

        if(! self::isConnected($apiKey)){
            return false;
        }

        $response = self::request(array(
            'body' => array(
                'action' => 'get_demo',
                'api_key' => $apiKey,
                'site_url' => esc_url(home_url('/')),
                'theme' => 'kallyas',
                'demo' => $demoName
            )
        ));

        if(is_array($response) && isset($response['body']))
        {
            $content = $response['body'];

            // Check for the zip content
            $len = strlen('[zip]');
            if( '[zip]' == substr($content, 0, $len))
            {
                $content = substr( $content, $len );
            }

            if( false !== WP_Filesystem() )
            {
                global $wp_filesystem;
                $wp_filesystem->put_contents( $savePath, $content );
            }

            //#!
            $r = file_put_contents( $savePath, $content );
            if($r > 0){
                return $savePath;
            }
            return false;
        }

        return false;
    }

    /**
     * Retrieve the information about the theme from Dashboard
     * @return bool|mixed
     */
    public static function getThemeInfo()
    {
        $apiKey = self::getApiKey();
        if(empty($apiKey)){
            return false;
        }

        if(! self::isConnected($apiKey)){
            return false;
        }

        $response = self::request(array(
            'body' => array(
                'action' => 'get_theme_info',
                'api_key' => $apiKey,
                'site_url' => esc_url(home_url('/')),
                'theme' => 'kallyas'
            )
        ));

        if(is_array($response) && isset($response['body']))
        {
            $response = json_decode($response[ 'body' ], true);
            if(!is_array($response) || !isset($response['success']) || !$response['success']){
                return false;
            }

            if(! isset($response['data']) || empty($response['data'])){
                return false;
            }

            return $response['data'];
        }
        return false;
    }

//<editor-fold desc="::: UTILITY METHODS">
    public static function request( $args = array() )
    {
        $args = array_merge(array(
            'timeout' => apply_filters( 'http_request_timeout', 30 ),
            'redirection' => apply_filters( 'http_request_redirection_count', 10 ),
            'sslverify' => false,
        ), $args);
        return wp_remote_post( self::DASH_ENDPOINT_URL, $args);
    }

    /**
     * Retrieve the saved API key
     * @return string
     */
    public static function getApiKey()
    {
        $apiKey = get_site_option( self::THEME_API_KEY_OPTION );
        return (empty($apiKey) ? '' : wp_strip_all_tags($apiKey));

    }

    public static function updateApiKey( $apiKey = '' )
    {
        if(empty($apiKey)){
            return false;
        }
        return update_site_option( self::THEME_API_KEY_OPTION, $apiKey );
    }

    public static function isWPMU()
    {
        return (function_exists('is_multisite') && is_multisite());
    }

	/**
	 * Delete the cached list of demos
	 */
	public static function clearDemosList()
	{
		delete_site_transient( self::THEME_DEMOS_TRANSIENT );
	}
//</editor-fold desc="::: UTILITY METHODS">
}
ZN_HogashDashboard::init();

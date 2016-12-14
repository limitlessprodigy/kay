<?php if(! defined('ABSPATH')){ return; }

if( ZN_HogashDashboard::isWPMU() )
{
    $isConnected = ZN_HogashDashboard::isConnected();
    $isNetworkPage = (isset($_REQUEST['page']) && $_REQUEST['page'] == ZN_HogashDashboard::NETWORK_MENU_SLUG);

    if( $isNetworkPage )
    {
        include( FW_PATH .'/admin/tmpl/form-register-theme-tmpl.php');
    }
    else {
        if( $isConnected){
            ?>
            <div class="inline notice notice-error">
                <p><?php _e('The theme has already been registered and connected with the Hogash Dashboard', 'zn_framework');?> </p>
            </div>
            <?php
        }
        else {
            ?>
            <div class="inline notice notice-error">
                <p><?php

                    echo sprintf(
                        __('Please register the theme through the <a href="%s" target="_blank">Multisite Network Dashboard</a>, or contact the network administrator and ask them to register the theme with the Hogash Dashboard.', 'zn_framework'),
                        network_admin_url('admin.php?page=kdash_')
                    );
                    ?>
                </p>
            </div>
            <?php
        }
    }
}
else {
    include( FW_PATH .'/admin/tmpl/form-register-theme-tmpl.php');
}




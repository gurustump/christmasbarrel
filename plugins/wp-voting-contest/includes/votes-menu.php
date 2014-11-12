<?php   
if(!function_exists('wp_voting_software_license_page')) {
    function wp_voting_software_license_page() {
        $license = get_option('wp_voting_software_license_key');
        $status = get_option('wp_voting_software_license_status');
        ?>
        <div class="wrap">
            <h2><?php _e('Plugin License Options','voting-contest'); ?></h2>
            <form method="post" action="options.php">
    
                <?php settings_fields('wp_voting_software_license'); ?>
    
                <table class="form-table">
                    <tbody>
                        <tr valign="top">	
                            <th scope="row" valign="top">
                                <?php _e('License Key','voting-contest'); ?>
                            </th>
                            <td>
                                <input id="wp_voting_software_license_key" name="wp_voting_software_license_key" type="text" class="regular-text" value="<?php esc_attr_e($license); ?>" />
                                <label class="description" for="wp_voting_software_license_key"><?php _e('Enter your license key','voting-contest'); ?></label>
                            </td>
                        </tr>
                        <?php if (false !== $license) { ?>
                            <tr valign="top">	
                                <th scope="row" valign="top">
                                    <?php _e('Activate License','voting-contest'); ?>
                                </th>
                                <td>
                                    <?php if ($status !== false && $status == 'valid') { ?>
                                        <span style="color:green;"><?php _e('active','voting-contest'); ?></span>
                                    <?php
                                    } else {
                                        wp_nonce_field('wp_voting_software_nonce', 'wp_voting_software_nonce');
                                        ?>
                                        <input type="submit" class="button-secondary" name="wp_voting_license_activate" value="<?php _e('Activate License','voting-contest'); ?>"/>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>	
                <?php submit_button(__('Save Changes','voting-contest')); ?>
    
            </form>
            <?php
        }
}

if(!function_exists('wp_voting_software_register_option')) {
    function wp_voting_software_register_option() {
        register_setting('wp_voting_software_license', 'wp_voting_software_license_key', 'wp_voting_sanitize_license');
    }
}
add_action('admin_init', 'wp_voting_software_register_option');
        
if(!function_exists('wp_voting_sanitize_license')) {
    function wp_voting_sanitize_license($new) {
        $old = get_option('wp_voting_software_license_key');
        if ($old && $old != $new) {
            delete_option('wp_voting_software_license_status'); 
        }
        return $new;
    }
}

if(!function_exists('wp_voting_software_activate_license')) {
    function wp_voting_software_activate_license() {

        if (isset($_POST['wp_voting_license_activate'])) {

            if (!check_admin_referer('wp_voting_software_nonce', 'wp_voting_software_nonce'))
                return; 
            $license = trim(get_option('wp_voting_software_license_key'));

            $api_params = array(
                'edd_action' => 'activate_license',
                'license' => $license,
                'item_name' => urlencode(WP_VOTING_SL_PRODUCT_NAME) 
            );

            $response = wp_remote_get(add_query_arg($api_params, WP_VOTING_SL_STORE_API_URL));

            if (is_wp_error($response))
                return false;

            $license_data = json_decode(wp_remote_retrieve_body($response));

            update_option('wp_voting_software_license_status', $license_data->license);
        }
    }
}
add_action('admin_init', 'wp_voting_software_activate_license');
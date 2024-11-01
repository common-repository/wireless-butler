<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.google.com/
 * @since      1.0.0
 *
 * @package    Wireless_Butler
 * @subpackage Wireless_Butler/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">
    <h1><?php _e( 'Wireless Butler', 'wireless_butler' ); ?></h1>
    
    <form method="post" action="options.php">
        <?php settings_fields( 'wireless_butler_plugin_options' ); ?>
        <?php do_settings_sections( 'wireless_butler_plugin' ); ?>
        <h3><?php _e( 'Form Shortcode', 'wireless_butler' ); ?></h3>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e( 'Form Shortcode', 'wireless_butler' ); ?></th>
                <td>
                    <input type="text"value="[wireless_butler_form_1]" readonly />
                </td>
            </tr>
        </table>
        <h3><?php _e( 'Step 1 Form Options', 'wireless_butler' ); ?></h3>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e( 'Form Greeting', 'wireless_butler' ); ?></th>
                <td>
                    <input type="text" name="wireless_butler_form_1_step_1_greeting" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_1_greeting') ); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Form Heading', 'wireless_butler' ); ?></th>
                <td>
                    <input type="text" name="wireless_butler_form_1_step_1_heading" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_1_heading') ); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Form Label', 'wireless_butler' ); ?></th>
                <td>
                    <input type="text" name="wireless_butler_form_1_step_1_label" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_1_label') ); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Select the options that match your account holder', 'wireless_butler' ); ?></th>
                <td>
                    <input type="text" name="wireless_butler_form_1_step_1_account_holder" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_1_account_holder') ); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Tell us how many non-smartphones you have on your bill.', 'wireless_butler' ); ?></th>
                <td>
                    <input type="text" name="wireless_butler_form_1_step_1_smartphone_heading" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_1_smartphone_heading') ); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Button Text', 'wireless_butler' ); ?></th>
                <td>
                    <input type="text" name="wireless_butler_form_1_step_1_button_text" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_1_button_text') ); ?>" />
                </td>
            </tr>
        </table>
        <h3><?php _e( 'Step 2 Form Options', 'wireless_butler' ); ?></h3>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e( 'Monthly Savings Text', 'wireless_butler' ); ?></th>
                <td>
                    <input type="text" name="wireless_butler_form_1_step_2_heading" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_2_heading') ); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Already on cheapest plan text', 'wireless_butler' ); ?></th>
                <td>
                    <input type="text" name="wireless_butler_form_1_step_2_chepest_plan_text" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_2_chepest_plan_text') ); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Total Bill Label', 'wireless_butler' ); ?></th>
                <td>
                    <input type="text" name="wireless_butler_form_1_step_2_total_bill" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_2_total_bill') ); ?>" />
                    </td>
                </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Month Bill Label', 'wireless_butler' ); ?></th>
                <td>
                    <input type="text" name="wireless_butler_form_1_step_2_latest_month_bill" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_2_latest_month_bill') ); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Past Due Label', 'wireless_butler' ); ?></th>
                <td>
                    <input type="text" name="wireless_butler_form_1_step_2_past_due" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_2_past_due') ); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Total Plan Charges Label', 'wireless_butler' ); ?></th>
                <td>
                    <input type="text" name="wireless_butler_form_1_step_2_total_plan_charges" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_2_total_plan_charges') ); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'GB in Your Plan Label', 'wireless_butler' ); ?></th>
                <td>
                    <input type="text" name="wireless_butler_form_1_step_2_gb_in_your_plan" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_2_gb_in_your_plan') ); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'GB of Data Used Label', 'wireless_butler' ); ?></th>
                <td>
                    <input type="text" name="wireless_butler_form_1_step_2_gb_of_data_used" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_2_gb_of_data_used') ); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Device Balance Label', 'wireless_butler' ); ?></th>
                <td>
                    <input type="text" name="wireless_butler_form_1_step_2_device_balance" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_2_device_balance') ); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Device Owned Label', 'wireless_butler' ); ?></th>
                <td>
                    <input type="text" name="wireless_butler_form_1_step_2_device_owned" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_2_device_owned') ); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Wireless Butler reach out text', 'wireless_butler' ); ?></th>
                <td>
                    <input type="text" name="wireless_butler_form_1_step_2_reach_out_text" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_2_reach_out_text') ); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Button Text', 'wireless_butler' ); ?></th>
                <td>
                    <input type="text" name="wireless_butler_form_1_step_2_button_text" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_2_button_text') ); ?>" />
                </td>
            </tr>
           
        </table>
        
        <input type="hidden" name="wireless_butler_email_to_user_subject" value="<?php echo esc_attr(get_option('wireless_butler_email_to_user_subject')); ?>" />
        <input type="hidden" name="wireless_butler_email_to_user_content" value="<?php echo esc_html(get_option('wireless_butler_email_to_user_content')); ?>" />
        <input type="hidden" name="wireless_butler_notification_email" value="<?php echo esc_attr( get_option('wireless_butler_notification_email') ); ?>" />
        <input type="hidden" name="wireless_butler_form_notification_template" value="<?php echo esc_html(get_option('wireless_butler_form_notification_template')); ?>" />

        <?php submit_button(); ?>

    </form>
</div>
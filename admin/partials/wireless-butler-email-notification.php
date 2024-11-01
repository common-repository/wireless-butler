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
    <h1><?php _e( 'Wireless Butler Email Notification', 'wireless_butler' ); ?></h1>
    
    <form method="post" action="options.php">
        <h3><?php _e( 'Email Notification to Admin', 'wireless_butler' ); ?></h3>
        <?php settings_fields( 'wireless_butler_plugin_options' ); ?>
        <?php do_settings_sections( 'wireless_butler_plugin' ); ?>
        <table class="form-table">  
            <tr valign="top">
                <th scope="row"><?php _e( 'Notification Email', 'wireless_butler' ); ?></th>
                <td>
                    <input type="text" name="wireless_butler_notification_email" value="<?php echo esc_attr( get_option('wireless_butler_notification_email') ); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Email Template', 'wireless_butler' ); ?></th>
                <td>
                    <p><label for="wireless_butler_form_notification_template"><b><?php _e( 'Placeholders', 'wireless_butler' ); ?>:</b> [FIRST_NAME], [LAST_NAME], [EMAIL], [PHONE], [CARRIER],[WIRELESS_BILL], [TOTAL_BILL], [LATEST_MONTH_BILL], [PAST_DUE], [TOTAL_PLAN_CHARGES], [USED_DATA], [TOTAL_PLAN_DATA], [SAVINGS_AMOUNT], [DEVICE_BALANCE], [DEVICE_OWNED], [RECOMMENDATION_URL]</label></p>
                    <textarea name="wireless_butler_form_notification_template" rows="15" cols="75" id="wireless_butler_form_notification_template" class="code" spellcheck="false"><?php echo esc_html(get_option('wireless_butler_form_notification_template')); ?></textarea>
                </td>
            </tr>
        </table>

        <h3><?php _e( 'Email to User', 'wireless_butler' ); ?></h3>
        <table class="form-table">  
            <tr valign="top">
                <th scope="row"><?php _e( 'Email Subject', 'wireless_butler' ); ?></th>
                <td>
                    <input type="text" name="wireless_butler_email_to_user_subject" value="<?php echo esc_attr( get_option('wireless_butler_email_to_user_subject') ); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Email Content', 'wireless_butler' ); ?></th>
                <td>
                    <textarea name="wireless_butler_email_to_user_content" rows="15" cols="75" id="wireless_butler_email_to_user_content" class="code" spellcheck="false"><?php echo esc_html(get_option('wireless_butler_email_to_user_content')); ?></textarea>
                </td>
            </tr>
        </table>

        <input type="hidden" name="wireless_butler_form_1_step_1_greeting" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_1_greeting') ); ?>" />
        <input type="hidden" name="wireless_butler_form_1_step_1_heading" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_1_heading') ); ?>" />
        <input type="hidden" name="wireless_butler_form_1_step_1_label" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_1_label') ); ?>" />
        <input type="hidden" name="wireless_butler_form_1_step_1_account_holder" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_1_account_holder') ); ?>" />
        <input type="hidden" name="wireless_butler_form_1_step_1_smartphone_heading" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_1_smartphone_heading') ); ?>" />
        <input type="hidden" name="wireless_butler_form_1_step_1_button_text" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_1_button_text') ); ?>" />

        <input type="hidden" name="wireless_butler_form_1_step_2_heading" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_2_heading') ); ?>" />
        <input type="hidden" name="wireless_butler_form_1_step_2_chepest_plan_text" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_2_chepest_plan_text') ); ?>" />
        <input type="hidden" name="wireless_butler_form_1_step_2_total_bill" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_2_total_bill') ); ?>" />
        <input type="hidden" name="wireless_butler_form_1_step_2_latest_month_bill" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_2_latest_month_bill') ); ?>" />
        <input type="hidden" name="wireless_butler_form_1_step_2_past_due" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_2_past_due') ); ?>" />
        <input type="hidden" name="wireless_butler_form_1_step_2_total_plan_charges" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_2_total_plan_charges') ); ?>" />
        <input type="hidden" name="wireless_butler_form_1_step_2_gb_of_data_used" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_2_gb_of_data_used') ); ?>" />
        <input type="hidden" name="wireless_butler_form_1_step_2_gb_in_your_plan" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_2_gb_in_your_plan') ); ?>" />
        <input type="hidden" name="wireless_butler_form_1_step_2_reach_out_text" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_2_reach_out_text') ); ?>" />
        <input type="hidden" name="wireless_butler_form_1_step_2_device_balance" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_2_device_balance') ); ?>" />
        <input type="hidden" name="wireless_butler_form_1_step_2_device_owned" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_2_device_owned') ); ?>" />
        <input type="hidden" name="wireless_butler_form_1_step_2_button_text" value="<?php echo esc_attr( get_option('wireless_butler_form_1_step_2_button_text') ); ?>" />

        <?php submit_button(); ?>

    </form>
</div>
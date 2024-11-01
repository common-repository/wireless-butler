<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.google.com/
 * @since      1.0.0
 *
 * @package    Wireless_Butler
 * @subpackage Wireless_Butler/public/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wireless_butler_form_1 alignwide">
   <div class="wireless_butler_form_1_step_1">
      <div class="titlebill">
         <h3>
            <?php echo esc_attr(get_option('wireless_butler_form_1_step_1_greeting')); ?> <br> 
            <span><?php echo esc_attr(get_option('wireless_butler_form_1_step_1_heading')); ?></span>
         </h3>
         <div class="title-heading"><?php echo esc_attr(get_option('wireless_butler_form_1_step_1_label')); ?></div>
      </div>
      <form method="post" enctype="multipart/form-data" id="wirelessButlerForm1Step1">
         <div class="bill-form">
            <div class="formrow">
               <div class="col-6">
                  <label for="fname"><?php _e( 'First Name', 'wireless_butler' ); ?>:</label>
                  <input type="text" class="formcntrl" name="fname" id="fname" placeholder="<?php _e( 'Enter your first name', 'wireless_butler' ); ?>">
                  <div class="err hide" id="fname_err"></div>
               </div>
               <div class="col-6">
                  <label for="fname"><?php _e( 'Last Name', 'wireless_butler' ); ?>:</label>
                  <input type="text" class="formcntrl" name="lname" placeholder="<?php _e( 'Enter your last name', 'wireless_butler' ); ?>" id="lname">
                  <div class="err hide" id="lname_err"></div>
               </div>
               <div class="col-6">
                  <label for="fname"><?php _e( 'Mail Address', 'wireless_butler' ); ?>:</label>
                  <input type="email" class="formcntrl" name="email" placeholder="<?php _e( 'Enter your email', 'wireless_butler' ); ?>">
                  <div class="err hide" id="email_err"></div>
               </div>
               <div class="col-6">
                  <label for="fname"><?php _e( 'Phone Number', 'wireless_butler' ); ?>:</label>
                  <input type="text" class="formcntrl" name="phone" placeholder="<?php _e( 'Enter your phone', 'wireless_butler' ); ?>">
               </div>
               <div class="col-12">
                  <div class="input-select inputmrgin">
                     <label for="service"><?php _e( 'Who do you have service with?', 'wireless_butler' ); ?>:</label>
                     <div class="formcntrl12">
                        <select name="carrier_id" class="formcntrl carrier-select">
                           <option value=""><?php _e( 'Choose your carrier', 'wireless_butler' ); ?></option>
                           <?php
                           foreach($carrierList as $carrier) {
                              echo '<option value="'.esc_attr($carrier['carrier_id']).'">'.esc_attr($carrier['name']).'</option>';
                           }
                           ?>
                        </select>
                        <div class="err hide" id="carrier_err"></div>
                     </div>
                  </div>
               </div>
               <div class="col-12 select-opt-title inputmrgin">
                  <?php echo esc_attr(get_option('wireless_butler_form_1_step_1_account_holder')); ?></br>
                  <?php _e( 'These help us see what discounts you\'re eligible for.', 'wireless_butler' ); ?>
               </div>
               <div class="select-opt">
                  <input type="checkbox" name="auto_pay_with_carrier" value="1" id="autopay"> 
                  <label for="autopay"><?php _e( 'I am willing to enroll in Auto Pay with my carrier.', 'wireless_butler' ); ?></label>
               </div>
               <div class="select-opt">
                  <input type="checkbox" name="in_military" value="1" id="Military"> 
                  <label for="Military"><?php _e( 'I am in Military', 'wireless_butler' ); ?></label>
               </div>
               <div class="select-opt">
                  <input type="checkbox" name="over_55_year_of_age" value="1" id="other"> 
                  <label for="other"><?php _e( 'I am over 55 years of age', 'wireless_butler' ); ?></label>
               </div>
               <div class="col-12 select-opt-title inputmrgin">
                  <p> 
                     <?php echo esc_attr(get_option('wireless_butler_form_1_step_1_smartphone_heading')); ?></br>
                     <?php _e( 'Leave blank if none', 'wireless_butler' ); ?>
                  </p>
               </div>
               <div class="col-3 mt0">
                  <label for="fname"><?php _e( 'Basic Phone', 'wireless_butler' ); ?>:</label>
                  <div class="input-bx">
                     <input type="number" class="formcntrl" name="basic_phone" id="basicPhone" placeholder="0">
                  </div>
               </div>
               <div class="col-3 mt0">
                  <label for="fname"><?php _e( 'Tablet', 'wireless_butler' ); ?>:</label>
                  <div class="input-bx">
                     <input type="number" class="formcntrl" name="tablet" id="tablet" placeholder="0">
                  </div>
               </div>
               <div class="col-3 mt0">
                  <label for="fname"><?php _e( 'Mobile Hotspot Only', 'wireless_butler' ); ?>:</label>
                  <div class="input-bx">
                     <input type="number" class="formcntrl" name="mobile_hotspot" id="mobileHotspot" placeholder="0">
                  </div>
               </div>
               <div class="col-3 mt0">
                  <label for="fname"><?php _e( 'Wearable', 'wireless_butler' ); ?>:</label>
                  <div class="input-bx">
                     <input type="number" class="formcntrl" name="wearable" id="wearable" placeholder="0">
                  </div>
               </div>
               <div class="select-opt inputmrgin">
                  <input type="hidden" name="manual" value="0"> 
                  <input type="checkbox" name="manual" value="1" id="wirelessButlerManual"> 
                  <label for="wirelessButlerManual"><?php _e( 'I don\'t have bill.', 'wireless_butler' ); ?></label>
               </div>
               <div class="dropzone-section">
                  <div class="fallback">
                     <label for="billFile" class="formcntrl">Upload Your Bill</label>
                     <input name="bill" type="file" accept=".pdf" style="visibility:hidden;" id="billFile"/>
                     <div class="err hide" id="bill_err">This is required</div>
                     <div style="display:inline-block;">
                        <a href="https://wirelessbutlerserver.com/wp/wp-content/uploads/2022/05/Verizon.mp4" target="_blank" class="verizon-video-link bill-video hide">Click here to see how to download your Verizon bill</a>
                        <a href="https://wirelessbutlerserver.com/wp/wp-content/uploads/2022/05/Sprint.mp4" target="_blank" class="sprint-video-link bill-video hide">Click here to see how to download your Sprint bill</a>
                     </div>
                  </div>
               </div>
               <div class="submit-btn-sec">
                  <button type="submit" class="btn submit-btn">
                     <span class="loader hide">
                        <img src="<?php echo esc_url(plugin_dir_url(__DIR__).'images/spinner.gif'); ?>" class="loader-img"/>
                        <?php _e( 'Please Wait', 'wireless_butler' ); ?>
                     </span>
                     <span class="btn-element"><?php echo esc_attr(get_option('wireless_butler_form_1_step_1_button_text')); ?></span>
                  </button>
               </div>
            </div>
         </div>
      </form>
   </div>
   <div class="wireless_butler_form_1_step_2 hide">
      <div class="title-heading hide" id="suggestionText"></div>
      <form id="wirelessButlerForm1Step2">
         <div class="bill-form">
            <div class="formrow">
               <div class="col-4 mt15">
                  <label for="lservice"><?php echo esc_attr(get_option('wireless_butler_form_1_step_2_total_bill')); ?></label>
                  <input type="text" class="formcntrl currencyFormat" list="totalBillList" id="totalBill">
                  <datalist id="totalBillList"></datalist>
                  <div class="err hide" id="totalBill_err"></div>
               </div>
               <div class="col-4 mt15 hide">
                  <label for="pcst"><?php echo esc_attr(get_option('wireless_butler_form_1_step_2_latest_month_bill')); ?></label>
                  <input type="hidden" class="formcntrl" id="latestMonthBill" list="latestMonthBillList">
                  <datalist id="latestMonthBillList"></datalist>
                  <div class="err hide" id="latestMonthBillList_err"></div>
               </div>
               <div class="col-4 mt15 hide">
                  <label for="fname"><?php echo esc_attr(get_option('wireless_butler_form_1_step_2_past_due')); ?></label>
                  <input type="hidden" class="formcntrl" id="pastDue" list="pastDueList">
                  <datalist id="pastDueList"></datalist>
                  <div class="err hide" id="pastDueList_err"></div>
               </div>
               <div class="col-4 mt15">
                  <label for="gbpln"><?php echo esc_attr(get_option('wireless_butler_form_1_step_2_total_plan_charges')); ?></label>
                  <input type="text" class="formcntrl currencyFormat" id="totalPlanCharges" list="totalPlanChargesList">
                  <datalist id="totalPlanChargesList"></datalist>
                  <div class="err hide" id="totalPlanChargesList_err"></div>
               </div>
               <div class="col-4 mt15">
                  <?php echo "<br>"; ?>
               </div>
               <div class="col-4 mt15">
                  <label for="gbnpln"><?php echo esc_attr(get_option('wireless_butler_form_1_step_2_gb_of_data_used')); ?></label>
                  <input type="text" class="formcntrl" id="usedData" list="usedDataList">
                  <datalist id="usedDataList"></datalist>
                  <div class="err hide" id="usedDataList_err"></div>
               </div>
               <div class="col-4 mt15">
                  <label for="plnname"><?php echo esc_attr(get_option('wireless_butler_form_1_step_2_gb_in_your_plan')); ?></label>
                  <input type="text" class="formcntrl" id="totalPlanData" list="totalPlanDataList">
                  <datalist id="totalPlanDataList"></datalist>
                  <div class="err hide" id="totalPlanDataList_err"></div>
               </div>
               <div class="col-4 mt15">
                  <?php echo "<br>"; ?>
               </div>
               <div class="col-4 mt15">
                  <label for="dvcBal"><?php echo esc_attr(get_option('wireless_butler_form_1_step_2_device_balance')); ?></label>
                  <input type="text" class="formcntrl currencyFormat" id="deviceBalance" list="deviceBalanceList">
                  <datalist id="deviceBalanceList"></datalist>
                  <div class="err hide" id="deviceBalanceList_err"></div>
               </div>
               <div class="col-12 mt15">
                  <label for="dvcOwn"><?php echo esc_attr(get_option('wireless_butler_form_1_step_2_device_owned')); ?></label>
                  <textarea class="formcntrl" id="deviceOwned" cols="10" ></textarea>
                  <div class="err hide" id="deviceOwnedList_err"></div>
               </div>
               <div class="submit-btn-sec">
                  <input type="hidden" id="rowID">
                  <button type="submit" class="btn submit-btn showpln">
                     <span class="loader hide">
                        <img src="<?php echo esc_url(plugin_dir_url(__DIR__).'images/spinner.gif'); ?>" class="loader-img"/>
                        <?php _e( 'Please Wait', 'wireless_butler' ); ?>
                     </span>
                     <span class="btn-element"><?php echo esc_attr(get_option('wireless_butler_form_1_step_2_button_text')); ?></span>
                  </button>
               </div>
            </div>
         </div>
      </form>
      <div class="reach-txt"><?php echo esc_attr(get_option('wireless_butler_form_1_step_2_reach_out_text')); ?></div>
   </div>
   <div class="title-heading wireless_butler_form_1_step_2_message hide"><?php _e( 'Thanks for submitting. Email has been sent.', 'wireless_butler' ); ?></div>
</div>
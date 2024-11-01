(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	
	

	$(document).ready(function(event) {
		/**
		 * Number.prototype.format(n, x, s, c)
		 * 
		 * @param integer n: length of decimal
		 * @param integer x: length of whole part
		 * @param mixed   s: sections delimiter
		 * @param mixed   c: decimal delimiter
		 */
		Number.prototype.format = function(n, x, s, c) {
			var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
				num = this.toFixed(Math.max(0, ~~n));
			
			return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
		};

		$('#wirelessButlerManual').change(function(){
			if(!$(this).prop('checked')) {
				$('.dropzone-section').removeClass('hide');
			}else{
				$('.dropzone-section').addClass('hide');
				$('#bill_err').addClass('hide');
			}
		});

		$('#wirelessButlerForm1Step1').submit(wirelessButlerForm1Step1Submit);
		$('#wirelessButlerForm1Step2').submit(wirelessButlerForm1Step2Submit);

		function isInt(n){
			return Number(n) === n && n % 1 === 0;
		}
		
		function isFloat(n){
			var floatValues =  /[+-]?([0-9]*[.])?[0-9]+/;
			if (n.match(floatValues) && !isNaN(n)) {
				return true;
			}
			return false;
		}

		function validateEmail(email) {
			const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			return re.test(String(email).toLowerCase());
		}

		$('select[name=carrier_id]').change(function(){
			var carrierID = $(this).val();
			$('.bill-video').addClass('hide');
			if(carrierID == 1) {
				$('.verizon-video-link').removeClass('hide');
			}else if(carrierID == 2){
				$('.sprint-video-link').removeClass('hide');
			}
		});

		function wirelessButlerForm1Step1Submit(e) {
			var ths = $(this);
			e.preventDefault();
			
			var err = false;
			$(this).find('.err').addClass('hide');
			var fname =  $(this).find('input[name=fname]').val();
			var lname =  $(this).find('input[name=lname]').val();
			var email =  $(this).find('input[name=email]').val();
			var carrierID =  $(this).find('select[name=carrier_id]').val();
			var manual = $('#wirelessButlerManual').prop('checked');
			if(fname == '') {
				err = true
				$('#fname_err').html('This is required').removeClass('hide');
			}
			if(lname == '') {
				err = true
				$('#lname_err').html('This is required').removeClass('hide');
			}
			if(email == '') {
				err = true
				$('#email_err').html('This is required').removeClass('hide');
			}else if(!validateEmail(email)) {
				err = true
				$('#email_err').html('Please enter valid email').removeClass('hide');
			}
			if(carrierID == '') {
				err = true
				$('#carrier_err').html('This is required').removeClass('hide'); 
			}
			if(!manual) {
				//check file uploaded
				if($(this).find('input[name=bill]').val() == '') {
					err = true
					$('#bill_err').html('This is required').removeClass('hide');
				}
			}
			if(!err) {
				var autoPayWithCarrier = ( $(this).find('input[name=auto_pay_with_carrier]').prop('checked'))? '1':'0';
				var inMilitary = ( $(this).find('input[name=in_military]').prop('checked'))? '1':'0';
				var over55YearOfAge = ( $(this).find('input[name=over_55_year_of_age]').prop('checked'))? '1':'0';
				var formData = new FormData();
				formData.append("bill", $(this).find('input[name=bill]').prop('files')[0]);
				formData.append("fname", fname);
				formData.append("lname", lname);
				formData.append("email", email);
				formData.append("phone", $(this).find('input[name=phone]').val());
				formData.append("auto_pay_with_carrier", autoPayWithCarrier);
				formData.append("in_military", inMilitary);
				formData.append("over_55_year_of_age",over55YearOfAge);
				formData.append("basic_phone", $(this).find('input[name=basic_phone]').val());
				formData.append("tablet", $(this).find('input[name=tablet]').val());
				formData.append("mobile_hotspot", $(this).find('input[name=mobile_hotspot]').val());
				formData.append("wearable", $(this).find('input[name=wearable]').val());
				formData.append("carrier_id", carrierID);
				formData.append("action", 'wireless_butler_form_1_step_1');
				formData.append("manual", $(this).find('input[name=manual]').val());
	
				$(this).find('button[type=submit]').attr('disabled', true);
				$(this).find('button[type=submit]').find('.btn-element').addClass('hide');
				$(this).find('button[type=submit]').find('.loader').removeClass('hide');
	
				$.ajax({
					action:  		'wireless_butler_form_1_step_1',
					type:    		"POST",
					url:     		wirelessButlerObj.ajaxurl,
					data:    		formData,
					cache: 			false,
					processData: 	false,
					contentType: 	false,
					dataType: 		'json',
					success: function(response) {
						if(response.success) {
							var data = response.data;
							$(".wireless_butler_form_1_step_1").addClass('hide');
							$(".wireless_butler_form_1_step_2").removeClass('hide');
		
							$("#rowID").val(data.id);
							if(typeof data.totalBill != 'undefined') {
								$("#totalBill").val((typeof data.totalBill[0] != 'undefined')? data.totalBill[0] : '');
								if(data.totalBill.length > 1) {
									var totalBillList = '';
									data.totalBill.forEach(function(el){
										totalBillList += '<option value="'+el+'">';
									})
									$("#totalBillList").html(totalBillList);
								}
							}
							if(typeof data.latestMonthBill != 'undefined') {
								$("#latestMonthBill").val((typeof data.latestMonthBill[0] != 'undefined')? data.latestMonthBill[0] : '');
								if(data.latestMonthBill.length > 1) {
									var latestMonthBillList = '';
									data.latestMonthBill.forEach(function(el){
										latestMonthBillList += '<option value="'+el+'">';
									})
									$("#latestMonthBillList").html(latestMonthBillList);
								}
							}
							if(typeof data.pastDue != 'undefined') {
								$("#pastDue").val((typeof data.pastDue[0] != 'undefined')? data.pastDue[0] : '');
								if(data.pastDue.length > 1) {
									var pastDueList = '';
									data.pastDue.forEach(function(el){
										pastDueList += '<option value="'+el+'">';
									})
									$("#pastDueList").html(pastDueList);
								}
							}
							if(typeof data.usedData != 'undefined') {
								$("#usedData").val((typeof data.usedData[0] != 'undefined')? data.usedData[0] : '');
								if(data.usedData.length > 1) {
									var usedDataList = '';
									data.usedData.forEach(function(el){
										usedDataList += '<option value="'+el+'">';
									})
									$("#usedDataList").html(usedDataList);
								}
							}
							if(typeof data.totalPlanData != 'undefined') {
								$("#totalPlanData").val((typeof data.totalPlanData[0] != 'undefined')? data.totalPlanData[0] : '');
								if(data.totalPlanData.length > 1) {
									var totalPlanDataList = '';
									data.totalPlanData.forEach(function(el){
										totalPlanDataList += '<option value="'+el+'">';
									})
									$("#totalPlanDataList").html(totalPlanDataList);
								}
							}
							if(typeof data.deviceBalance != 'undefined') {
								$("#deviceBalance").val((typeof data.deviceBalance[0] != 'undefined')? data.deviceBalance[0] : '');
								if(data.deviceBalance.length > 1) {
									var deviceBalanceList = '';
									data.deviceBalance.forEach(function(el){
										deviceBalanceList += '<option value="'+el+'">';
									})
									$("#deviceBalanceList").html(deviceBalanceList);
								}
							}
							if(typeof data.deviceOwned != 'undefined') {
								$("#deviceOwned").val((typeof data.deviceOwned[0] != 'undefined')? data.deviceOwned[0] : '');
								// if(data.deviceOwned.length > 1) {
								// 	var deviceOwnedList = '';
								// 	data.deviceOwned.forEach(function(el){
								// 		deviceOwnedList += '<option value="'+el+'">';
								// 	})
								// 	$("#deviceOwnedList").html(deviceOwnedList);
								// }
							}
							if(typeof data.totalPlanCharges != 'undefined') {
								$("#totalPlanCharges").val(data.totalPlanCharges);
							}
							$('.currencyFormat').blur();
						}else{
							$('#bill_err').html(response.data.error.bill).removeClass('hide');

							ths.find('button[type=submit]').removeAttr('disabled');
							ths.find('button[type=submit]').find('.btn-element').removeClass('hide');
							ths.find('button[type=submit]').find('.loader').addClass('hide');
						}
					}
				});
			}
			return false;
		}

		function wirelessButlerForm1Step2Submit(e) {
			var ths = $(this);
			e.preventDefault();
			
			var err = false;
			$(this).find('.err').addClass('hide');
			var totalBill = $(this).find('input[id=totalBill]').val().replaceAll(',', '').replaceAll('$', '');
			var latestMonthBill = $(this).find('input[id=latestMonthBill]').val();
			var pastDue = $(this).find('input[id=pastDue]').val();
			var totalPlanCharges = $(this).find('input[id=totalPlanCharges]').val().replaceAll(',', '').replaceAll('$', '');
			var usedData = $(this).find('input[id=usedData]').val();
			var totalPlanData = $(this).find('input[id=totalPlanData]').val();
			var deviceBalance = $(this).find('input[id=deviceBalance]').val().replaceAll(',', '').replaceAll('$', '');
			var deviceOwned = $(this).find('input[id=deviceOwned]').val();

			if(totalBill != '' && !isInt(totalBill) && !isFloat(totalBill)) {
				err = true
				$('#totalBill_err').html('Please enter valid value').removeClass('hide');
			}
			if(latestMonthBill != '' && !isInt(latestMonthBill) && !isFloat(latestMonthBill)) {
				err = true
				$('#latestMonthBill_err').html('Please enter valid value').removeClass('hide');
			}
			if(pastDue != '' && !isInt(pastDue) && !isFloat(pastDue)) {
				err = true
				$('#pastDue_err').html('Please enter valid value').removeClass('hide');
			}
			if(totalPlanCharges != '' && !isInt(totalPlanCharges) && !isFloat(totalPlanCharges)) {
				err = true
				$('#totalPlanCharges_err').html('Please enter valid value').removeClass('hide');
			}
			if(usedData != '' && !isInt(usedData) && !isFloat(usedData)) {
				err = true
				$('#usedData_err').html('Please enter valid value').removeClass('hide');
			}
			if(totalPlanData != '' && !isInt(totalPlanData) && !isFloat(totalPlanData)) {
				err = true
				$('#totalPlanData_err').html('Please enter valid value').removeClass('hide');
			}

			if(!err) {
				var formData = new FormData();
				formData.append("rowID", $(this).find('input[id=rowID]').val());
				formData.append("totalBill", totalBill);
				formData.append("latestMonthBill", latestMonthBill);
				formData.append("pastDue", pastDue);
				formData.append("totalPlanCharges", totalPlanCharges);
				formData.append("usedData", usedData);
				formData.append("totalPlanData", totalPlanData);
				formData.append("deviceBalance", deviceBalance);
				formData.append("deviceOwned", deviceOwned);
				formData.append("action", 'wireless_butler_form_1_step_2');
	
				ths.find('button[type=submit]').attr('disabled', true);
				ths.find('button[type=submit]').find('.btn-element').addClass('hide');
				ths.find('button[type=submit]').find('.loader').removeClass('hide');
	
				$.ajax({
					action:  		'wireless_butler_form_1_step_2',
					type:    		"POST",
					url:     		wirelessButlerObj.ajaxurl,
					data:    		formData,
					cache: 			false,
					processData: 	false,
					contentType: 	false,
					dataType: 		'json',
					success: function(res) {
						// $(".wireless_butler_form_1_step_2").addClass('hide');
						$('.wireless_butler_form_1_step_2_message').removeClass('hide');
						$('#suggestionText').html(res.suggestionText);
						$('#suggestionText').removeClass('hide');
	
						ths.find('button[type=submit]').removeAttr('disabled');
						ths.find('button[type=submit]').find('.btn-element').removeClass('hide');
						ths.find('button[type=submit]').find('.loader').addClass('hide');
	
					}
				});
			}
			return false;
		}

		$('.currencyFormat').blur(function(){
			var value = $(this).val();
			if(value != '') {
				var num = parseFloat(value);
				$(this).val('$'+num.format(2, 3, ',', '.'));
			}
		}).blur();

		$('.currencyFormat').focus(function(){
			var num = $(this).val().replaceAll(',', '').replaceAll('$', '');
			$(this).val(num);
		})
	});

})( jQuery );
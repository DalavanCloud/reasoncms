// Javascript code to for the giving form 
//
// @author Mark Heiman

$(document).ready(function() {
	/** PageOne **/
	if ($("div#giftForm.pageOne").length)
	{
		toggle_recur_fields();
		toggle_split_option();
		toggle_split_designation();
		$("input#gift_amountElement").keyup(function(){toggle_split_option()});
		$("input#checkbox_split_gift").change(function(){toggle_split_designation()});
		$("#gift_designation_container").keyup(function(){total_split_gifts()});
		
		
		$("input[type='checkbox']").not("#checkbox_specific_fund").not("#checkbox_match_gift").change(function(){ show_amount_fields(); });
		
		$("input[name='installment_type']").change(function(){ toggle_recur_fields(); });

		$(".inlineElement").find("input[id*='amountElement']").blur(function(){ add_amounts(); });

		// Set the initial state for employer name field
		$("#employernameItem").hide(500);
		
		// Show/hide employer name based on match status
		$("input#checkbox_match_gift").change(function(){
				if ($("input#checkbox_match_gift:checked").val())
				$("#employernameItem").show(500);
			else
				$("#employernameItem").hide(500);
		});
				
		//$("input#checkbox_match_gift").change();
		$("input#checkbox_annual_fund").change();
				
		toggle_gift_prompt();
		$("#gift_promptElement").change(function(){ toggle_gift_prompt(); });

		// add a span class around the money elements's "$"
		$("#gift_amountElement").parent().contents().filter(function() {
			return this.nodeType === 3 }).wrap('<span class="currency"></span>');
		$(".inlineElement input[name$='_amount']").parent().contents().filter(function() {
			return this.nodeType === 3 }).wrap('<span class="currency"></span>');
		$("#checkbox_annual_fund").parent().contents().filter(function() {
			return this.nodeType === 3 }).last().wrap('<span class="comment">&nbsp;</span>');
		$("#checkbox_scholarship_fund").parent().contents().filter(function() {
			return this.nodeType === 3 }).last().wrap('<span class="comment">&nbsp;</span>');
		$(".inlineElement input[name$='_amount']").attr('placeholder', 'Amount');
		$("#other_designation_detailsElement").attr('placeholder', 'Other Designation');
	}
	
	/** PageTwo **/
	if ($("div#giftForm.pageTwo").length) {
		set_name_field_prompt();
		$("input[name$='_name']").focus(function(){ clear_name_field_prompt($(this)); });
		$("input[name$='_name']").blur(function(){ set_name_field_prompt(); });
		$("form#disco_form").submit(function(){
			clear_name_field_prompt($("input[name='first_name']"));
			clear_name_field_prompt($("input[name='last_name']"));	
		});
		
		
		// Show class year when alum affiliation chosen
		toggle_class_year();
			
		$("input#checkbox_luther_affiliation_0").change(function(){ toggle_class_year(); });
		$("input#checkbox_luther_affiliation_3").change(function(){ toggle_class_year(); });
		

		// Show/hide and populate Country field based on state/province choice
		$("select#state_provinceElement").change(function(){ toggle_country_field("select#state_provinceElement","#countryItem" ); });
		
		// Set the initial state for the Country field
		$("select#state_provinceElement").change();
		$("#countryItem").hide(500);
	}

	/** PageThree **/
	if ($("div#giftForm.pageThree").length) {
		// Add the controls to open and close the gift detail.
		if ($("div#giftForm h3#yearlyTotalsHeading").length)
		{
			$("div#giftForm div#reviewGiftDetails").hide(500);
			
			$("div#giftForm div#reviewGiftOverview").append('<p><a id="showGiftDetails" href="#">Yearly totals for this gift</a></p>');
			$("div#giftForm #reviewGiftDetails").append('<a id="hideGiftDetails" href="#"><i class="fa fa-times"></i></a>');

			$("a#showGiftDetails").click(function(event){
				$("a#showGiftDetails").hide(500);
				$("div#reviewGiftDetails").show(500);
				event.preventDefault();
			});
		
			$("a#hideGiftDetails").click(function(event){
				$("a#showGiftDetails").show(500);
				$("div#reviewGiftDetails").hide(500);
				event.preventDefault();
			});
		}
		
		toggle_billing_address();
		
		$("input[name='billing_address']").change(function(){ toggle_billing_address(); });

		// Show/hide and populate Country field based on state/province choice
		$("select#billing_state_provinceElement").change(function(){ toggle_country_field("select#billing_state_provinceElement","#billingcountryItem"); });
		
		// Set the initial state for the Country field
		$("select#billing_state_provinceElement").change();
	}

	/** PageFour **/
	$("p.printConfirm").html("<input type='submit' value='"+ $("p.printConfirm").html() + "' />");
	$("p.printConfirm input").click(function(event){
		window.print();
		event.preventDefault();
	});
});

function set_name_field_prompt()
{
	if ($("input[name='first_name']").val() == '')
	{
		$("input[name='first_name']").addClass("unfocused_label").val('First');		
	}
	if ($("input[name='last_name']").val() == '')
	{
		$("input[name='last_name']").addClass("unfocused_label").val('Last');		
	}
	
	if ($("input[name='spouse_first_name']").val() == '')
	{
		$("input[name='spouse_first_name']").addClass("unfocused_label").val('First');		
	}
	if ($("input[name='spouse_last_name']").val() == '')
	{
		$("input[name='spouse_last_name']").addClass("unfocused_label").val('Last');		
	}

}

function clear_name_field_prompt(field)
{
	if (field.val() == 'First' || field.val() == 'Last')
	{
		field.removeClass("unfocused_label").val('');		
	}
}

function toggle_class_year()
{
	if ($("input#checkbox_luther_affiliation_0:checked").val() ||
	    $("input#checkbox_luther_affiliation_3:checked").val())
                $("#classyearItem").show(500);
        else
                $("#classyearItem").hide(500);
}


function toggle_country_field(stateElementSelector, countryItemSelector)
{
	// Show/hide and populate Country field based on state/province choice
	// If not US or Canada, show the Country field
	if ($(stateElementSelector).val() == "XX")
	{
   	    $(countryItemSelector + " select").val('');
    	$("#countryItem").show(500);
   		$("#billingcountryItem").show(500);
	}
	// If US or Canada, populate Country but hide it
	else
	{
	    //$(countryItemSelector).hide(500);
	    // If a Canadian province...
	    if (/^(?:AB|BC|MB|NB|NL|NT|NS|NU|ON|PE|QC|SK|YT)$/.test($(stateElementSelector).val())) 
		$(countryItemSelector + " select").val("CAN");
	    // If anything else (other than unset)
	    else if ($(stateElementSelector).val() != "")
		$(countryItemSelector + " select").val('USA');
	}
}

function toggle_recur_fields()
{
	if (!$("input[name='installment_type']:checked").val() ||
	     $("input[name='installment_type']:checked").val() == 'Onetime')
	{
		$("#recurgroupItem").hide(500);
	} else {
		$("#recurgroupItem").show(500);
	}
}

function toggle_recur_fields_old()
{
	if (!$("input[name='installment_type']:checked").val() ||
	     $("input[name='installment_type']:checked").val() == 'Onetime')
	{
		$("#installmentstartdateItem").hide(500);	
		$("#installmentenddateItem").hide(500);	
	} else {
		$("#installmentstartdateItem").show(500);	
		$("#installmentenddateItem").show(500);	
	}
}

function toggle_billing_address()
{
	if (!$("input[name='billing_address']:checked").val() ||
	     $("input[name='billing_address']:checked").val() == 'entered')
	{
		$("#billingstreetaddressItem").hide(500);	
		$("#billingcityItem").hide(500);	
		$("#billingstateprovinceItem").hide(500);	
		$("#billingzipItem").hide(500);	
		$("#billingcountryItem").hide(500);	
	} else {
		$("#billingstreetaddressItem").show(500);	
		$("#billingcityItem").show(500);	
		$("#billingstateprovinceItem").show(500);	
		$("#billingzipItem").show(500);	
		$("#billingcountryItem").show(500);
		$("select#billing_state_provinceElement").change();
	}
}

function toggle_gift_prompt() {
	if ($("#gift_promptElement").val() == 'staff_visit') {
		$("#giftpromptdetailsItem").show(500);
		$("#gift_prompt_detailsElement").parent().prev().html('Name of development officer');
	}
	else if ($("#gift_promptElement").val() == 'other') {
		$("#giftpromptdetailsItem").show(500);
		$("#gift_prompt_detailsElement").parent().prev().html('Please tell us more');
	} else {
		$("#giftpromptdetailsItem").hide(500);
		$("#gift_prompt_detailsElement").val('');
	}
}


function show_amount_fields() {
	$(".inlineElement").find("input[id*='amountElement']").parent().hide();
	// if more than one designation is checked,
	// show designation amount boxes for those checked.
	// Look for all checkboxes ignore matching gifts and specific funds checkboxes
	var checked_boxes = $("input[type='checkbox']:checked").not("#checkbox_specific_fund").not("#checkbox_match_gift");
	checked_boxes.each(function(){
		if ( checked_boxes.length > 1 ) {
			amount_selector = $("#"+$(this).prop('name')+"_amountElement");
			$(amount_selector).parent().show();
			// clear any existing _amountElement values
			$(".inlineElement").find("input[id*='amountElement']").val('');
			// get the first open *_amountElement and fill it with the init ial gift amount
			$(".inlineElement").find("input[id*='amountElement']:visible").first().val($('#gift_amountElement').val());
			$(".inlineElement").find("input[id*='amountElement']:visible").first().effect('highlight');
		} else {
			amount_selector = $("#"+$(this).prop('name')+"_amountElement");
			$(amount_selector).parent().hide();
			// clear all _amountElement values
			$(".inlineElement").find("input[id*='amountElement']").val('');
		}
	});
}

function add_amounts() {
	var initial_gift_amount = $("#gift_amountElement").val();
	var total = 0;	
}

/* If the split gift option is hidden, show it if the total gift amount
is $50 or more. */
function toggle_split_option()
{
	if ($("input#checkbox_split_gift").prop('checked')) return;

	var amount = $('input#gift_amountElement').val().match(/[\d\.,]+/);
	$('div#splitgiftItem').toggle(amount && Number(amount[0].replace(',','')) >= 50);
}

/* Rebuild the designation element as an interface for gift splitting */
function toggle_split_designation()
{	
	if ($("input#checkbox_split_gift").prop('checked')) 
	{
		// Read the split_designations element and convert any data there into an object
		var designations = {};
		if ($("input#split_designationsElement").val())
		{
			designations = JSON.parse($("input#split_designationsElement").val())
		}
	
		// Add a class to indicate our state
		$('#gift_designation_container').addClass('split');
	
		// Hide the designation radio buttons
		$('#gift_designation_container span.radioButton').hide();

		// Loop through each of the designations and add a text field
		$('#gift_designation_container div.radioItem').each(function(){
			$(this).prepend('<span class="splitText">$ <input type="text" class="splitAmount" name="gift_designation_split[]" /></span>');
			
			// If this option was selected already, prefill with the gift amount
			if ($('span.radioButton input', $(this)).prop('checked'))
			{
				$('input.splitAmount', $(this)).val($('input#gift_amountElement').val());
			}
			
			// If there's a value in the designations object for this field, set that
			if (designations[$('span.radioButton input', $(this)).prop('value')])
			{
				$('input.splitAmount', $(this)).val(designations[$('span.radioButton input', $(this)).prop('value')]);
			}
		});
		
		// Move the gift total below the designations
		$("div#giftdesignationItem").after($("div#giftamountItem"));
		
		$("div#giftamountItem div.words").html('Gift Total:');
	}
	else
	// Undo the splitting interface
	{
		// Remove the class to indicate our state
		$('#gift_designation_container').removeClass('split');

		// Show the designation radio buttons
		$('#gift_designation_container span.radioButton').show();

		// Remove the text boxes
		$('#gift_designation_container span.splitText').remove();
	
		// Move the gift total back to the top
		$("div#giftamountheaderItem").after($("div#giftamountItem"));
		
		$("div#giftamountItem div.words").html('');
		
		// Clear the designation description element
		$("input#split_designationsElement").val('');
	}
}

/* Add up the split gift amounts and put them in the gift_amount element,
	and put a JSON representation of the gift amounts into the split_designations
	element. */
function total_split_gifts()
{
	if ($('#gift_designation_container').hasClass('split'))
	{
		var total = 0;
		var designations = {};
		
		$('#gift_designation_container div.radioItem').each(function(){
			var amount = $('input.splitAmount', $(this)).val().match(/[\d\.,]+/);
			if (amount)
			{
				total = total + Number(amount[0].replace(',',''));
				designations[$('input[type="radio"]', $(this)).prop('value')] = amount;
			}
		});
	
		$("input#gift_amountElement").val(total);
		$("input#split_designationsElement").val(JSON.stringify(designations));
	}
}

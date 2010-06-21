<?php
////////////////////////////////////////////////////////////////////////////////
//
//    Steve Smith
//    2010-06-15 (very early in the morning)
//
//    Work on the second page of the homecoming registration form
//
////////////////////////////////////////////////////////////////////////////////
#####test
##################include_once(WEB_PATH.'stock/giftclass.php');
include_once(WEB_PATH.'stock/homecomingPFclass.php');
################## reason_include_once( 'classes/repeat_transaction_helper.php' );

class HomecomingRegistrationTwoForm extends FormStep
{
	var $_log_errors = true;
	var $no_session = array( 'credit_card_number' );
	var $error;
	var $budget_number = '10-0000-0000-1307';
	var $expense_budget_number = '10-202-60201-51331';
	var $revenue_budget_number = '10-000-60201-44906-UHOMP';
	var $transaction_comment = 'Homecoming Registration';
	var $is_in_testing_mode; // This gets set using the value of the THIS_IS_A_DEVELOPMENT_REASON_INSTANCE constant or if the 'tm' (testing mode) request variable evaluates to an integer
	
	// the usual disco member data
	var $elements = array(
		'review_note' => array(
			'type' => 'comment',
			'text' => 'Homecoming overview',
		),
		'payment_note' => array(
			'type' => 'comment',
			'text' => '<h3>Payment Method</h3>',
		),
		'payment_amount' => array(
			'type' => 'solidtext',
		),
		'credit_card_type' => array(
			'type' => 'radio_no_sort',
			'options' => array('Visa'=>'Visa','MasterCard'=>'MasterCard','American Express'=>'American Express','Discover'=>'Discover'),
		),
		'credit_card_number' => array(
			'type' => 'text',
			'size'=>35,
		),
		'credit_card_expiration_month' => array(
			'type' => 'month',
			'display_name' => 'Expiration Month',
		),
		'credit_card_expiration_year' => array(
			'type' => 'numrange',
			'start' => 2007,
			'end' => 2022,
			'display_name' => 'Expiration Year',
		),
		'credit_card_name' => array(
			'type' => 'text',
			'display_name' => 'Name as it appears on card',
			'size'=>35,
		),
		'billing_address' => array(
			'type' => 'radio_no_sort',
			'options' => array('entered'=>'Use address provided on previous page','new'=>'Use a different address'),
			'display_name' => 'Billing Address',
			'default' => 'entered',
		),
		'billing_street_address' => array(
			'type' => 'textarea',
			'rows' => 3,
			'cols' => 35,
			'display_name' => 'Street Address',
		),
		'billing_city' => array(
			'type' => 'text',
			'size'=>35,
			'display_name' => 'City',
		),
		'billing_state_province' => array(
			'type' => 'state_province',
			'display_name' => 'State/Province',
			'include_military_codes' => true,
		),
		'billing_zip' => array(
			'type' => 'text',
			'display_name' => 'Zip/Postal Code',
			'size'=>35,
		),
		'billing_country' => array(
			'type' => 'country',
//			'size'=>35,
			'display_name' => 'Country',
		),
		'confirmation_text' => array(
			'type' => 'hidden',
		),
		'result_refnum' => array(
			'type' => 'hidden',
		),
		'result_authcode' => array(
			'type' => 'hidden',
		),
	);
	var $required = array(
		'credit_card_type',
		'credit_card_number',
		'credit_card_expiration_month',
		'credit_card_expiration_year',
		'credit_card_name',
		'billing_address',
	);
	var $actions = array(
		'previous_step'=>'Make Changes To Your Weekend',
		'next_step'=>'Submit Your Gift For Processing',
	);
#########	var $helper;
	var $date_format = 'j F Y';
	var $display_name = 'Homecoming Review / Card Info';
	var $error_header_text = 'Please check your form.';
	var $database_transformations = array(
											'credit_card_number'=>'obscure_credit_card_number',
										);
	// style up the form and add comments et al
	function on_every_time()
	{
		if( !$this->controller->get('amount'))
		{
			echo '<div id="giftFormSetupError">You can\'t complete this step without having set up a gift; please go back to <a href="?_step=GiftPageOneForm">Gift Info</a> and provide a gift amount.</div>';
			$this->show_form = false;
			return;
		}

		$this->set_value('payment_amount', '$'.number_format($this->controller->get('amount'),2,'.',','));
/*################
		if($this->controller->get('installment_type') == 'Onetime')
		{
			$this->remove_element('installment_notification_note_1');
			$this->remove_element('installment_notification_note_2');
			$this->remove_element('installment_notification');
		}
*/
		if(THIS_IS_A_DEVELOPMENT_REASON_INSTANCE || !empty( $this->_request[ 'tm' ] ) )
		{
			$this->is_in_testing_mode = true;
		}
		else
		{
			$this->is_in_testing_mode = true;
		}
		
		$this->change_element_type('credit_card_expiration_year','numrange',array('start'=>date('Y'),'end'=>(date('Y')+15),'display_name' => 'Expiration Year'));
	}
	
	function post_error_check_actions()
	{
		if ($this->show_form)
		{
####################			$this->instantiate_helper();
			$text = $this->get_brief_review_text();
/*##################
			if(!$this->_has_errors() && $this->controller->get('installment_type') != 'Onetime')
			{
				$text .= build_gift_review_detail_output( $this->helper, $this->date_format );
			}
*/
			$text .= '<p class="changeRegistrationButton"><a href="?_step=HomecomingRegistrationOneForm">Change Registration Information</a></p>'."\n";
			$this->change_element_type( 'review_note', 'comment', array('text'=>$text) );

		}
	}
	
	function pre_show_form()
	{
		echo '<div id="homecomingForm" class="pageTwo">'."\n";
		if( $this->is_in_testing_mode )
		{
			echo '<div class="announcement">';
			echo 'Testing mode on. '."\n";
			echo 'Credit cards will not be charged in this mode.'."\n";
			echo '</div>'."\n";
		}
	}
	function post_show_form()
	{
		echo '</div>'."\n";
	}
	function get_brief_review_text()
	{
		$txt = '<div id="reviewOverview">'."\n";
/*##################
if ($this->controller->get('installment_type') == 'Onetime')
		{
			$txt .= '<p>You have indicated that you would like to make a one time gift of $'.number_format( $this->controller->get('gift_amount'), 2, '.', ',' ).'</p>'."\n";
		}
		else
		{
			$txt .= '<p>You have indicated that you would like to make a recurring gift of $'.number_format( $this->controller->get('gift_amount'), 2, '.', ',' ).' per '.$this->installment_type_to_word[$this->controller->get('installment_type')].', starting on '.prettify_mysql_datetime($this->controller->get('installment_start_date'), $this->date_format);
			if($this->controller->get('installment_end_date') != 'indefinite' )
			{
				$txt .= ' and ending on '.date($this->date_format, $this->helper->get_last_repeat_timestamp());
			} else {
				$txt .= ' with no designated end date';	
			}
			$txt .= '.</p>'."\n";
		}
*/
		$txt .= '</div>'."\n";
		return $txt;
	}
	
	function get_confirmation_text()
	{
		$txt = '<div id="reviewHomecomingRegistration">'."\n";			
		$txt .= '<p class="printConfirm">Print this confirmation for your records.</p>'."\n";
		$txt .= '<ul>'."\n";
		$txt .= '<li><strong>Date:</strong> '.date($this->date_format).'</li>'."\n";
		$txt .= '<li><strong>Name:</strong> '.$this->controller->get('current_first_name').' '.$this->controller->get('current_last_name').'</li>'."\n";
		$txt .= '<li><strong>Class Year:</strong> '.$this->controller->get('class_year').'</li>'."\n";
		$txt .= '<li><strong>Graduation Name:</strong> '.$this->controller->get('graduation_name').'</li>'."\n";
		if ($this->controller->get('preferred_first_name'))
		{
			$txt .= '<li><strong>Preferred First Name:</strong> '.$this->controller->get('preferred_first_name').'</li>'."\n";
		}
		$txt .= '<li><strong>Address:</strong>'."\n".$this->controller->get('address')."\n".$this->controller->get('city').' '.$this->controller->get('state_province').' '.$this->controller->get('zip')./*$this->controller->get('country').*/'</li>'."\n";
		if ($this->controller->get('home_phone'))
		{
			$txt .= '<li><strong>Home Phone:</strong> '.$this->controller->get('home_phone').'</li>'."\n";
		}
		if ($this->controller->get('cell_phone'))
		{
			$txt .= '<li><strong>Cell Phone:</strong> '.$this->controller->get('cell_phone').'</li>'."\n";
		}
		$txt .= '<li><strong>Email:</strong> '.$this->controller->get('e-mail').'</li>'."\n";
		if ($this->controller->get('guest_name'))
		{
			$txt .= '<li><strong>Spouse/Guest Name:</strong> '.$this->controller->get('guest_name').'</li>'."\n";
		}
		if($this->controller->get('attended_luther'))
		{
			$txt .= '<li><strong>Guest Class Year:</strong> '.$this->controller->get('attended_luther').'</li>'."\n";
		}		
		if($this->controller->get('attend_program') )
		{
			$txt .= '<li><strong>Tickets for Alumni Program:</strong> '.($this->controller->get('attend_program')).'</li>'."\n";
		}
		if($this->controller->get('attend_luncheon'))
		{
			$txt .= '<li><strong>Attend 75-50 Year Reunion Luncheon :</strong> '.$this->controller->get('attend_luncheon').'</li>'."\n";
		}
		if($this->controller->get('attend_dinner_50_to_25'))
		{
			$txt .= '<li><strong>Attend 50-25 Year Reunion Dinner:</strong> '.$this->controller->get('attend_dinner_50_to_25').'</li>'."\n";
		}
		if($this->controller->get('attend_dinner_20_to_10'))
		{
			$txt .= '<li><strong>Attend 20-10 Year Reunion Reception:</strong> '.$this->controller->get('attend_dinner_20_to_10').'</li>'."\n";
		}
		if($this->controller->get('attend_dinner_5'))
		{
			$txt .= '<li><strong>Attend 5 Year Reunion Reception:</strong> '.$this->controller->get('attend_dinner_5').'</li>'."\n";
		}
		$txt .= '</ul>'."\n";
		$txt .= '</div>'."\n";
		$this->set_value('confirmation_text', $txt); 
		return $txt;
	}
	

	function run_error_checks()
	{
		if($this->get_value('billing_address') == 'new' && (!$this->get_value('billing_street_address') || !$this->get_value('billing_city') || !$this->get_value('billing_state_province') || !$this->get_value('billing_zip') || !$this->get_value('billing_country') ) )
		{
			$this->set_error('billing_address','Please enter your full billing address if the address you entered on the previous page was not the billing address for your credit card.');
		}

		
		// Process credit card
		if( !$this->_has_errors() )
		{
			$pf = new homecomingPF;
/*#####################
if( $this->controller->get('installment_type') == 'Onetime')
			{
				$immediate_amount = $this->controller->get('gift_amount');
			}
			else
			{
				$immediate_amount = 0;
			}
*/
			$expiration_mm = str_pad($this->get_value('credit_card_expiration_month'), 2, '0', STR_PAD_LEFT);
			$expiration_yy = substr($this->get_value('credit_card_expiration_year'), 2, 2);
			$expiration_mmyy = $expiration_mm.$expiration_yy;
			
			foreach($this->controller->get_element_names() as $element_name)
			{
				if($this->controller->get($element_name))
				{
					if(empty($this->database_transformations[$element_name]))
					{
						$pass_info[$element_name] = $this->controller->get($element_name);
					}
					else
					{
						$pass_info[$element_name] = $this->database_transformations[$element_name]($this->controller->get($element_name));
					}
				}
			}
			foreach($this->elements as $element_name => $vals)
			{
				if($this->get_value($element_name))
				{
					if(empty($this->database_transformations[$element_name]))
					{
						$pass_info[$element_name] = $this->get_value($element_name);
					}
					else
					{
						$pass_info[$element_name] = $this->database_transformations[$element_name]($this->get_value($element_name));
					}
				}
			}
			
			$pf->set_params( $pass_info );
			
			$pf->set_info(
				$this->controller->get('amount'),
				$this->get_value('credit_card_number'),
				$expiration_mmyy,
				//$this->budget_number,
				$this->revenue_budget_number,
//				$this->income_budget_number,
				$this->get_value('credit_card_name'),
				$this->expense_budget_number,
				$this->transaction_comment
			);
			
#############			$this->instantiate_helper();
#############			$this->helper->build_transactions_array();
			
/*###################
			if($this->controller->get('installment_type') != 'Onetime')
			{
				// A value of zero for $installment_quantity indicates no end date.
				if($this->controller->get('installment_end_date') == 'indefinite')
				{
					$installment_quantity = 0;
				}
				else
				{
					$installment_quantity = $this->helper->get_repeat_quantity();
				}
				
				// PayPeriod is one of WEEK, BIWK, SMMO, FRWK, MONT, QTER, SMYR, QTER.
				$repeat_types = array('Monthly'=>'MONT','Quarterly'=>'QTER','Yearly'=>'YEAR');
				$pf_repeat_type = $repeat_types[$this->controller->get('installment_type')];
				if($this->get_value('installment_notification') == 'yes')
				{
					$email = $this->controller->get('email');
				}
				else
				{
					$email = '';
				}
				$pf->set_recur(
					$this->controller->get('gift_amount'),
					date('mdY',strtotime($this->controller->get('installment_start_date'))),
					$installment_quantity,
					$pf_repeat_type,
					$email
				);
			}
*/
			/* THIS IS WHERE THE TRANSACTION TAKES PLACE */
			// Test mode: $result = $pf->transact('test');
			// Live mode: $result = $pf->transact();
			if($this->is_in_testing_mode)
			{
				$result = $pf->transact('test');
			}
			else
			{
				$result = $pf->transact();
			}
			 if (!$pf->approved)
			 {
				$message = $pf->message;
				$this->set_error('credit_card_number',$message);
			}
			else
			{
				// It's important that these things happen before we build the confirmation text, since they are needed by that code.
				if(!empty($result['REFNUM']))
				{
					$this->set_value( 'result_refnum', $result['REFNUM'] );
				}
				else
				{
					trigger_error( 'No Reference Number (REFNUM) in transaction result.' );
				}
				$this->set_value( 'result_authcode', $result['AUTHCODE'] );
				
				$confirm_text = $this->get_confirmation_text();
###########			$confirm_text .= build_gift_review_detail_output( $this->helper, $this->date_format );
				
				$this->set_value( 'confirmation_text', $confirm_text );
				$pf->set_confirmation_text( $confirm_text );
				
				// This is where we send the confirmation email.
				// for now we are filtering out obviously bad/non-carleton email addresses
				// NOTE: REMOVE THIS FILTER BEFORE WE GO LIVE
				//if(strstr( $this->controller->get('email'), 'carleton.edu' ) )
				//{
				$replacements = array(
										'<th class="col1">Date</th>'=>'',
										'<th class="col1">Year</th>'=>'',
										'<th>Amount</th>'=>'',
										'</td><td>'=>': ',
										'�'=>'-',
										'<h3>'=>'--------------------'."\n\n",
										'</h3>'=>'',
										'<br />'=>"\n",
									);
				if (reason_unique_name_exists('homecoming_thank_you_blurb'))
					$confirm_text_with_blurb = get_text_blurb_content('homecoming_thank_you_blurb') . $confirm_text;
				else
					$confirm_text_with_blurb = '<p><strong>Thank you for registering for Homecoming!</strong></p>' . $confirm_text;
				
				//}
				$mail_text = str_replace(array_keys($replacements),$replacements,$confirm_text);
				$mail = new Email($this->controller->get('e-mail'),'alumni@luther.edu','alumni@luther.edu','Luther College Homecoming Registration Confirmation',strip_tags($confirm_text_with_blurb),$confirm_text_with_blurb);
				$mail->send();
				
				$mail2 = new Email('smitst01@luther.edu', 'noreply@luther.edu','noreply@luther.edu', 'New Homecoming Registration '.date('mdY H:i:s'),strip_tags($mail_text), $mail_text);
				$mail2->send();
			}
		}
	}
	function where_to()
	{
		$refnum = $this->get_value( 'result_refnum' );
		$text = $this->get_value( 'confirmation_text' );
		reason_include_once( 'minisite_templates/modules/homecoming_registration/homecoming_confirmation.php' );
		$gc = new HomecomingConfirmation;
		$hash = $gc->make_hash( $text );
		connectDB( REASON_DB );
		$url = get_current_url();
		$parts = parse_url( $url );
		$url = $parts['scheme'].'://'.$parts['host'].$parts['path'].'?r='.$refnum.'&h='.$hash;
		return $url;
	}
}

/*######################
function build_gift_transaction_helper( $params_array )
{
	$helper = new repeatTransactionHelper();
		
	if($params_array['installment_type'] == 'Onetime')
	{
		$helper->set_single_time_amount( $params_array['gift_amount'] );
		$helper->set_single_time_date( date('Y-m-d') );
	} else {
		$helper->set_repeat_amount( $params_array['gift_amount'] );
		$helper->set_repeat_type( $params_array['installment_type'] );
		$helper->set_repeat_start_date( $params_array['installment_start_date'] );
		$helper->set_end_date( $params_array['installment_end_date'] );
	}
	return $helper;
}

function build_gift_review_detail_output($helper, $date_format = 'j F Y')
{
		// A yearly totals disclose link is inserted here by JS
		$txt = '<div id="reviewGiftDetails">'."\n";
		$txt .= '<h3 id="yearlyTotalsHeading">Yearly Totals for This Gift</h3>'."\n\n";
		$txt .= '<h4>Per calendar year:</h4>';
		$txt .= '<table cellpadding="0" cellspacing="0" border="0" summary="Total amounts given, by calendar year">'."\n";
		$txt .= '<tr><th class="col1">Year</th><th>Amount</th></tr>'."\n";
		$cy_gifts = $helper->get_calendar_year_totals();
		if( $helper->repeats_indefinitely() )
		{
			$break = false;
			$previous_amount_text = '';
			$i = 0;
			foreach($cy_gifts as $year=>$amount)
			{
				$amount_text = number_format( $amount*.01, 2, '.', ',' );
				if( empty($previous_amount_text) || $previous_amount_text != $amount_text )
				{
					$year_text = $year;
					$amount_post = '';
				}
				else
				{
					$year_text = 'Subsequently';
					$amount_post = ' per calendar year';
					$break = true;
				}
				$txt .= '<tr><td class="col1">'.$year_text.'</td><td>$'.$amount_text.$amount_post.'</td></tr>'."\n";
				if($break || $i > 500 ) // second part is to avoid any possibility of an infinite loop
				{
					break;
				}
				$previous_amount_text = $amount_text;
				$i++;
			}
		}
		else // definite gift
		{
			foreach($cy_gifts as $year=>$amount)
			{
				$txt .= '<tr><td class="col1">'.$year.'</td><td>$'. number_format( $amount*.01, 2, '.', ',' ) .'</td></tr>'."\n";
			}
		}
		$txt .= '</table>'."\n";
		$txt .= '<h4>Per Carleton fiscal year:</h4>';
		$txt .= '<table cellpadding="0" cellspacing="0" border="0" summary="Total amounts given, by Carleton fiscal year">'."\n";
		$txt .= '<tr><th class="col1">Year</th><th>Amount</th></tr>'."\n";
		$fy_gifts = $helper->get_fiscal_year_totals();
		
		if( $helper->repeats_indefinitely() )
		{
			$break = false;
			$previous_amount_text = '';
			$i = 0;
			foreach($fy_gifts as $start_year=>$amount)
			{
				$amount_text = number_format( $amount*.01, 2, '.', ',' );
				if( empty($previous_amount_text) || $previous_amount_text != $amount_text )
				{
					$year_text = 'July ';
					$year_text .= $start_year;
					$year_text .= ' &#8211; June ';
					$year_text .= $start_year + 1;
					$amount_post = '';
				}
				else
				{
					$year_text = 'Subsequently';
					$amount_post = ' per Carleton fiscal year';
					$break = true;
				}
				$txt .= '<tr><td class="col1">'.$year_text.'</td><td>$'.$amount_text.$amount_post.'</td></tr>'."\n";
				if($break || $i > 500 ) // second part is to avoid any possibility of an infinite loop
				{
					break;
				}
				$previous_amount_text = $amount_text;
				$i++;
			}
		}
		else
		{
			foreach($fy_gifts as $start_year=>$amount)
			{
				$txt .= '<tr><td class="col1">';
				$txt .= 'July ';
				$txt .= $start_year;
				$txt .= ' � June ';
				$txt .= $start_year + 1;
				$txt .= '</td><td>$'. number_format( $amount*.01, 2, '.', ',' ) .'</td></tr>'."\n";
			}
		}
		$txt .= '</table>'."\n";
		$txt .= '<p class="givingTotalsDisclaimer">Amounts listed above reflect <em>this gift only</em>, not your overall giving history. Please contact us for giving history information.</p>';
		$txt .= '</div>'."\n";
		return $txt;
}
*/

function obscure_credit_card_number ( $cc_num )
{
	$char_count = strlen ( $cc_num );
	$obscure_end = $char_count-4;
	$obscured_num = '';
	for($i=0; $i<$obscure_end; $i++)
	{
		$obscured_num .= 'x';
	}
	$obscured_num .= substr( $cc_num, $char_count-4 );
	return $obscured_num;
}
function trim_hours_from_datetime( $datetime )
{
	return substr( $datetime, 0, 10 );
}

?>

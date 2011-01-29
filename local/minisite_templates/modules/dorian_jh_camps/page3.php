<?php
////////////////////////////////////////////////////////////////////////////////
//
//    Matt Ryan
//    2005-02-17
//
//    Work on the second page of the giving form
//
//    Modified for Luther - Steve Smith & Lucas Welper
//    search for SLS to find modifications
//    2011-01-26
//
//
////////////////////////////////////////////////////////////////////////////////

include_once(WEB_PATH.'stock/dorian_jh_camps.php');
include_once(TYR_INC . 'tyr.php');
//reason_include_once( 'classes/repeat_transaction_helper.php' );

class DorianJHCampsThreeForm extends FormStep
{
	var $_log_errors = true;
	var $no_session = array( 'credit_card_number' );
	var $error;
	var $expense_budget_number = '10-202-60500-51111';
        // $expense_budget_number stays the same, per Renee Lillibridge and Karen Dallenbach (1/27/11)
	var $revenue_budget_number = '10-000-08520-22000';
        // $revenue_budget_number  before June 1, 10-000-08520-22000
        // $revenue_budget_number on or after June 1, 10-000-08520-40221

	var $transaction_comment = 'Dorian Camps';
	var $is_in_testing_mode; // This gets set using the value of the THIS_IS_A_DEVELOPMENT_REASON_INSTANCE constant or if the 'tm' (testing mode) request variable evaluates to an integer
	
	// the usual disco member data
	var $elements = array(
		'review_note' => array(
			'type' => 'comment',
			'text' => '<h3>Camp overview</h3>',
                 ),
                'payment_amount' => 'hidden',
		'payment_note' => array(
			'type' => 'comment',
			'text' => '<h3>Payment Method</h3>',
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
	
	var $date_format = 'j F Y';
	var $display_name = 'Camp Review / Card Info';
	var $error_header_text = 'Please check your form.';
	var $database_transformations = array('credit_card_number'=>'obscure_credit_card_number');

	// style up the form and add comments et al
	function on_every_time()
	{
                // determine which revenue_budget_number to use
                // $revenue_budget_number  before June 1, 10-000-08520-22000
                // $revenue_budget_number on or after June 1, 10-000-08520-40221

                $june1 = 151; // June 1 == day 151 (152 on a leap year) on a 0 - 364 scale
                if (date('L')) // if this year is a leap year
                    $june1 = 152;
                $date = getdate();
                if ($date['yday'] >= $june1){
                    $this->revenue_budget_number = '10-000-08520-40221';
                }
                else {
                    $this->revenue_budget_number = '10-000-08520-22000';
                }

                // calculate the total_cost of the camp by adding lesson_cost (if present) to the camp_cost
                $camp_cost = 433;
                $lesson_cost = 0;
                if ($this->controller->get('private_lessons'))
                {
                    $lesson_cost = 35 * $this->controller->get('private_lessons');
                }
                $total_cost = $camp_cost + $lesson_cost;
                $this->change_element_type('payment_amount', 'radio_no_sort', array(
                        'options' => array(
                            '$40' => '$40 - Deposit only',
                            '$' . $total_cost => '$' . $total_cost . ' - Total cost'
                        )
                    )
                );
		if(THIS_IS_A_DEVELOPMENT_REASON_INSTANCE || !empty( $this->_request[ 'tm' ] ) )
		{
			$this->is_in_testing_mode = true;
		}
		else
		{
			$this->is_in_testing_mode = false;
		}
		
		$this->change_element_type('credit_card_expiration_year','numrange',array('start'=>date('Y'),'end'=>(date('Y')+15),'display_name' => 'Expiration Year'));
	}
	
	function pre_show_form()
	{
		echo '<div id="campForm" class="pageTwo">'."\n";
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
	function get_confirmation_text()
	{

		$txt = '<div id="campOverview">'."\n";			
	
		$txt .= '<p class="printConfirm">Print this confirmation for your records.</p>'."\n";
		$txt .= '<h3>Thank you for registering for Dorain Camp</h3>';
		if (reason_unique_name_exists('dorian_jh_thank_you_blurb'))
			$txt .= '<p>' . get_text_blurb_content('dorian_jh_thank_you_blurb'). '</p>';
		$txt .= '<p>Luther College is, for tax deduction purposes, a 501(c)(3) organization.</p>'."\n";
		$txt .= '<p>If you experience technical problems using the registration form, please contact the Luther College Music Office.</p>'."\n";
		$txt .= '<ul>'."\n";
		$txt .= '<li><strong>Date:</strong> '.date($this->date_format).'</li>'."\n";
		$txt .= '<h4>Your Information</h4>';
		$txt .= '<li><strong>Name:</strong> '.$this->controller->get('first_name').' '.$this->controller->get('last_name').'</li>'."\n";
		$txt .= '<li><strong>Address:</strong>'."\n".$this->controller->get('address')."\n".$this->controller->get('city').' '.$this->controller->get('state_province').' '.$this->controller->get('zip').'</li>'."\n";
		$txt .= '<li><strong>Phone:</strong> '.$this->controller->get('phone').'</li>'."\n";
		$txt .= '<li><strong>E-mail:</strong> '.$this->controller->get('e-mail').'</li>'."\n";
                $txt .= '<li><strong>School:</strong> '.$this->controller->get('school').'</li>'."\n";
                $txt .= '<li><strong>Grade:</strong> '.$this->controller->get('grade').'</li>'."\n";
                if ($this->controller->get('roomate_requested'))
                {
                    $txt .= '<li><strong>School:</strong> '.$this->controller->get('roomate_requested').'</li>'."\n";
                }
		$txt .= '<h4>Participation</h4>';
		if ($this->controller->get('band_participant'))
		{
			$txt .= '<li>You\'ll play ' .$this->controller->get('band_instrument'). ' in band.</li>'."\n";
		}
		if ($this->controller->get('orchestra_participant'))
		{
			$txt .= $txt .= '<li>You\'ll play ' .$this->controller->get('orchestra_instrument'). ' in orchestra.</li>'."\n";
		}
                if ($this->controller->get('jazz_band_participant'))
		{
			$txt .= $txt .= '<li>You\'ll play ' .$this->controller->get('jazz_band_instrument'). ' in jazz band.</li>'."\n";
		}
                if ($this->controller->get('wind_choir_participant'))
		{
			$txt .= $txt .= '<li>You\'ll play ' .$this->controller->get('wind_choir_instrument'). ' in wind choir.</li>'."\n";
		}
                if ($this->controller->get('brass_choir_participant'))
		{
			$txt .= $txt .= '<li>You\'ll play ' .$this->controller->get('brass_choir_instrument'). ' in brass choir.</li>'."\n";
		}
                if ($this->controller->get('private_lessons'))
		{
			$txt .= '<li>You\'d like ' .$this->controller->get('private_lessons'). ' set(s) of private lessons for '
                                . $this->controller->get('private_instrument_1');
                                if ($this->controller->get('private_instrument_2'))
                                {
                                    $txt .= ' and ' . $this->controller->get('private_instrument_2');
                                }
                                $txt .= '</li>'."\n";
		}

		$txt .= '<li><strong>Period 1:</strong>'.$this->controller->get('period_1').'</li>'."\n";
                $txt .= '<li><strong>Period 2:</strong>'.$this->controller->get('period_2').'</li>'."\n";
                $txt .= '<li><strong>Period 3:</strong>'.$this->controller->get('period_3').'</li>'."\n";
                $txt .= '<li><strong>Period 4:</strong>'.$this->controller->get('period_4').'</li>'."\n";
                $txt .= '<li><strong>Period 5:</strong>'.$this->controller->get('period_5').'</li>'."\n";
                $txt .= '<li><strong>Period 6:</strong>'.$this->controller->get('period_6').'</li>'."\n";
		
		$txt .= '</ul>'."\n";
		$txt .= '</div>'."\n";
		return $txt;
	}
	function run_error_checks()
	{
		if($this->get_value('billing_address') == 'new'
                        && (!$this->get_value('billing_street_address')
                        || !$this->get_value('billing_city')
                        || !$this->get_value('billing_state_province')
                        || !$this->get_value('billing_zip')
                        || !$this->get_value('billing_country') ) )
		{
			$this->set_error('billing_address', 'Please enter your full billing address if the address
                            you entered on the previous page was not the billing address for your credit card.');
		}
		
		
		// Process credit card
		if( !$this->_has_errors() )
		{
			$pf = new dorian_jh;
			
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
				$this->get_value('payment_amount'),
				$this->get_value('credit_card_number'),
				$expiration_mmyy,
				$this->revenue_budget_number,
				$this->get_value('credit_card_name'),
				$this->transaction_comment,
				$this->expense_budget_number
			);
			
			//$this->helper->build_transactions_array();
			
			
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
				//$confirm_text .= build_gift_review_detail_output( $this->helper, $this->date_format );
				
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
										'–'=>'-',
										//'<h3>'=>'--------------------'."\n\n",
										//'</h3>'=>'',
										'<br />'=>"\n",
									);
				/*
if (reason_unique_name_exists('giving_form_thank_you_blurb'))
					$confirm_text = get_text_blurb_content('giving_form_thank_you_blurb') . $confirm_text;
				else
					$confirm_text = '<p><strong>Thank you for your gift to Luther College!</strong></p>' . $confirm_text;
*/
				$mail_text = str_replace(array_keys($replacements),$replacements,$confirm_text);
				$email_to_music = new Email(array('dorian@luther.edu', 'buzzja01@luther.edu'), 'noreply@luther.edu','noreply@luther.edu', 'New Dorian Camper '.date('mdY H:i:s'),strip_tags($mail_text), $mail_text);
				$email_to_music->send();
				$email_to_giver = new Email($this->controller->get('e-mail'),'dorian@luther.edu','dorian@luther.edu','Luther College Dorian Camp Confirmation',strip_tags($mail_text),$mail_text);
				$email_to_giver->send();

//				$add_headers = 'Content-Type: text/plain; charset="utf-8"'."\r\n".'From: "Luther College Giving" <giving@luther.edu>' . "\r\n" .
//'Reply-To: "Luther College Giving" <giving@luther.edu>';
				/*
$add_headers = 'MIME-Version: 1.0' . "\r\n" . 'Content-Type: text/html; charset="utf-8"'."\r\n".'From: "Luther College Giving" <giving@luther.edu>' . "\r\n" .
'Reply-To: "Luther College Giving" <giving@luther.edu>';
				mail($this->controller->get('email'),'Luther College Gift Confirmation', $mail_text, $add_headers);
				mail('waskni01@luther.edu', 'New Online Gift', strip_tags($mail_text), $add_headers);
*/
				//}
			}
		}
	}
	function where_to()
	{
		$refnum = $this->get_value( 'result_refnum' );
		$text = $this->get_value( 'confirmation_text' );
		reason_include_once( 'minisite_templates/modules/dorian_jh_camps/dorian_jh_camps_confirmation.php' );
		$gc = new DorianJHCampConfirmation;
		$hash = $gc->make_hash( $text );
		connectDB( REASON_DB );
		$url = get_current_url();
		$parts = parse_url( $url );
		$url = $parts['scheme'].'://'.$parts['host'].$parts['path'].'?r='.$refnum.'&h='.$hash;
		return $url;
	}
}
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

<?php
////////////////////////////////////////////////////////////////////////////////
//
//    Steve Smith
//    2010-05-18
//
//    Work on the first page of the homecoming form
//
////////////////////////////////////////////////////////////////////////////////

class HomecomingRegistrationOneForm extends FormStep
{
	var $_log_errors = true;
	var $error;
	
	var $elements = array(
		'amount' => 'cloaked',
		'your_information_header' => array(
			'type' => 'comment',
			'text' => '<h3>Your Information</h3>',
		),
		'current_first_name' => array(
			'type' => 'text',
			'size' => 15,
		),
		'current_last_name' => array(
			'type' => 'text',
			'size' => 20,
		),
		'class_year' => 'text',
		'graduation_name' => array(
			'type' => 'text',
			'size' => 30,
		),
		'preferred_first_name' => array(
			'type' => 'text',
			'size' => 15,
		),
		'address' => array(
			'type' => 'text',
			'size' => 35,
		),
		'city' => array(
			'type' => 'text',
			'size' => 35,
		),
		'state_province' => array(
			'type' => 'state_province',
			'display_name' => 'State/Province',
		),
		'zip' => array(
			'type' => 'text',
			'display_name' => 'Zip/Postal Code',
			'size' => 35,
		),
		'home_phone' => array(
			'type' => 'text',
			'size'=>20,
		),
		'cell_phone' => array(
			'type' => 'text',
			'size'=>20,
		),
		'e-mail' => array(
			'type' => 'text',
			'size'=> 35,
		),
		'guest_information_header' => array(
			'type' => 'comment',
			'text' => '<h3>Spouse/Guest Information</h3>',
		),
		'guest_name' => array(
			'type' => 'text',
			'display_name' => 'Spouse/Guest Name',
			'size' => 35,
		),
		'attended_luther' => array(
			'type' => 'radio_inline_no_sort',
			'options' => array('yes' => 'Yes', 'no' => 'No'),
		),
		'guest_class' => 'text',
		'reservations_header' => array(
			'type' => 'comment',
			'text' => '<h3>Festival Dinner Reservations</h3>',
		),
		'attend_program' => array(
			'type' => 'select_no_sort',
			'display_name' => 'Founders Day Festival Dinner',
			'comments' => '<br />$40/person',
			'options' => array(
				'--'=>'--', 
				'1'=>'1 ticket, $40',
				'2'=>'2 tickets, $80',
				'3'=>'3 tickets, $120',
				'4'=>'4 tickets, $160',
				'5'=>'5 tickets, $200',
				'6'=>'6 tickets, $240',
			),
		),

                'festival_vegetarian' => array(
			'type' => 'radio_inline_no_sort',
                        'display_name' => 'Do you require a vegetarian meal?',
			'options' => array('yes' => 'Yes', 'no' => 'No'),
		),
                'festival_guests_names' => array(
			'type' => 'textarea',
                        'display_name' => 'Festival Guest Names and Class Year (if applicable)',	
		),
                'vegetarian_guests' => array(
			'type' => 'radio_inline_no_sort',
                        'display_name' => 'Do any of your guests require vegetarian meal?',
			'options' => array('yes' => 'Yes', 'no' => 'No'),
		),
                'vegetarian_guests_names' => array(
			'type' => 'text',
			'display_name' => 'Please list the vegetarian guests',
			'size' => 33,
                    ),
                'seating_preference' => array(
			'type' => 'textarea',
			'display_name' => 'Please tell us with whom you wish to be seated',
			'size' => 35,
                ),
                'luncheon_header' => array(
			'type' => 'comment',
			'text' => '<h3>Class Reunion Reservations</h3>'
		),
		'attend_luncheon' => 'text',
		'dinner_header' => array(
			'type' => 'comment',
			'text' => '<h3>Class Reunion Dinners/Receptions</h3>',
		),
		'attend_dinner_50_to_25' => 'text',
		'attend_dinner_20_to_10' => 'text',
		'attend_dinner_5' => 'text',
                'attend_1961_reception'=>'text',
		'ride_in_parade' => array(
			'type' => 'radio_inline_no_sort',
			'display_name' => 'Will you ride in the parade?',
			'comments' => 'Class of 1961 only',
			'options' => array(
				'yes' => 'Yes',
				'no' => 'No',
			),
		),
                
	);
	
	var $required = array('current_first_name', 'current_last_name', 'graduation_name', 'e-mail');
	
	var $display_name = 'Homecoming Info';
	var $error_header_text = 'Please check your form.';

	// style up the form and add comments et al	
	function on_every_time()
	{
		$date = getdate();
		$this->change_element_type( 
			'class_year', 'year', array('start' => ($date['year'] - 75), 'end' => ($date['year'])));
		$this->change_element_type( 
			'guest_class', 'year', array('start' => ($date['year'] - 75), 'end' => ($date['year'])));

		
		// Set years and cost for luncheon
		$classes_string_75_to_50 = 'for Classes ';
		for ($i = 75; $i >= 55; $i -= 5){
			$classes_string_75_to_50 .= ($date['year'] - $i);
			$classes_string_75_to_50 .= ', ';
		}
		$classes_string_75_to_50 .= $date['year'] - 50;
		$this->change_element_type(
			'attend_luncheon', 'select', array(
				'display_name' => 'Tickets for Luncheon',
				'comments' => '<br />'.$classes_string_75_to_50.'<br />No Cost',
				'options' => array( 
					'1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', 
					'6' => '6', '7' => '7', '8' => '8', '9' => '9',	'10' => '10',),
				)
			);
                // 1961 reception
                $this->change_element_type(
			'attend_1961_reception', 'select', array(
				'display_name' => 'Reservation for Friday\'s reception',
				'comments' => '<br /> Class of 1961 only<br />No Cost',
				'options' => array( 
					'1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', 
					'6' => '6', '7' => '7', '8' => '8', '9' => '9',	'10' => '10',),
				)
			);
			

		// Set years and ticket cost for 55 to 25 year reunions
		$classes_string_55_to_25 = 'for Classes ';
		for ($j = 55; $j >= 30; $j -= 5){
			$classes_string_55_to_25 .= ($date['year'] -$j);
			$classes_string_55_to_25 .= ', ';
		}
		$classes_string_55_to_25 .= $date['year'] - 25;
		$this->change_element_type(
			'attend_dinner_50_to_25', 'select_no_sort', array(
				'display_name' => 'Tickets for Saturday\'s Reunion Dinner', 
				'comments' => '<br />'.$classes_string_55_to_25.'<br />$20/person',
				'options' => array(
					'--'=>'--', 
					'1'=>'1 ticket, $20',
					'2'=>'2 tickets, $40',
					'3'=>'3 tickets, $60',
					'4'=>'4 tickets, $80',
					'5'=>'5 tickets, $100',
					'6'=>'6 tickets, $120',
				),
			));
		// Set years and ticket cost for 20 to 10 year reunions		
		$classes_string_20_to_10 = 'for Classes ';
		for ($k = 20; $k >= 15; $k -= 5){
			$classes_string_20_to_10 .= ($date['year'] -$k);
			$classes_string_20_to_10 .= ', ';
		}
		$classes_string_20_to_10 .= $date['year'] - 10;
		$this->change_element_type(
			'attend_dinner_20_to_10', 'select_no_sort', array(
				'display_name' => 'Tickets for Saturday\'s Reunion Reception', 
				'comments' => '<br />'.$classes_string_20_to_10.'<br />$15/person',
				'options' => array(
					'--'=>'--', 
					'1'=>'1 ticket, $15',
					'2'=>'2 tickets, $30',
					'3'=>'3 tickets, $45',
					'4'=>'4 tickets, $60',
					'5'=>'5 tickets, $75',
					'6'=>'6 tickets, $90',
				),
			));
			
		// Set cost for 5 year reunion
		$this->change_element_type(
			'attend_dinner_5', 'select_no_sort', array(
				'display_name' => 'Tickets for Saturday\'s Reunion Reception',
				'comments' => '<br />for Class of '. ($date['year']-5) .'<br />$10/person',
				'options' => array(
					'--'=>'--', 
					'1'=>'1 ticket, $10',
					'2'=>'2 tickets, $20',
					'3'=>'3 tickets, $30',
					'4'=>'4 tickets, $40',
					'5'=>'5 tickets, $50',
					'6'=>'6 tickets, $60',

					),
				)
			);				
	}

	function pre_show_form()
	{
		echo '<div id="homecomingForm" class="pageOne">'."\n";
	}
	function post_show_form()
	{
		echo '</div>'."\n";
	}
	
	function needs_payment()
	{
	  	$amount = 0;
	  	$program_tix = $this->get_value('attend_program');
	  	$dinner_tix_50_to_25 = $this->get_value('attend_dinner_50_to_25');
	  	$dinner_tix_20_to_10 = $this->get_value('attend_dinner_20_to_10');
	  	$dinner_tix_5 = $this->get_value('attend_dinner_5');

		if (isset($program_tix))
	  	{
			$amount = $amount + ($program_tix * 40);
		}
		
		if (isset($dinner_tix_50_to_25))
	  	{
			$amount = $amount + ($dinner_tix_50_to_25 * 20);
		}
		
		if (isset($dinner_tix_20_to_10))
	  	{
			$amount = $amount + ($dinner_tix_20_to_10 * 15);
		}
		
		if (isset($dinner_tix_5))
	  	{
			$amount = $amount + ($dinner_tix_5 * 10);
		}
		$this->set_value('amount', $amount);
		
		if ($amount == 0)
		{
			return 'HomecomingRegistrationConfirmation';
		}else{
			return 'HomecomingRegistrationTwoForm';
		}
	}
}

?>

<?

reason_include_once('minisite_templates/modules/form/views/thor/credit_card_payment.php');
include_once(WEB_PATH . 'stock/pfproclass.php'); //<<<< Change this
$GLOBALS['_form_view_class_names'][basename(__FILE__, '.php')] = 'CreditCardNoPaymentThorForm';

/**
 *
 * 	For forms that would like to offer registration but with an option for people to pay by check or when they
 * 	arrive (i.e. they don't have to pay when filling out the form). If they do pay at the time of registration, 
 * 	their expense budget number, revenue budget number and payment amount will be forwarded to this form. 
 * 
 *
 * @package reason_package_local
 * @subpackage thor_view
 * @author Steve Smith
 *
 * @todo get form title from the forwarding form.
 */
class CreditCardNoPaymentThorForm extends CreditCardThorForm {

		function run_error_checks() {
				
				
				$no_payment = false;
				
				// Validate the e-mail address field if used
				if (($email_name = $this->get_element_name_from_label('Your Email')) && $this->get_value($email_name))
						if (!check_against_regexp($this->get_value($email_name), array('email')))
								$this->set_error($email_name, 'Please enter a valid email address.');

				// Make sure we have a payment amount; look for a dollar sign first, then any number
				if (preg_match('/\$([\d,]+\.?\d{0,2})/', $this->get_value($this->payment_element), $match) ||
						preg_match('/([\d,]+\.?\d{0,2})/', $this->get_value($this->payment_element), $match)) {
						// remove any extra characters from amount
						$payment_amount = preg_replace('/[^\d\.]/', '', $match[1]);
				
				// If there is a hidden field called 'No Payment Option' then allow the user to go to the thank you page
				} elseif ($this->get_element_name_from_label('No Payment Option')) {
						$no_payment = true;
				} else {
						
						$this->set_error($this->payment_element, 'Could not work out payment amount. Please contact the form maintainer.');
				}

				// This is where we process the credit card, so that the form can't be submitted unless the payment
				// goes through
				if (!$this->_has_errors() && $no_payment === false) {
						$pf = new pfpc;
						$expiration_mm = str_pad($this->get_value('credit_card_expiration_month'), 2, '0', STR_PAD_LEFT);
						$expiration_yy = substr($this->get_value('credit_card_expiration_year'), 2, 2);
						$expiration_mmyy = $expiration_mm . $expiration_yy;

						foreach ($this->elements as $element_name => $vals) {
								if ($this->get_value($element_name)) {
										if (empty($this->database_transformations[$element_name])) {
												$pass_info[$element_name] = $this->get_value($element_name);
										} else {
												$pass_info[$element_name] = $this->database_transformations[$element_name]($this->get_value($element_name));
										}
								}
						}

						$model = & $this->get_model();

						$pf->set_info(
								$payment_amount, $this->get_value('credit_card_number'), $expiration_mmyy, $this->get_value($this->revenue_budget_number), $this->get_value('credit_card_name'), $this->get_value($this->expense_budget_number), $model->get_form_name(), $this->get_value('billing_street_address'), $this->get_value('billing_city'), $this->get_value('billing_state_province'), $this->get_value('billing_zip'), $this->get_value($email_name)
						);

						/* THIS IS WHERE THE TRANSACTION TAKES PLACE */
						// Test mode: $result = $pf->transact('test');
						// Live mode: $result = $pf->transact();
						if ($this->is_in_testing_mode) {
								$pfresult = $pf->transact('test');
						} else {
								$pfresult = $pf->transact();
						}
						if (!$pf->approved) {
								$message = $pf->message;
								$this->set_error('credit_card_number', $message);
						} else {
								//// DO YOUR OWN LOGGING HERE
								connectDB('reason_transactions');

								$billing_address = $this->get_value('billing_street_address') . "\n" .
								$this->get_value('billing_city') . ", " .
								$this->get_value('billing_state_province') . "  " .
								$this->get_value('billing_zip') . "\n" .
								$this->get_value('billing_country') . "\n";

								$query = 'INSERT INTO transactions SET
								REFNUM = "' . $pfresult['PNREF'] . '",
								source = "' . addslashes($pf->comment2) . '", 
								amount = "' . addslashes($pf->amount) . '", 
								name_on_card = "' . addslashes($this->get_value('credit_card_name')) . '", 
								billing_address = "' . addslashes($billing_address) . '", 
								card_number = "' . addslashes(obscure_credit_card_number($this->get_value('credit_card_number'))) . '", 
								card_expiration = "' . addslashes($expiration_mmyy) . '"';

								$dbresult = db_query($query, 'We were unable to record your transaction in our database. 
								Your credit card has been charged, but you should contact the owner of this form
								to verify that your payment was received.', false);

						connectDB(REASON_DB);
						}
				}
		}

}

?>
<?php
// the shortcode
function contact_shortcode($atts) {
	extract(shortcode_atts(array(
		"email" 				=> get_bloginfo('admin_email'),
		"subject" 				=> '',
		"label_name" 			=> __('Your name', 'netfunda') ,
		"label_email" 			=> __('Your email', 'netfunda') ,
		"label_subject" 		=> __('Subject', 'netfunda') ,
		"label_message"			=> __('Message', 'netfunda') ,
		"label_submit" 			=> __('Submit', 'netfunda') ,
		"error_empty" 			=> __("Please fill in all the required fields", "netfunda"),
		"error_form_name" 		=> __('Please enter at least 3 characters', 'netfunda') ,
		"error_form_subject" 		=> __('Please enter at least 3 characters', 'netfunda') ,
		"error_form_message" 		=> __('Please enter at least 1 characters', 'netfunda') ,
		"error_email" 			=> __("Please enter a valid email", "netfunda"),
		"success" 				=> __("Thanks for your message!", "netfunda"),
	), $atts));

	if (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['form_send']) ) {
	
		$post_data = array(
			'form_name'		=> $_POST['form_name'],
			'email'			=> $_POST['email'],
			'form_subject'		=> $_POST['form_subject'],
			'form_message'		=> $_POST['form_message']
		);
		



global $wpdb;
$query = "INSERT INTO `wpcontract` (`name`, `email`, `subject`, `message`) VALUES
(' $post_data[form_name]', '$post_data[email]', '$post_data[form_subject]', '$post_data[form_message]');";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($query);


		$error = false;
		$required_fields = array("form_name", "email", "form_subject", "form_message");
		



		foreach ($required_fields as $required_field) {
			$value = stripslashes(trim($post_data[$required_field]));
		
		// displaying error message if validation failed for each input field
			if(((($required_field == "form_name") || ($required_field == "form_subject")) && strlen($value)<3) || 
			 	(($required_field == "form_message") && strlen($value)<1) || empty($value)) {
				$error_class[$required_field] = "error";
				$error_msg[$required_field] = ${"error_".$required_field};
				$error = true;
				$result = $error_empty;
			}
			$form_data[$required_field] = $value;
		}
		

		// sending email to admin
		if ($error == false) {
			$email_subject = "[" . get_bloginfo('name') . "] " . $form_data['form_subject'];
			$email_message = $form_data['form_message'] . "\n\nIP: ";
			$headers  = "From: ".$form_data['form_name']." <".$form_data['email'].">\n";
			$headers .= "Content-Type: text/plain; charset=UTF-8\n";
			$headers .= "Content-Transfer-Encoding: 8bit\n";
			wp_mail($email, $email_subject, $email_message, $headers);
			$result = $success;
			$sent = true;
		}
	}

	// message 
	if($result != "") {
		$info .= '<div class="info">'.$result.'</div>';
	}

	// the contact form with error messages


	$email_form = '<form class="contact" id="contact" method="post" action="">
		<div >
			<label for="contact_name">'.$label_name.': <span class="error '.((isset($error_class['form_name']))?"":" hide").'" >'.$error_form_name.'</span></label>
			<input type="text" name="form_name" id="contact_name" class="'.$error_class['form_name'].'" maxlength="50" value="'.$form_data['form_name'].'" />
		</div>
		<div>
			<label for="contact_email">'.$label_email.': <span class="error '.((isset($error_class['email']))?"":" hide").'" >'.$error_email.'</span></label>
			<input type="text" name="email" id="contact_email" class="'.$error_class['email'].'"  maxlength="50" value="'.$form_data['email'].'" />
		</div>
		<div>
			<label for="contact_subject">'.$label_subject.': <span class="error '.((isset($error_class['form_subject']))?"":" hide").'" >'.$error_form_subject.'</span></label>
			<input type="text" name="form_subject" id="contact_subject" maxlength="50"  class="'.$error_class['form_subject'].'"  value="'.$subject.$form_data['form_subject'].'" />
		</div>
		<div>
			<label for="contact_message">'.$label_message.': <span class="error '.((isset($error_class['form_message']))?"":" hide").'" >'.$error_form_message.'</span></label>
			<textarea name="form_message" id="contact_message" rows="10" class="'.$error_class['form_message'].'" >'.$form_data['form_message'].'</textarea>
			
		</div>
		<div>
			<input type="submit" value="'.$label_submit.'" name="form_send" id="contact_send" />
		</div>


</form>'
;
if($sent == true) {
		return $info;
	} else {
		return $info.$email_form;
	} 

}
add_shortcode('paste-it-go-get-contact-form', 'contact_shortcode');
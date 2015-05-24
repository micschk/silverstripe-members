<?php

class AccountCreateForm_SendLoginExtension extends DataExtension {
	
	public function onCreate($data, $form){
		
		$arrayData = new ArrayData(array(
			'UserAccount' => $data,
			'SiteConfig' => SiteConfig::current_site_config()
		));
		$body = $arrayData->renderWith('SendLoginDetailsEmail');

		Email::config()->admin_email? $from = Email::config()->admin_email : $from = 'noreply@'.$_SERVER['HTTP_HOST'];
		if($from=="noreply@localhost") $from="noreply@test.com";
		$to = $data['Email'];
		$email = new Email(
			$from,
			$to,
			"Account: {$data['Email']}",
			$body
		);
//		Debug::dump($email->debug());
//		exit();
		$email->send();
		
	}
	
}

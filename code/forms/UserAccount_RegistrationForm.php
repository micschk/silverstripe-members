<?php

class UserAccount_RegistrationForm extends UserAccount_CreateForm{

	private static $allowed_actions = array(
		'doCreate'
	);
	
	function __construct($controller,$name = "UserAccount_RegistrationForm", $fields = null){
		
		parent::__construct($controller, $name, $fields);
		
		$this->extend('updateMemberRegistrationForm');
	}
	
	public function doCreate($data, $form){

		//log out existing user
		if($member = Member::currentUser()){
			$member->logOut();
		}
		
		$member = Member::create();
		$form->saveInto($member);
		$member->write();
		$this->extend('onRegister');
		
		// add to group set in UserAccountPage
		if($this->controller->UserAccountPage()->exists()){
			$group = $this->controller->UserAccountPage()->Group();
			if($group->exists()){
				$group->Members()->add($member);
			}
		}
		
		//$member->logIn();
		
		// Send update notification (before writing so we can check which fields have changed
		if (Config::inst()->get('UserAccounts', 'send_frontend_update_notifications')) {
			$this->controller->sendRegistrationNotification($member);
		}
		
		return $this->controller->redirect($this->controller->Link('done'));
		
	}
	
}

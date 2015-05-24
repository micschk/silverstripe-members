<?php

class UserAccount_CreateForm extends Form{

	private static $allowed_actions = array(
		'doCreate'
	);
	
	function __construct($controller,$name = "UserAccount_CreateForm", $fields = null){
		if(!$fields){
			$restrictfields = array(
				Member::get_unique_identifier_field(),'FirstName','Surname'
			);
			$fields = singleton('Member')->scaffoldFormFields(array(
				'restrictFields' => $restrictfields,
				'fieldClasses' => array(
					'Email' => 'EmailField'
				)
			));
		}
		$fields->push(new ConfirmedPasswordField("Password"));
		
		$actions = new FieldList(
			$register = new FormAction('doCreate',"Create account")		
		);
		$validator = new MemberCreation_Validator(
			Member::get_unique_identifier_field(),
			'FirstName',
			'Surname'
		);
		parent::__construct($controller, $name, $fields, $actions, $validator);
		
		if(class_exists('SpamProtectorManager')) {
			$this->enableSpamProtection();
		}
		
		$this->extend('updateMemberCreationForm');
	}
	
	public function doCreate($data, $form){

		//Debug::dump('called');
		
		$member = Member::create();
		$form->saveInto($member);
		$member->write();
		
		// add to group set in UserAccountPage
		$group = $this->controller->Group();
		if($group->exists()){
			$group->Members()->add($member);
		}
		
		// extension hook
		$this->extend('onCreate', $data, $form);
//		$member->logIn();
		
//		if($back = Session::get("BackURL")){
//			Session::clear("BackURL");
//			return $this->Controller()->redirect($back);
//		}
//		if($link = $member->getProfileLink()){
//			return $this->controller->redirect($link);
//		}
		
		return $this->controller->redirect($this->controller->Link());
	}
	
}

class MemberCreation_Validator extends Member_Validator{
	
	public function php($data) {
		$valid = parent::php($data);
	
		$identifierField = Member::config()->unique_identifier_field;
	
		$member = Member::get()
					->filter($identifierField, $data[$identifierField])
					->first();

		if(is_object($member) && $member->isInDB()) {
			$uniqueField = $this->form->Fields()->dataFieldByName($identifierField);
			$this->validationError(
				$uniqueField->id(),
				sprintf(
					_t(
						'Member.VALIDATIONMEMBEREXISTS',
						'A member already exists with the same %s'
					),
					strtolower($identifierField)
				),
				'required'
			);
			$valid = false;
		}
	
		// Execute the validators on the extensions
		if($this->extension_instances) {
			foreach($this->extension_instances as $extension) {
				if(method_exists($extension, 'hasMethod') && $extension->hasMethod('updatePHP')) {
					$valid &= $extension->updatePHP($data, $this->form);
				}
			}
		}
	
		return $valid;
	}
	
}
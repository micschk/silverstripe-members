<?php

class UserAccount_MemberExtrafields_Extension extends DataExtension {

	private static $db = array(
		'PhoneNumbers' => 'MultiValueField'
	);
	
	public function updateCMSFields(FieldList $fields) {
		//$this->updateUserAccountFormFields($fields);
		$fields->push(UserAccount_MemberExtrafields_Extension::getExtraFields());
	}
	
	public static function getExtraFields(){
		return new MultiValueTextField('PhoneNumbers', 
				_t (
						'UserAccounts.PHONENUMBERS',
						'Phone number(s)'
					)
				);
	}
	
}

// extra class to decorate controller because of errors when applied from Member decorator
class UserAccount_MemberExtrafields_Controller_Extension extends Extension{
	
	public function updateUserAccountFormFields(FieldList $fields) {
		$fields->push(UserAccount_MemberExtrafields_Extension::getExtraFields());
	}
	
//	public function updateEditAccountForm($form) {
//		$fields = $form->Fields();
//		$this->updateUserAccountFormFields($fields);
//	}
	
	public function updateAccountRegisterForm(Form $form) {
		$form->Fields()->push(UserAccount_MemberExtrafields_Extension::getExtraFields());
	}

}
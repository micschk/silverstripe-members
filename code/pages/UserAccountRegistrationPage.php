<?php

/**
 * Page for allowing users to register
 */
class UserAccountRegistrationPage extends Page{
	
	private static $db = array (
		'Title_AfterRegistration' => 'VarChar(512)',
		'Content_AfterRegistration' => 'HTMLText',
	);

	private static $has_one = array(
		'UserAccountPage' => 'SiteTree'
	);

	public function getCMSFields(){
		$fields = parent::getCMSFields();
		
		// set warning if no UserAccountPage linked
		if(!$this->UserAccountPageID){
			Session::set(
				"FormInfo.Form_EditForm.formError.message", 
				_t('UserAccounts.UserAccountPageRequired', 
						'Please make sure to link this RegistrationPage to a UserAccountPage that it should take its configuration from (like groups newly registered users should be added to, and e-mail addresses that should be notified of new registrations).')
			);
			Session::set("FormInfo.Form_EditForm.formError.type", 'warning');
		}
		
		$pagepicker = DropdownField::create("UserAccountPageID", "Linked UserAccountPage",
				UserAccountPage::get()->map()->toArray()
			)->setHasEmptyDefault(true);
		
		$aftertitle = TextField::create('Title_AfterRegistration', "Title After Registration");
		$aftercontent = HtmlEditorField::create('Content_AfterRegistration', "Content After Registration");
		
		$fields->addFieldsToTab("Root.UserAccounts", 
				array($pagepicker, $aftertitle, $aftercontent));

		return $fields;
	}
	
//	public function validate() {
//		$result = parent::validate();
//		
//		// Check if we're linked to a valid UserAccountPage or Subclass thereof...
//		if($this->ID && !$this->UserAccountPageID){
//			$result->error(
//				_t('UserAccounts.UserAccountPageRequired', 
//				//'Please make sure to link your registerpage to a UserAccountPage'
//						$this->UserAccountPageID
//				)
//			);
//		}
//		
//		return $result;
//	}
	
	// Only allow creation of a Registrationpage if a UserAccountPage exists
//	function canCreate($member = null) {
//		parent::canCreate($member);
//
//		$UserAccountPage = UserAccountPage::get();
//
//		if ($UserAccountPage && $UserAccountPage->Count()) {
//			return true;
//		}
//
//		return false;
//	}
	
}

class UserAccountRegistrationPage_Controller extends Page_Controller{
	
	private static $allowed_actions = array(
		'accountRegistrationForm' => true,
		'done' => true,
	);
	
	public function init(){
		parent::init();
		
		// set form & render template...
		$this->Form = $this->accountRegistrationForm();

		$this->extend("onBeforeCreate");

		return $this->renderWith(array(
			"UserAccountRegistrationPage",
			"UserAccountPage_edit",
			"UserAccountPage",
			"Page"
		));
	}
	
	public function done(){
		
		return $this->customise(array(
			'Title' => $this->Title_AfterRegistration,
			'Content' => $this->Content_AfterRegistration,
			'Form' => false,
		));
		
	}
	
	/**
	 * Send a notification of registration if configured
	 * @param type $UserAccount
	 */
	
	public function sendRegistrationNotification($UserAccount) {
		//$this->owner->isChanged(), or $this->owner->isChanged('FieldName');
		$body = $this->customise(array('UserAccount' => $UserAccount))
				->renderWith('RegistrationNotificationEmail');

		Email::config()->admin_email? $from = Email::config()->admin_email : $from = 'noreply@test.com';
		
		$userAccountPage = $this->UserAccountPage();
		if($userAccountPage->exists()){
			$notifylist = $userAccountPage->NotifyEmailOfUpdates;
		}
		if(! $notifylist){ return; }
		$to = str_replace("\n", ',', $notifylist);
		$email = new Email(
			$from,
			$to,
			"New registration: {$UserAccount->getName()}",
			$body
		);

//		Debug::dump($email->debug());
//		exit();
		
		$email->send();
	}
	
    /**
     * Factory for generating a profile form. The form can be expanded using an
     * extension class and calling the updateAccountEditForm method.
     *
     * @return Form
     */
	public function accountRegistrationForm(){
		$form = UserAccount_RegistrationForm::create($this, "accountRegistrationForm");

        $this->extend("updateAccountRegisterForm", $form);

        return $form;
	}

}

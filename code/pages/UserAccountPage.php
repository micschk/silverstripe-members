<?php

// NOTE: 'CurrentAccount' refers to the account being edited/viewed as opposed tot the current member

/**
 * Page for displaying a member's own profile, or a list of members (configurable via the CMS)
 * and serving as a parent for viewing individual members.
 */
class UserAccountPage extends Page{

	public static $icon = 'useraccounts/images/members-icon.png';	
	
	public static $Account = null; // holds a reference to user for view/edit methods
	
	private static $db = array (
		'IndexView'	=> 'Enum("list, view", "list")',
		'NotifyEmailOfUpdates' => 'Text',
		
//		'Title_CreateAccount' => 'VarChar(255)',
//		'Content_CreateAccount' => 'HTMLText',
//		'Title_ViewAccount' => 'VarChar(255)',
//		'Content_CreateAccount' => 'HTMLText',
//		'Title_EditAccount' => 'VarChar(255)',
//		'Content_CreateAccount' => 'HTMLText',
//		'Title_DeleteAccount' => 'VarChar(255)',
//		'Content_CreateAccount' => 'HTMLText',
//		'Title_ChangePassword' => 'VarChar(255)',
//		'Content_ChangePassword' => 'HTMLText',
	);

	private static $has_one = array(
		'Group' => 'Group'
	);
	
	private static $many_many = array (
		'EditAccountGroups' => 'Group',
	);

	public function getCMSFields(){
		$fields = parent::getCMSFields();
		
		$editorgrouppicker = TreeMultiselectField::create(
				'EditAccountGroups',
				_t('UserAccounts.EDITGROUPS', 'Allow account editing'),
				'Group'
			);
		$editorgrouppicker->setRightTitle(
				_t('UserAccounts.EDITGROUPS_RIGHTTITLE', 'User groups to allow creation & editing of accounts from front-end (leave empty to disallow'));
		
		$grouppicker = DropdownField::create("GroupID", "Member group",
				Group::get()->map()->toArray()
			)->setHasEmptyDefault(true);
		
		$indexview = DropdownField::create("IndexView", _t('UserAccounts.INDEXVIEW', 'Page Content'),
				array(
					'list' => _t('UserAccounts.LIST', 'A list of members in this group'),
					'view' => _t('UserAccounts.VIEW', 'The profile of the currently logged in member'),
				));
		
		$notifymails = TextareaField::create('NotifyEmailOfUpdates', 
				_t('UserAccounts.NOTIFYMAILS', 'Update notifications'));
			$notifymails->setRightTitle(_t('UserAccounts.NOTIFYMAILSHELP', 
					'Email addresses to be notified when users change their details (one per line)'));
		
		$fields->addFieldsToTab("Root.UserAccounts", 
				array($grouppicker, $indexview, $editorgrouppicker, $notifymails));

		return $fields;
	}

	/**
	 * Get the members list.
	 */
	public function getAccountList(){
		
		$group = $this->Group();
		if($group->exists()){
			$members = $group->Members();
		} else {
			$members = Member::get()->byID(-1); // empty memberlist...
		}
		//$members = $this->Groups()->relation('Members'); // only works for many_many
		
		// allow extending
		$this->extend('updateUserList', $members);
		
		return $members;
	}
	
	/**
	 * Get the members list.
	 */
	public function getEditorList(){
		
		$members = $this->EditAccountGroups()->relation('Members'); // 'relation' only works for many_many
		
		// allow extending
		$this->extend('updateEditorList', $members);
		
		return $members;
	}
	
	/**
	 * Get current Members ID for comparison in templates
	 */
	public function currentMemberID(){
		return Member::currentUserID();
	}
	
}

class UserAccountPage_Controller extends Page_Controller{
	
	protected $current_account_id = null;
	
	private static $allowed_actions = array(
		'index' => true,
//		'list' => true,
//		'register' => true, // create
//		'accountRegisterForm' => true,
		'addaccount' => true,
		'accountCreateForm' => true,
		'view' => true,  // read
		'edit' => true, // edit
		'accountEditForm' => true,
		'changepassword' => true,
		'ChangePasswordForm' => true,
		'delete' => true, // delete
		'accountDeleteForm' => true,
	);
	
	public function init(){
		parent::init();
		// save reference to memberID (results in null if not editing anyone)
		$this->setCurrentAccountID( $this->getCurrentAccountIDFromRequest() );
	}
	
	public function index() {
//		if (isset($_GET['BackURL'])) {
//			Session::set('MemberProfile.REDIRECT', $_GET['BackURL']);
//		}
//		$mode = Member::currentUser() ? 'profile' : 'register';
//		$data = Member::currentUser() ? $this->indexProfile() : $this->indexRegister();
//		if (is_array($data)) {
//			return $this->customise($data)
//					->renderWith(array('MemberProfilePage_'.$mode, 'MemberProfilePage', 'Page'));
//		}
//		return $data;
		
		// show list or member's own profile (let the templates get the required data)
		$mode = $this->IndexView;
		return $this->renderWith(array(
			'UserAccountPage_'.$mode, 
			'UserAccountPage',
			'Page'));
	}

	/**
	 * Allow users to register if registration is enabled.
	 *
	 * @return array
	 */
//	protected function indexRegister() {
//		if(!$this->AllowRegistration) return Security::permissionFailure($this, _t (
//			'MemberProfiles.CANNOTREGPLEASELOGIN',
//			'You cannot register on this profile page. Please login to edit your profile.'
//		));
//
//		return array (
//			'Title'   => $this->obj('RegistrationTitle'),
//			'Content' => $this->obj('RegistrationContent'),
//			'Form'    => $this->RegisterForm()
//		);
//	}

	/**
	 * Allows users to edit their profile if they are in at least one of the
	 * groups this page is restricted to, and editing isn't disabled.
	 *
	 * If editing is disabled, but the current user can add users, then they
	 * are redirected to the add user page.
	 *
	 * @return array
	 */
//	protected function indexProfile() {
//		if(!$this->AllowProfileEditing) {
//			if($this->AllowAdding && Injector::inst()->get('Member')->canCreate()) {
//				return $this->redirect($this->Link('add'));
//			}
//
//			return Security::permissionFailure($this, _t(
//				'MemberProfiles.CANNOTEDIT',
//				'You cannot edit your profile via this page.'
//			));
//		}
//
//		$member = Member::currentUser();
//
//		foreach($this->Groups() as $group) {
//			if(!$member->inGroup($group)) {
//				return Security::permissionFailure($this);
//			}
//		}
//
//		$form = $this->ProfileForm();
//		$form->loadDataFrom($member);
//
//		if($password = $form->Fields()->fieldByName('Password')) {
//			$password->setCanBeEmpty(false);
//			$password->setValue(null);
//			$password->setCanBeEmpty(true);
//		}
//
//		return array (
//			'Title' => $this->obj('ProfileTitle'),
//			'Content' => $this->obj('ProfileContent'),
//			'Form'  => $form
//		);
//	}
	
	
	/**
	 * Register a new member
	 */
	public function register() {
		if($member = $this->getMemberFromRequest()) {
		}
	}
	
	/**
	 * Edit a member (edit own profile or if allowed of another member)
	 */
	public function addaccount() {
		
		// Check if user can update this account
		$this->checkUpdatePrivileges();
		
		// all OK, set form & render template...
		$this->Form = $this->accountCreateForm();

		$this->extend("onBeforeCreate");

		return $this->renderWith(array(
			"UserAccountPage_create",
			"UserAccountPage_edit",
			"UserAccountPage",
			"Page"
		));
		
	}

	/**
	 * View an individual member.
	 */
	public function view() {
		
		// check if we can/may view the user
		$user = $this->getCurrentAccountFromRequest();
		if( !Config::inst()->get('UserAccounts', 'allow_profile_viewing')
				|| !$user ) {
			$this->httpError(404);
		} else {
			// all OK, set user & render template...
			$this->Account = $user;
			return $this;
		}
		
		//return new MemberProfileViewer($this, 'show');
//		if($user = $this->getUserFromRequest()) {
//			//shift the request params
//			$this->request->shiftAllParams();
//			$this->request->shift();
//			$record = new MemberProfilePage(array(
//				'ID' => -1,
//				'Content' => '',
//				'ParentID' => $this->ID,
//				'MemberID' => $member->ID,
//				'URLSegment' => 'view/'.$member->ID
//			));
//			$cont = new MemberProfilePage_Controller($record);
//			$cont->setMember($member);
//			return $cont;
//		}
	
	}
	
	/**
	 * Edit a member (edit own profile or if allowed of another member)
	 */
	public function edit() {
		
		// Check if user can update this account
		$this->checkUpdatePrivileges();
		
		// all OK, set user & render template...
		if($account = $this->getCurrentAccountFromRequest()) {
			
			$this->Account = $account;
			$this->Form = $this->accountEditForm();
			
			// allow updating from from Member decorator
			$this->Account->extend("updateEditAccountForm", $this->Form);
			
			$this->Form->loadDataFrom($account);
			
			$this->extend("onBeforeEdit");

			return $this->renderWith(array(
				"UserAccountPage_edit",
				"UserAccountPage",
				"Page"
			));
		} else {
			$this->sendHttpError(404);
		}
		
	}
	
	/**
	 * Delete a member 
	 */
	public function delete() {
		// Check if user can update this account
		$this->checkUpdatePrivileges();
		
		// all OK, set user & render template...
		if($account = $this->getCurrentAccountFromRequest()) {
			$this->Account = $account;
			$this->Form = $this->accountDeleteForm();
			
			// allow updating from from Member decorator
			$this->Account->extend("updateDeleteAccountForm", $this->Form);
			
			$this->Form->loadDataFrom($account);
			
			$this->extend("onBeforeDelete");
			
			return $this->renderWith(array(
				"UserAccountPage_delete",
				"UserAccountPage",
				"Page"
			));
		}
	}
	
	public function changepassword() {
		
		// Check if user can update this account
		// Users may only edit their own passwords anyway...
		//$this->checkUpdatePrivileges();
		
        // Set the back URL for this form (this is hardcoded into core)...
        Session::set("BackURL",$this->Link("changepassword"));
		
		$this->Form = $this->ChangePasswordForm();
			
		$this->extend("onBeforeChangePassword");

		return $this->renderWith(array(
			"UserAccountPage_changepassword",
			"UserAccountPage_edit",
			"UserAccountPage",
			"Page"
		));
		
    }
	
	//
	// HELPERS
	//
	
	public function getCurrentAccountID() {
		return $this->current_account_id;
	}
	
	public function setCurrentAccountID($id) {
		$this->current_account_id = $id;
		return $this;
	}
	
	/**
	 * Get an edited Member ID using the URL ID parameter, should be called just once e.g. on init
	 * getting the ID later should be done via getCurrentAccountID
	 */
	public function getCurrentAccountIDFromRequest() {
		// when requesting editfield
		if($id = (int)$this->request->param('ID') ){ return $id; }
		// when sent from Uploadfield (json) etc
		if($id = (int)$this->request->postVar('ID')) { return $id; } 
		// when sent as get var (not sure if this ever happens...
		if($id = (int)$this->request->getVar('ID')) { return $id; }
		// if none set, return the current user's ID -- mixes things up
		//return Member::currentUserID();
	}

	/**
	 * Get an invidual member using the URL ID parameter
	 * @return Member|null
	 */
	public function getCurrentAccountFromRequest() {
		$users = $this->getAccountList();
		return $users->byID( (int)$this->getCurrentAccountID() );
	}
	
	/**
	 * 
	 * @param type $UserAccount
	 */
	public function checkUpdatePrivileges(){
		$currentUserID = Member::currentUserID();
		$editedAccountID = (int)$this->request->param('ID');
		
		//
		// Various checks
		// 
		// HTTP forbidden = 403, still we may want to return 404 so we get a nicer page?
		// user not currently logged in
		if( !$currentUserID ){ $this->sendHttpError(403); }
		// editing of accounts not allowed via this class
		if( !Config::inst()->get('UserAccounts', 'allow_profile_editing') ){
			$this->sendHttpError(403); 
		}
		// user not same as edited account & user can CRUD other acounts
		$inEditorGroups = $this->getEditorList()->filter('ID',$currentUserID)->first();
		if( $currentUserID != $editedAccountID 
				&& !$inEditorGroups){ 
			$this->sendHttpError(403); 
		}
	}
	
	public function sendUpdateNotification($UserAccount) {
		//$this->owner->isChanged(), or $this->owner->isChanged('FieldName');
		if($UserAccount->isChanged() && $this->NotifyEmailOfUpdates){
		
			$body = $this->customise(array('UserAccount' => $UserAccount))
					->renderWith('UpdateNotificationEmail');
		
	//		$body = "$name has updated their details via the website. Here is the new information:<br/>";
	//		foreach($this->member->getAllFields() as $key => $field){
	//			if(isset($data[$key])){
	//				$body .= "<br/>$key: ".$data[$key];
	//				$body .= ($field != $data[$key])? "  <span style='color:red;'>(changed)</span>" : "";
	//			}
	//		}
			
			$notifylist = $this->NotifyEmailOfUpdates;
			if(! $notifylist){ return; }
			
			Email::config()->admin_email? $from = Email::config()->admin_email : $from = 'noreply@test.com';
			$to = str_replace("\n", ',', $notifylist);
			$email = new Email(
				$from,
				$to,
				"Account updated: {$UserAccount->getName()}",
				$body
			);
//			Debug::dump($UserAccount->isChanged('PhoneNumbers'));
//			Debug::dump($UserAccount->db('PhoneNumbers')->isChanged());
//			Debug::dump($email->debug());
//			exit();
			
			$email->send();
		}
	}
	
	public function sendDeleteNotification($UserAccount) {
		//$this->owner->isChanged(), or $this->owner->isChanged('FieldName');
		$body = $this->customise(array('UserAccount' => $UserAccount))
				->renderWith('DeleteNotificationEmail');

		$notifylist = $this->NotifyEmailOfUpdates;
		if(! $notifylist){ return; }
			
		Email::config()->admin_email? $from = Email::config()->admin_email : $from = 'noreply@test.com';
		$to = str_replace("\n", ',', $notifylist);
		$email = new Email(
			$from,
			$to,
			"Account deleted: {$UserAccount->getName()}",
			$body
		);

		$email->send();
	}
	
	/**
	 * Send a httperror, checks in order if errorcode(s) exist as styled error page, 
	 * else sends a 404 as backup.
	 * @param int/array $errorcodes
	 */
	public function sendHttpError($errorcodes){
		// make array if int or str
		if(!is_array($errorcodes)){ $errorcodes = array($errorcodes); }
		foreach($errorcodes as $errorcode){
			if($errorpage = ErrorPage::get()->filter('ErrorCode',$errorcode)->first()){
				$this->httpError($errorcode);
			}
		}
		// no styled errorpage found for errorcode
		$this->httpError(404);
	}
	
	//
	// Forms
	//

    /**
     * Factory for generating a change password form. The form can be expanded
     * using an extension class and calling the updateChangePasswordForm method.
     *
     * @return Form
     */
    public function ChangePasswordForm() {
        $form = ChangePasswordForm::create($this,"ChangePasswordForm");

        $form
            ->Actions()
            ->find("name","action_doChangePassword")
            ->addExtraClass("btn")
            ->addExtraClass("btn-green");

        $cancel_btn = LiteralField::create(
            "CancelLink",
            '<a href="' . $this->Link() . '" class="btn btn-red">'. _t("UserAccounts.CANCEL", "Cancel") .'</a>'
        );

        $form
            ->Actions()
            ->insertBefore($cancel_btn,"action_doChangePassword");

        $this->extend("updateChangePasswordForm", $form);

        return $form;
    }
	
    /**
     * Factory for generating a profile form. The form can be expanded using an
     * extension class and calling the updateAccountEditForm method.
     *
     * @return Form
     */
	public function accountCreateForm(){
		$form = UserAccount_CreateForm::create($this, "accountCreateForm");

        $this->extend("updateAccountCreateForm", $form);

        return $form;
	}
	
    /**
     * Factory for generating a profile form. The form can be expanded using an
     * extension class and calling the updateAccountEditForm method.
     *
     * @return Form
     */
	public function accountEditForm(){
		$form = UserAccount_EditForm::create($this, "accountEditForm");

        $this->extend("updateAccountEditForm", $form);

        return $form;
	}
	
    /**
     * Factory for generating a delete form. The form can be expanded using an
     * extension class and calling the updateAccountEditForm method.
     *
     * @return Form
     */
	public function accountDeleteForm(){
		$form = UserAccount_DeleteForm::create($this, "accountDeleteForm");

        $this->extend("updateAccountDeleteForm", $form);

        return $form;
	}


}

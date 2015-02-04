<?php

class UserAccount_EditForm extends Form {
	
    public function __construct($controller, $name = "UserAccount_EditForm") {
		
        $fields = new FieldList();
		
		//$fields = $this->member->getMemberFormFields();

        $fields->add(HiddenField::create("ID"));
        $fields->add(TextField::create("FirstName", _t('Member.FIRSTNAME',"First Name")));
        $fields->add(TextField::create("Surname", _t('Member.SURNAME',"Surname")));
        $fields->add(EmailField::create("Email", _t("Member.EMAIL","Email")));
		
		///// Adding an uploadfield directly works just fine, adding it via an extension doesn't alas...
		
//		$UploadField = UploadField::create("ProfileImage",'ProfileImage');
//		$UploadField->setTitle(_t (
//			'UserAccounts.PROFILEIMAGE',
//			'Profile Image'
//		));
//		$UploadField->setAllowedFileCategories( 'image' );
//		$UploadField->setFolderName( 'profileimages' );
//		$UploadField->setAllowedMaxFileNumber(1);
//		$UploadField->setCanAttachExisting(false); // Block access to Silverstripe assets library
//		$UploadField->setCanPreviewFolder(false); // Don't show target filesystem folder on upload field
//		
//		$UploadField->getUpload()->setReplaceFile(true);
//		$UploadField->setOverwriteWarning(false); // Don't bug uploader with 'file exists' messages
//
//		// Prevents the form thinking the current Page is the underlying object
//		$UploadField->relationAutoSetting = false;
//		
//		$fields->add($UploadField);
		
		/////
		
		// extend from the original controller as well to keep things simple...
        $controller->extend("updateUserAccountFormFields", $fields);
		
		// extra hook to update fields from extensions on Member
//		if( $this->edited_account ){
//			$this->edited_account->extend("updateUserAccountFormFields", $fields);
//		}

        $cancel_url = Controller::join_links($controller->Link());

        $actions = new FieldList(
            LiteralField::create(
                "cancelLink",
                '<a class="btn btn-red" href="'.$cancel_url.'">'. _t("UserAccounts.CANCEL", "Cancel") .'</a>'
            ),
            FormAction::create("doUpdate",_t("CMSMain.SAVE", "Save"))
//                ->addExtraClass("btn")
//                ->addExtraClass("btn-green")
        );

        $controller->extend("updateEditFormActions", $actions);

        $required = new RequiredFields(array(
            "FirstName",
            "Surname",
            "Email"
        ));

        $controller->extend("updateEditRequiredFields", $required);

        parent::__construct($controller, $name, $fields, $actions, $required);
    }

    /**
     * Register a new member or change an existing
     *
     * @param array $data User submitted data
     * @param Form $form The used form
     */
    public function doUpdate($data, $form) {
		$filter = array();
		$member = Member::get()->byID($data["ID"]);

		// Check that a member isn't trying to mess up another users profile
		if ($member && ($data["ID"] == Member::currentUserID() || $member->canEdit(Member::currentUser()))) {
			// check if e-mail isn't used yet
			$email = Convert::raw2sql($data['Email']);
			$existingmember = Member::get()->filter('Email', $email)->first();
			if ( $existingmember && $existingmember->ID != $data["ID"]) {
				// Add a error message
				$form->addErrorMessage("Email", 
						_t("UserAccounts.EMAILEXISTS",
						'Sorry, that email address already exists. Please choose another.'), "bad");

				// Load errors into session and post back
				Session::set("FormInfo.Form_Form.data", $data);

				// Redirect back to form
				return $this->controller->redirectBack();
			}
			// Load into member
			$form->saveInto($member);
			
			// Send update notification (before writing so we can check which fields have changed
			if (Config::inst()->get('UserAccounts', 'send_frontend_update_notifications')) {
				$this->controller->sendUpdateNotification($member);
			}
			
			// Proceed to save...
			$member->write();
			$form->sessionMessage(_t("UserAccounts.DETAILSUPDATED", 
					"Your details have been updated."), "good");
			
			
			return $this->controller->redirectBack();
			//return $this->controller->redirect($this->controller->Link());
		} else {
			$form->sessionMessage(
					"error", _t("UserAccounts.CANNOTEDIT", "You cannot edit this account")
			);

			return $this->controller->redirectBack();
		}
	}
		
}

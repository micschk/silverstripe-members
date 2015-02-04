<?php

class UserAccount_DeleteForm extends Form {
	
    public function __construct($controller, $name = "UserAccount_DeleteForm") {
		
        $fields = new FieldList();
		
		//$fields = $this->member->getMemberFormFields();

        $fields->add(HiddenField::create("ID"));
		$fields->add(TextField::create("FirstName", _t('Member.FIRSTNAME',"First Name"))->setReadonly(true));
        $fields->add(TextField::create("Surname", _t('Member.SURNAME',"Surname"))->setReadonly(true));
		$fields->add(LiteralField::create("sure",
				_t("UserAccounts.SUREREMOVE", "Are you sure you want to remove this account?")));
		
		// extend from the original controller as well to keep things simple...
        $controller->extend("updateUserDeleteFormFields", $fields);
        $cancel_url = Controller::join_links($controller->Link());

        $actions = new FieldList(
            LiteralField::create(
                "cancelLink",
                '<a class="btn" href="'.$cancel_url.'">'. _t("UserAccounts.CANCEL", "Cancel") .'</a>'
            ),
            FormAction::create("doRemove",_t("CMSMain.REMOVE", "Remove"))
                ->addExtraClass("btn")
                ->addExtraClass("btn-red")
        );

        $controller->extend("updateDeleteFormActions", $actions);

        parent::__construct($controller, $name, $fields, $actions);
    }

    /**
     * Register a new member or change an existing
     *
     * @param array $data User submitted data
     * @param Form $form The used form
     */
    public function doRemove($data, $form) {
		$filter = array();
		$member = Member::get()->byID($data["ID"]);

		// Check that a member isn't trying to mess up another users profile
		if ($member && ($data["ID"] == Member::currentUserID() || $member->canEdit(Member::currentUser()))) {
			
			// Send update notification (before writing so we can check which fields have changed
			if (Config::inst()->get('UserAccounts', 'send_frontend_update_notifications')) {
				$this->controller->sendDeleteNotification($member);
			}
			
			// Proceed to remove...
			$member->delete();
			
			//@TODO: give some kind of feedback at this point if the user's removed his own account & log him out.
//			$form->sessionMessage(_t("UserAccounts.ACCOUNTREMOVED", 
//					"Account was removed."), "good");
			
			//return $this->controller->redirectBack();
			return $this->controller->redirect($this->controller->Link());
		} else {
			$form->sessionMessage(
					"error", _t("UserAccounts.CANNOTEDIT", "You cannot edit this account")
			);

			return $this->controller->redirectBack();
		}
	}
		
}

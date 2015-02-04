<?php

class UserAccount_Member_Extension extends DataExtension{
	
	public function ProfileLink($action = null) {
		if($directorypage = FrontUserPage::get()->first()){
			return Controller::join_links(
				$directorypage->Link(),
				"view",
				$this->owner->ID,
				$action
			);
		}
		return Controller::join_links(
			MemberProfilePage_Controller::config()->url_segment,
			$action
		);
	}

	//allow content editors to CVED (CRUD)

	public function canCreate($member = null) {
		if(Permission::check("CMS_ACCESS_CMSMain")){
			return true;
		}
	}

	public function canView($member = null) {
		if(Permission::check("CMS_ACCESS_CMSMain")){
			return true;
		}
	}

	public function canEdit($member = null) {
		if(Permission::check("CMS_ACCESS_CMSMain")){
			return true;
		}
	}

	public function canDelete($member = null) {
		if(Permission::check("CMS_ACCESS_CMSMain")){
			return true;
		}
	}

}
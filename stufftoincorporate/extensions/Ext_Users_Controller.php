<?php
/**
 * Extension for Controller that provide methods such as member management
 * interface to templates
 *
 * @package users
 */
class Ext_Users_Controller extends Extension {

    /**
     * Render current user account nav
     *
     * @return string
     */
    public function getUserAccountNav() {
        return $this->owner->renderWith("Users_AccountNav");
    }
	
	public function setFlashMessage($status, $message){
		Session::set('FlashStatus', $status);
		Session::set('FlashMessage', $message);
	}
	
	public function FlashMessage() {
		if(Session::get('FlashMessage')) {
			$message = Session::get('FlashMessage');
			$status = Session::get('FlashStatus');
			Session::clear('FlashStatus');
			Session::clear('FlashMessage');
			return new ArrayData(array('Message' => $message, 'Status' => $status));
		}
		return false;
	}

}

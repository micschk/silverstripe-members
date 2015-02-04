<?php

/**
 * TODO: some extra privilege-checking on uploaded files to prevent accessing other uploader's files...
 * As described in some of the links below
 */

/**
 * References on getting UF to work on front end/when added via extension
 * https://www.cwp.govt.nz/guides/core-technical-documentation/framework/en/reference/uploadfield
 * https://github.com/silverstripe/silverstripe-framework/pull/1862/files
 * http://doc.silverstripe.org/en/developer_guides/extending/extensions
 */

/** From uploadfield README.md:
+## Using the UploadField in a frontend form
+
+The UploadField can be used in a frontend form, given that sufficient attention is given
+to the permissions granted to non-authorised users.
+
+By default Image::canDelete and Image::canEdit do not require admin privileges, so 
+make sure you override the methods in your Image extension class.
+
+For instance, to generate an upload form suitable for saving images into a user-defined
+gallery the below code could be used:
+
+	:::php
+
+	// In GalleryPage.php
+	class GalleryPage extends Page {}
+	class GalleryPage_Controller extends Page_Controller {
+		public function Form() {
+			$fields = new FieldList(
+				new TextField('Title', 'Title', null, 255),
+				$field = new UploadField('Images', 'Upload Images')
+			); 
+			$field->setCanAttachExisting(false); // Block access to Silverstripe assets library
+			$field->setCanPreviewFolder(false); // Don't show target filesystem folder on upload field
+			$field->relationAutoSetting = false; // Prevents the form thinking the GalleryPage is the underlying object
+			$actions = new FieldList(new FormAction('submit', 'Save Images'));
+			return new Form($this, 'Form', $fields, $actions, null);
+		}
 
+		public function submit($data, Form $form) {
+			$gallery = new Gallery();
+			$form->saveInto($gallery);
+			$gallery->write();
+			return $this;
+		}
+	}
+
+	// In Gallery.php
+	class Gallery extends DataObject {	
+		private static $db = array(
+			'Title' => 'Varchar(255)'
+		);
+
+		private static $many_many = array(
+			'Images' => 'Image'
+		);
+	}
 
+	// In ImageExtension.php
+	class ImageExtension extends DataExtension {
+
+		private static $belongs_many_many = array(
+			'Gallery' => 'Gallery'
+		);
+
+		function canEdit($member) {
+			// This part is important!
+			return Permission::check('ADMIN');
+		}
+	}
+	Image::add_extension('ImageExtension');
 */

class UserAccount_MemberProfilePic_Extension extends DataExtension{

	private static $has_one = array(
		'ProfileImage' => 'Image',
	);
	
	public function updateCMSFields(FieldList $fields) {
		
		// Field already gets 'scaffolded' in by SS
		//$fields->addFieldToTab('Root.Main', $this->getProfileImageField());
		//$UploadField = $fields->dataFieldByName('ProfileImage');
		$fields->replaceField('ProfileImage', 
				UserAccount_MemberProfilePic_Extension::getProfileImageField());
		
	}
	
	// Static so we can call it from UserAccount_Controller_ProfilePic_Extension
	public static function getProfileImageField(){
		//UploadField::create('Photo','', File::get()->byID($data['Photo']))
		$UploadField = UploadField::create("ProfileImage",'ProfileImage');
		$UploadField->setTitle(_t (
			'UserAccounts.PROFILEIMAGE',
			'Profile Image'
		));
		$UploadField->setAllowedFileCategories( 'image' );
		$UploadField->setFolderName( 'profileimages' );
		$UploadField->setAllowedMaxFileNumber(1);
		
		return $UploadField;
		
	}
	
}

// extra class to decorate controller because UploadField runs into errors when aplied from Member decorator
class UserAccount_ProfilePic_Controller_Extension extends Extension{
	
	public function updateUserAccountFormFields(FieldList $fields) {
		
		$UploadField = UserAccount_MemberProfilePic_Extension::getProfileImageField();
		
		$UploadField->setCanAttachExisting(false); // Block access to Silverstripe assets library
		$UploadField->setCanPreviewFolder(false); // Don't show target filesystem folder on upload field
		
		$UploadField->getUpload()->setReplaceFile(true);
		$UploadField->setOverwriteWarning(false); // Don't bug uploader with 'file exists' messages

		// Prevents the form thinking the current Page is the underlying object 
		// (results in 404's on actions etc if true and Uploadfield's added from extension)
		$UploadField->relationAutoSetting = false;
		// set the record to save on to the member being decorated -- $this->onwer points to the controller
		// calling this hook, not the member. Not necessary anyway, as the controller takes care of saving
		// the form into currently edited member.
		//$UploadField->setRecord($this->owner);
		
		// Add memberID to uploadfield so members cannot overwrite eachothers files;
		$editedAccountID = $this->owner->edited_account_id;
		$UploadField->setFolderName( 'profileimages/'.$editedAccountID );
		
		$fields->push($UploadField);
		
	}
	
}
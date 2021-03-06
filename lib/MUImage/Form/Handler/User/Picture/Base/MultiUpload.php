<?php
/**
 * MUImage.
 *
 * @copyright Michael Ueberschaer
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @package MUImage
 * @author Michael Ueberschaer <kontakt@webdesign-in-bremen.com>.
 * @link http://www.webdesign-in-bremen.com
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.5.4 (http://modulestudio.de) at Thu Feb 23 22:43:24 CET 2012.
 */

/**
 * This handler class handles the page events of the Form called by the MUImage_user_edit() function.
 * It aims on the picture object type.
 *
 * More documentation is provided in the parent class.
 */
class MUImage_Form_Handler_User_Picture_Base_MultiUpload extends MUImage_Form_Handler_User_Edit
{
	/**
	 * Persistent member vars
	 */

	/**
	 * Pre-initialise hook.
	 *
	 * @return void
	 */
	public function preInitialize()
	{
		parent::preInitialize();

		$this->objectType = 'picture';
		$this->objectTypeCapital = 'Picture';
		$this->objectTypeLower = 'picture';
		$this->objectTypeLowerMultiple = 'pictures';

		$this->hasPageLockSupport = true;
		$this->hasCategories = false;
		// array with upload fields and mandatory flags
		$this->uploadFields = array('imageUpload' => false);
	}

	/**
	 * Initialize form handler.
	 *
	 * This method takes care of all necessary initialisation of our data and form states.
	 *
	 * @return boolean False in case of initialization errors, otherwise true.
	 */
	public function initialize(Zikula_Form_View $view)
	{
		$dom = ZLanguage::getModuleDomain('MUImage');
			
		SessionUtil::delVar('muimagepictureids');
		
		$allowedFields = MUImage_Util_Controller::allowedFields();
		
		$fileSize = MUImage_Util_Controller::maxSize();
		
		// we check for required width for pictures
		$minWidth = MUImage_Util_Controller::minWidth();
			
		$this->view->assign('allowedFields', $allowedFields)
		           ->assign('minWidth', $minWidth)
		           ->assign('fileSize', $fileSize);

		// everything okay, no initialization errors occured
		return true;
	}

	/**
	 * Post-initialise hook.
	 *
	 * @return void
	 */
	public function postInitialize()
	{
		parent::postInitialize();
	}

	/**
	 * Get list of allowed redirect codes.
	 */
	protected function getRedirectCodes()
	{
		// admin list of albums
		$codes[] = 'adminViewAlbum';
		// admin display page of treated album
		$codes[] = 'adminDisplayAlbum';
		// user list of albums
		$codes[] = 'userViewAlbum';
		// user display page of treated album
		$codes[] = 'userDisplayAlbum';
		return $codes;
	}

	/**
	 * Get the default redirect url. Required if no returnTo parameter has been supplied.
	 * This method is called in handleCommand so we know which command has been performed.
	 */
	protected function getDefaultReturnUrl($args, $obj)
	{
		// redirect to the list of pictures
		$viewArgs = array('ot' => $this->objectType);
		$url = ModUtil::url($this->name, 'user', 'view', $viewArgs);

		if ($args['commandName'] != 'delete' && !($this->mode == 'create' && $args['commandName'] == 'cancel')) {
			// redirect to the detail page of treated picture
			$url = ModUtil::url($this->name, 'user', 'display', array('ot' => 'picture', 'id' => $this->idValues['id']));
		}
		return $url;
	}

	/**
	 * Command event handler.
	 *
	 * This event handler is called when a command is issued by the user.
	 */
	public function HandleCommand(Zikula_Form_View $view, &$args)
	{
		$albumid = $this->request->getGet()->filter('album', 0, FILTER_SANITIZE_NUMBER_INT);
			
		if ($args['commandName'] == 'create') {

			$entityClass = $this->name . '_Entity_' . ucfirst($this->objectType);
			$repository = $this->entityManager->getRepository($entityClass);

			// fetch posted data input values as an associative array
			$formData = $this->view->getValues();
			$data = $formData[$this->objectTypeLower];

			foreach ($data as $key => $value) {
				if ($value['size'] > 0) {

					$entity = new MUImage_Entity_Picture();

					$entityData = array($key => $value);

					$this->uploadFields = array($key => false);
					//$upload
					$entityData = $this->handleUploads($entityData, $entity);
                    // if upload failed go to next file to upload
					if ($entityData == false) {
						continue;
					}

					// save the entered datas to the allowed upload field
					$entityData['imageUpload'] = $entityData[$key];
					unset($entityData[$key]);
					$entityData['imageUploadMeta'] = $entityData[$key . 'Meta'];
					unset($entityData[$key . 'Meta']);

					// get the selected album as object
					$albumrepository = MUImage_Util_Model::getAlbumRepository();
					$album = $albumrepository->selectById($albumid);
					$entityData['Album'] = $album;

					// set a default title and the correct data for imageupload
					$entity->setTitle($this->__('Please enter title...'));
					$entity->setImageUpload($entityData['imageUpload']);

					// assign fetched data
					$entity->merge($entityData);

					// save updated entity
					$this->entityRef = $entity;

					$this->performUpdate($args);

					$success = true;

					// default message
					$this->addDefaultMessage($args, $success);

				}
				else {
					continue;
				}
			}
			$pictureids = SessionUtil::getVar('muimagepictureids');
			$pictures = unserialize($pictureids);
			$id = $pictures[0];
			$url = ModUtil::url($this->name, 'user', 'editMulti', array('ot' => 'picture', 'id' => $id, 'album' => $albumid));
			return $this->view->redirect($url);

		}
			
		if ($args['commandName'] == 'cancel') {
			$url = ModUtil::url($this->name, 'user', 'display', array('ot' => 'album', 'id' => $albumid));
			return $this->view->redirect($url);

		}
	}

	/**
	 * Get success or error message for default operations.
	 *
	 * @param Array   $args    arguments from handleCommand method.
	 * @param Boolean $success true if this is a success, false for default error.
	 * @return String desired status or error message.
	 */
	protected function getDefaultMessage($args, $success = false)
	{
		if ($success !== true) {
			return parent::getDefaultMessage($args, $success);
		}

		$message = '';
		switch ($args['commandName']) {
			case 'create':
				$message = $this->__('Done! Image uploaded.');
				break;
			case 'update':
				$message = $this->__('Done! Picture updated.');
				break;
			case 'update':
				$message = $this->__('Done! Picture deleted.');
				break;
		}
		return $message;
	}

	/**
	 * Add success or error message to session.
	 *
	 * @param Array   $args    arguments from handleCommand method.
	 * @param Boolean $success true if this is a success, false for default error.
	 */
	protected function addDefaultMessage($args, $success = false)
	{
		$message = $this->getDefaultMessage($args, $success);
		if (!empty($message)) {
			if ($success === true) {
				LogUtil::registerStatus($message);
			} else {
				LogUtil::registerError($message);
			}
		}
	}

	/**
	 * Input data processing called by handleCommand method.
	 */
	public function fetchInputData(Zikula_Form_View $view, &$args)
	{

		// get treated entity reference from persisted member var
		$entity = $this->entityRef;

		$entityData = array();

		$this->reassignRelatedObjects();
		$entityData['Album'] = ((isset($selectedRelations['album'])) ? $selectedRelations['album'] : $this->retrieveRelatedObjects('album', 'muimageAlbum_AlbumItemList', false, 'POST'));

		// assign fetched data
		if (count($entityData) > 0) {
			$entity->merge($entityData);
		}

		// save updated entity
		$this->entityRef = $entity;
	}
	/**
	 * Executing insert and update statements
	 *
	 * @param Array   $args    arguments from handleCommand method.
	 */
	public function performUpdate($args)
	{
		// get treated entity reference from persisted member var
		$entity = $this->entityRef;

		$this->updateRelationLinks($entity);
		//$this->entityManager->transactional(function($entityManager) {
		$this->entityManager->persist($entity);
		$this->entityManager->flush();
		//});
	}

	/**
	 * Get url to redirect to.
	 */
	private function getRedirectUrl($args, $obj, $repeatCreateAction = false)
	{
		if ($this->inlineUsage == true) {
			$urlArgs = array('idp' => $this->idPrefix,
                'com' => $args['commandName']);
			$urlArgs = $this->addIdentifiersToUrlArgs($urlArgs);
			// inline usage, return to special function for closing the Zikula.UI.Window instance
			return ModUtil::url($this->name, 'user', 'handleInlineRedirect', $urlArgs);
		}

		if ($repeatCreateAction) {
			return $this->repeatReturnUrl;
		}

		// normal usage, compute return url from given redirect code
		if (!in_array($this->returnTo, $this->getRedirectCodes())) {
			// invalid return code, so return the default url
			return $this->getDefaultReturnUrl($args, $obj);
		}

		// parse given redirect code and return corresponding url
		switch ($this->returnTo) {
			case 'admin':
				return ModUtil::url($this->name, 'admin', 'main');
			case 'adminView':
				return ModUtil::url($this->name, 'admin', 'view',
				array('ot' => $this->objectType));
			case 'adminDisplay':
				if ($args['commandName'] != 'delete' && !($this->mode == 'create' && $args['commandName'] == 'cancel')) {
					return ModUtil::url($this->name, 'admin', $this->addIdentifiersToUrlArgs());
				}
				return $this->getDefaultReturnUrl($args, $obj);
			case 'user':
				return ModUtil::url($this->name, 'user', 'main');
			case 'userView':
				return ModUtil::url($this->name, 'user', 'view',
				array('ot' => $this->objectType));
			case 'userDisplay':
				if ($args['commandName'] != 'delete' && !($this->mode == 'create' && $args['commandName'] == 'cancel')) {
					return ModUtil::url($this->name, 'user', $this->addIdentifiersToUrlArgs());
				}
				return $this->getDefaultReturnUrl($args, $obj);
			case 'adminViewAlbum':
				return ModUtil::url($this->name, 'admin', 'view',
				array('ot' => 'album'));
			case 'adminDisplayAlbum':
				if (!empty($this->album)) {
					return ModUtil::url($this->name, 'admin', 'display', array('ot' => 'album', 'id' => $this->album));
				}
				return $this->getDefaultReturnUrl($args, $obj);
			case 'userViewAlbum':
				return ModUtil::url($this->name, 'user', 'view',
				array('ot' => 'album'));
			case 'userDisplayAlbum':
				if (!empty($this->album)) {
					return ModUtil::url($this->name, 'user', 'display', array('ot' => 'album', 'id' => $this->album));
				}
				return $this->getDefaultReturnUrl($args, $obj);
			default:
				return $this->getDefaultReturnUrl($args, $obj);
		}
	}


	/**
	 * Reassign options chosen by the user to avoid unwanted form state resets.
	 * Necessary until issue #23 is solved.
	 */
	public function reassignRelatedObjects()
	{
		$selectedRelations = array();
		// reassign the album eventually chosen by the user
		$selectedRelations['album'] = $this->retrieveRelatedObjects('album', 'muimageAlbum_AlbumItemList', false, 'POST');
		$this->view->assign('selectedRelations', $selectedRelations);
	}
	/**
	 * Helper method for updating links to related records.
	 */
	protected function updateRelationLinks($entity)
	{
	}

	/**
	 * Helper method to process upload fields
	 */
	protected function handleUploads($formData, $existingObject)
	{
		if (!count($this->uploadFields)) {
			return $formData;
		}

		// initialise the upload handler
		$uploadManager = new MUImage_UploadHandler();
		$existingObjectData = $existingObject->toArray();

		// process all fields
		foreach ($this->uploadFields as $uploadField => $isMandatory) {
			// check if an existing file must be deleted
			$hasOldFile = (!empty($existingObjectData[$uploadField]));
			$hasBeenDeleted = !$hasOldFile;
			if ($this->mode != 'create') {
				if (isset($formData[$uploadField . 'DeleteFile'])) {
					if ($hasOldFile && $formData[$uploadField . 'DeleteFile'] === true) {
						// remove upload file (and image thumbnails)
						$existingObjectData = $uploadManager->deleteUploadFile($this->objectType, $existingObjectData, $uploadField);
						if (empty($existingObjectData[$uploadField])) {
							$existingObject[$uploadField] = '';
						}
					}
					unset($formData[$uploadField . 'DeleteFile']);
					$hasBeenDeleted = true;
				}
			}

			// look whether a file has been provided
			if (!$formData[$uploadField] || $formData[$uploadField]['size'] == 0) {
				// no file has been uploaded
				unset($formData[$uploadField]);
				// skip to next one
				continue;
			}

			if ($hasOldFile && $hasBeenDeleted !== true && $this->mode != 'create') {
				// remove old upload file (and image thumbnails)
				$existingObjectData = $uploadManager->deleteUploadFile($this->objectType, $existingObjectData, $uploadField);
				if (empty($existingObjectData[$uploadField])) {
					$existingObject[$uploadField] = '';
				}
			}

			// do the actual upload (includes validation, physical file processing and reading meta data)
			$uploadResult = $uploadManager->performFileUpload($this->objectType, $formData, $uploadField);
			if ($uploadResult == false) {
				return false;
			}
			// assign the upload file name
			$formData[$uploadField] = $uploadResult['fileName'];
			// assign the meta data
			$formData[$uploadField . 'Meta'] = $uploadResult['metaData'];

			// if current field is mandatory check if everything has been done
			if ($isMandatory && $formData[$uploadField] === false) {
				// mandatory upload has not been completed successfully
				return false;
			}

			// upload succeeded
		}

		return $formData;
	}
}

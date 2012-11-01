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
 * @version Generated by ModuleStudio 0.5.4 (http://modulestudio.de) at Sun Feb 19 15:20:07 CET 2012.
 */

/**
 * This is the User controller class providing navigation and interaction functionality.
 */
class MUImage_Controller_User extends MUImage_Controller_Base_User
{
	/**
	 * This method provides a generic item detail view.
	 *
	 * @param string  $id           Check for entity
	 * @return parent function
	 */
	public function display($args)
	{
		$view = new Zikula_Request_Http();
		$id = $view->getGet()->filter('id', 0 , FILTER_SANITIZE_STRING);
		$ot = $view->getGet()->filter('ot','album' , FILTER_SANITIZE_STRING);
		// DEBUG: permission check aspect starts
		$this->throwForbiddenUnless(SecurityUtil::checkPermission('MUImage:Album:', $id.'::', ACCESS_READ));
		// DEBUG: permission check aspect ends
			
		if ($id != 0) {

			$count = MUImage_Util_View::countPictures();
			$count2 = MUImage_Util_View::countAlbums();

			$this->view->assign('numpictures', $count);
			$this->view->assign('numalbums', $count2);

		}
		// we get the picture object
		$picturerepository = MUImage_Util_Model::getPictureRepository();
		$picture = $picturerepository->selectById($id);
		// if object is a picture, we want to count views, the picture id is not the actuel userid
		// or the user is not loggedin we add to 1 to view
		if ($ot == 'picture' && ModUtil::getVar('MUImage', 'countImageView') == true && ($picture->getCreatedUserId() != $coredata.user.uid || UserUtil::isLoggedIn() == false)) {
			$picture->setImageView($picture->getImageView() + 1);

			$serviceManager = $this->getServiceManager();
			$entityManager = $serviceManager->getService('doctrine.entitymanager');

			$entityManager->flush();
		}
			
		return parent::display($args);
			
	}

	/**
	 * This method provides a generic item list overview.
	 *
	 * @param string  $objectType   Treated object type.
	 * @return parent function.
	 */
	public function view($args)
	{
		$objectType = (isset($args['ot']) && !empty($args['ot'])) ? $args['ot'] : $this->request->getGet()->filter('ot', 'album', FILTER_SANITIZE_STRING);
			
		if ($objectType == 'album') {
			// DEBUG: permission check aspect starts
			$this->throwForbiddenUnless(SecurityUtil::checkPermission('MUImage:Album:', '::', ACCESS_READ));
			// DEBUG: permission check aspect ends
		}
		if ($objectType == 'picture') {
			// DEBUG: permission check aspect starts
			$this->throwForbiddenUnless(SecurityUtil::checkPermission('MUImage:Picture:', '::', ACCESS_READ));
			// DEBUG: permission check aspect ends
		}

		if ($objectType == 'picture') {
			$url = ModUtil::url($this->name, 'user', 'view', array('ot' => 'album'));
			System::redirect($url);
		}

		return parent::view($args);
	}

	/**
	 * This is a custom method. Documentation for this will be improved in later versions.
	 *
	 * @return mixed Output.
	 */
	public function zipUpload($args)
	{
		// DEBUG: permission check aspect starts
		$this->throwForbiddenUnless(SecurityUtil::checkPermission('MUImage::', '::', ACCESS_ADD));
		// DEBUG: permission check aspect ends

		// parameter specifying which type of objects we are treating
		$objectType = (isset($args['ot']) && !empty($args['ot'])) ? $args['ot'] : $this->request->getGet()->filter('ot', 'picture', FILTER_SANITIZE_STRING);
		$utilArgs = array('controller' => 'user', 'action' => 'multiUpload');
		if (!in_array($objectType, MUImage_Util_Controller::getObjectTypes('controllerAction', $utilArgs))) {
			$objectType = MUImage_Util_Controller::getDefaultObjectType('controllerAction', $utilArgs);
		}
		// create new Form reference
		$view = FormUtil::newForm($this->name, $this);

		// build form handler class name
		$handlerClass = 'MUImage_Form_Handler_User_' . ucfirst($objectType) . '_ZipUpload';

		// execute form using supplied template and page event handler
		return $view->execute('user/' . $objectType . '/zipUpload.tpl', new $handlerClass());
	}

	/**
	 * This is a custom method. Documentation for this will be improved in later versions.
	 *
	 * @return mixed Output.
	 */
	public function multiUpload($args)
	{
		// DEBUG: permission check aspect starts
		$this->throwForbiddenUnless(SecurityUtil::checkPermission('MUImage::', '::', ACCESS_EDIT));
		// DEBUG: permission check aspect ends
		// parameter specifying which type of objects we are treating
		$objectType = (isset($args['ot']) && !empty($args['ot'])) ? $args['ot'] : $this->request->getGet()->filter('ot', 'picture', FILTER_SANITIZE_STRING);
		$utilArgs = array('controller' => 'user', 'action' => 'multiUpload');
		if (!in_array($objectType, MUImage_Util_Controller::getObjectTypes('controllerAction', $utilArgs))) {
			$objectType = MUImage_Util_Controller::getDefaultObjectType('controllerAction', $utilArgs);
		}
		// create new Form reference
		$view = FormUtil::newForm($this->name, $this);

		// build form handler class name
		$handlerClass = 'MUImage_Form_Handler_User_' . ucfirst($objectType) . '_MultiUpload';

		// execute form using supplied template and page event handler
		return $view->execute('user/' . $objectType . '/multiUpload.tpl', new $handlerClass());
	}

	/**
	 * This is a custom method. Documentation for this will be improved in later versions.
	 *
	 * @return mixed Output.
	 */
	public function editMulti($args)
	{

		// DEBUG: permission check aspect starts
		$this->throwForbiddenUnless(SecurityUtil::checkPermission('MUImage::', '::', ACCESS_EDIT));
		// DEBUG: permission check aspect ends
		// parameter specifying which type of objects we are treating
		$objectType = (isset($args['ot']) && !empty($args['ot'])) ? $args['ot'] : $this->request->getGet()->filter('ot', 'picture', FILTER_SANITIZE_STRING);
		$utilArgs = array('controller' => 'user', 'action' => 'editMulti');
		if (!in_array($objectType, MUImage_Util_Controller::getObjectTypes('controllerAction', $utilArgs))) {
			$objectType = MUImage_Util_Controller::getDefaultObjectType('controllerAction', $utilArgs);
		}
		// create new Form reference
		$view = FormUtil::newForm($this->name, $this);

		// build form handler class name
		$handlerClass = 'MUImage_Form_Handler_User_' . ucfirst($objectType) . '_EditMulti';

		// execute form using supplied template and page event handler
		return $view->execute('user/' . $objectType . '/editMulti.tpl', new $handlerClass());

	}

	/**
	 * This method provides a generic handling of simple delete requests.
	 *
	 * @param string  $ot           Treated object type.
	 * @param int     $id           Identifier of entity to be deleted.
	 * @param boolean $confirmation Confirm the deletion, else a confirmation page is displayed.
	 * @param string  $tpl          Name of alternative template (for alternative display options, feeds and xml output)
	 * @param boolean $raw          Optional way to display a template instead of fetching it (needed for standalone output)
	 * @return mixed Output.
	 */
	public function delete($args)
	{
		$id = $this->request->getGet()->filter('id' , 0, FILTER_SANITIZE_NUMBER_INT);
		$ot = $this->request->getGet()->filter('ot' , 'album', FILTER_SANITIZE_STRING);

		// we get the usergroups for the calling user
		$usergroups = (UserUtil::getGroupsForUser(UserUtil::getVar('uid')));

		if ($id > 0) {
			if ($ot == 'album') {
				$albumrepository = MUImage_Util_Model::getAlbumRepository();
				$album = $albumrepository->selectById($id);
				if ($album->getCreatedUserId() == UserUtil::getVar('uid')) {
					// nothing to do
				}
				else {
					// if user is no admin
					if (!in_array(2, $usergroups)) {
						$url = ModUtil::url($this->name, 'user' , 'display', array('ot' => 'album', 'id' => $id));
						LogUtil::registerError(__('You have no permissions to delete this album!'));
						System::redirect($url);
					}
				}
			}

			if ($ot == 'picture') {
				$picturerepository = MUImage_Util_Model::getPictureRepository();
				$picture = $picturerepository->selectById($id);
				if ($picture->getCreatedUserId() == UserUtil::getVar('uid')) {
					$album = $picture->getAlbum();
					$albumid = $album->getId();
					$this->view->assign('albumid', $albumid);
				}
				else {
					// if user is no admin
					if (!in_array(2, $usergroups)) {
						$url = ModUtil::url($this->name, 'user' , 'display', array('ot' => 'piture', 'id' => $id));
						LogUtil::registerError(__('You have no permissions to delete this picture!'));
						System::redirect($url);
					}
				}

			}

		}

		// TODO in next version MUImage_Util_View::checkForBlocksAndContent($id);

		return parent::delete($args);
	}
}

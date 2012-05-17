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
 * This is the Admin controller class providing navigation and interaction functionality.
 */
class MUImage_Controller_Admin extends MUImage_Controller_Base_Admin
{

	/**
	 * This method takes care of the application configuration.
	 *
	 * @return string Output
	 */
	public function album()
	{
		$this->throwForbiddenUnless(SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN));

		// Create new Form reference
		$view = FormUtil::newForm($this->name, $this);

		// Execute form using supplied template and page event handler
		return $view->execute('admin/album.tpl', new MUImage_Form_Handler_Admin_Base_Album());
	}

	/**
	 * This method takes care of the application configuration.
	 *
	 * @return string Output
	 */
	public function import()
	{
		$step = MUImage_Util_View::getStep();
		$this->throwForbiddenUnless(SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN));

		// Create new Form reference
		$view = FormUtil::newForm($this->name, $this);

		if ($step == 'first') {
			// Execute form using supplied template and page event handler
			return $view->execute('admin/import.tpl', new MUImage_Form_Handler_Admin_Base_Import());
		}

		if ($step == 'albums') {
			// Execute form using supplied template and page event handler
			return $view->execute('admin/albums.tpl', new MUImage_Form_Handler_Admin_Base_Import());
		}
	}
}

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
 * Installer implementation class
 */
class MUImage_Installer extends MUImage_Base_Installer
{
	/**
	 * Install the MUImage application.
	 *
	 * @return boolean True on success, or false.
	 */
	public function install()
	{
		parent::install();
		
		$this->setVar('ending', 'html');
		$this->setVar('deleteUserPictures', false);
		$this->setVar('minWidth', 400);
		$this->setVar('pageSizeAdminAlbums', 10);
		$this->setVar('pageSizeAdminPictures', 10);
		 
		// Set up module hooks
		HookUtil::registerProviderBundles($this->version->getHookProviderBundles());
		
		return true;
	}
	
	/**
	 * Upgrade the MUImage application from an older version.
	 *
	 * If the upgrade fails at some point, it returns the last upgraded version.
	 *
	 * @param integer $oldversion Version to upgrade from.
	 *
	 * @return boolean True on success, false otherwise.
	 */
	public function upgrade($oldversion)
	{
		
		 // Upgrade dependent on old version number
		switch ($oldversion) {
		case '1.0.0':
			$this->setVar('ending', 'html');
			$this->setVar('deleteUserPictures', false);
			$this->setVar('minWidth', 400);
		
		case '1.1.0':
			$this->setVar('pageSizeAdminAlbums', 10);
			$this->setVar('pageSizeAdminPictures', 10);
		
		case '1.2.0':			
			
			// later updates
			
		// do something
		// ...
		// update the database schema
		/*try {
		DoctrineHelper::updateSchema($this->entityManager, $this->listEntityClasses());
		} catch (Exception $e) {
		if (System::isDevelopmentMode()) {
		LogUtil::registerError($this->__('Doctrine Exception: ') . $e->getMessage());
		}
		return LogUtil::registerError($this->__f('An error was encountered while dropping the tables for the %s module.', array($this->getName())));
		}*/
		}
		
	
		// update successful
		return true;
	}
}

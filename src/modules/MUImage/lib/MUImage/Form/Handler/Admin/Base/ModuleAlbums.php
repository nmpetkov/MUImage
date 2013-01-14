
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
 * Configuration handler base class
 */
class MUImage_Form_Handler_Admin_Base_ModuleAlbums extends Zikula_Form_AbstractHandler
{
	/**
	 * Post construction hook.
	 *
	 * @return mixed
	 */
	public function setup()
	{
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
		// permission check
		if (!SecurityUtil::checkPermission('MUImage::', '::', ACCESS_ADMIN)) {
			return $view->registerError(LogUtil::registerPermissionError());
		}

		// custom initialisation aspects
		$this->initializeAdditions();

		// everything okay, no initialization errors occured
		return true;
	}

	/**
	 * Method stub for own additions in subclasses.
	 */
	protected function initializeAdditions()
	{
		$view = new Zikula_Request_Http();

		$module = $view->getGet()->filter('importmodule', '', FILTER_SANITIZE_STRING);
		// call api for albums
		$relevantalbums = ModUtil::apiFunc('MUImage', 'import', 'getAlbumNames', array('importmodule' => $module));

		$modulealbums = array();

		if (is_array($relevantalbums)) {
			foreach($relevantalbums as $album) {
				$modulealbums[] = array('value' => $album['ms_id'], 'text' => $album['ms_title']);
				$albums = $this->view->get_template_vars('albums');
				$albums['albumItems'] = $modulealbums;
				$this->view->assign('albums', $albums);
				$this->view->assign('module', $module);	
			}
		}
		else {
			LogUtil::registerError(__('Sorry. Could not load any albums for this module!'));
		}



	}

	/**
	 * Pre-initialise hook.
	 *
	 * @return void
	 */
	public function preInitialize()
	{
	}

	/**
	 * Post-initialise hook.
	 *
	 * @return void
	 */
	public function postInitialize()
	{
	}

	/**
	 * Command event handler.
	 *
	 * This event handler is called when a command is issued by the user. Commands are typically something
	 * that originates from a {@link Zikula_Form_Plugin_Button} plugin. The passed args contains different properties
	 * depending on the command source, but you should at least find a <var>$args['commandName']</var>
	 * value indicating the name of the command. The command name is normally specified by the plugin
	 * that initiated the command.
	 * @see Zikula_Form_Plugin_Button
	 * @see Zikula_Form_Plugin_ImageButton
	 */
	public function handleCommand(Zikula_Form_View $view, &$args)
	{
		$view = new Zikula_Request_Http();
		
		$module = $view->getGet()->filter('importmodule', '', FILTER_SANITIZE_STRING);
		
		$dom = ZLanguage::getModuleDomain($this->name);

		if ($args['commandName'] == 'start') {
			// check if all fields are valid
			if (!$this->view->isValid()) {
				return false;
			}

			// retrieve form data
			$data = $this->view->getValues();
				
			// check if existing supporters deleting
			$arguments['module'] = $module;
			$arguments['album'] = $data['albums']['album'];
			$arguments['folder'] = $data['albums']['folder'];
			if (!file_exists($arguments['folder'])) {
				LogUtil::registerError('Sorry! The directory does not exist!', $dom);
				// redirect back to the import page
				$url = ModUtil::url('MUImage', 'admin', 'import');
				return $this->view->redirect($url);
				
			}

			// call api for import
			if ($arguments['module'] != '' && $arguments['album'] > 0 && $arguments['folder'] != '') {
            $result = ModUtil::apiFunc($this->name, 'import', 'handleImport', $arguments);
            if ($result === true) {
            	LogUtil::registerStatus(__('Success! The album is imported!', $dom));
            }
            else {
            	LogUtil::registerError(LogUtil::registerError(__('Sorry! The album is not imported!', $dom)));
            }
			}
			else {
				LogUtil::registerError(__('no such album', $dom));
			}

			LogUtil::registerStatus($this->__('Done! Module import complete.'));
		}
			
			if ($args['commandName'] == 'cancel') {

			// redirect back to the main page
			$url = ModUtil::url('MUImage', 'admin', 'main');
			return $this->view->redirect($url);
			}


	}
}

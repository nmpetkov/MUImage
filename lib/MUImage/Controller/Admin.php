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
	 * This method provides a generic item list overview.
	 *
	 * @param string  $ot           Treated object type.
	 * @param string  $sort         Sorting field.
	 * @param string  $sortdir      Sorting direction.
	 * @param int     $pos          Current pager position.
	 * @param int     $num          Amount of entries to display.
	 * @param string  $tpl          Name of alternative template (for alternative display options, feeds and xml output)
	 * @param boolean $raw          Optional way to display a template instead of fetching it (needed for standalone output)
	 * @return mixed Output.
	 */
	public function view($args)
	{
		// DEBUG: permission check aspect starts
		$this->throwForbiddenUnless(SecurityUtil::checkPermission('MUImage::', '::', ACCESS_ADMIN));
		// DEBUG: permission check aspect ends
	
		// parameter specifying which type of objects we are treating
		$objectType = (isset($args['ot']) && !empty($args['ot'])) ? $args['ot'] : $this->request->getGet()->filter('ot', 'album', FILTER_SANITIZE_STRING);
		$utilArgs = array('controller' => 'admin', 'action' => 'view');
		if (!in_array($objectType, MUImage_Util_Controller::getObjectTypes('controllerAction', $utilArgs))) {
			$objectType = MUImage_Util_Controller::getDefaultObjectType('controllerAction', $utilArgs);
		}
		$repository = $this->entityManager->getRepository('MUImage_Entity_' . ucfirst($objectType));
	
		$tpl = (isset($args['tpl']) && !empty($args['tpl'])) ? $args['tpl'] : $this->request->getGet()->filter('tpl', '', FILTER_SANITIZE_STRING);
		if ($tpl == 'tree') {
			$trees = ModUtil::apiFunc($this->name, 'selection', 'getAllTrees', array('ot' => $objectType));
			$this->view->assign('trees', $trees)
			->assign($repository->getAdditionalTemplateParameters('controllerAction', $utilArgs));
			// fetch and return the appropriate template
			return MUImage_Util_View::processTemplate($this->view, 'admin', $objectType, 'view', $args);
		}
	
		// parameter for used sorting field
		$sort = (isset($args['sort']) && !empty($args['sort'])) ? $args['sort'] : $this->request->getGet()->filter('sort', '', FILTER_SANITIZE_STRING);
		if (empty($sort) || !in_array($sort, $repository->getAllowedSortingFields())) {
			$sort = $repository->getDefaultSortingField();
		}
	
		// parameter for used sort order
		$sdir = (isset($args['sortdir']) && !empty($args['sortdir'])) ? $args['sortdir'] : $this->request->getGet()->filter('sortdir', '', FILTER_SANITIZE_STRING);
		$sdir = strtolower($sdir);
		if ($sdir != 'asc' && $sdir != 'desc') {
			$sdir = 'asc';
		}
	
		// convenience vars to make code clearer
		$currentUrlArgs = array('ot' => $objectType);
	
		$selectionArgs = array(
				'ot'      => $objectType,
				'where'   => '',
				'orderBy' => $sort . ' ' . $sdir
		);
	
		$showAllEntries = (int)(isset($args['all']) && !empty($args['all'])) ? $args['all'] : $this->request->getGet()->filter('all', 0, FILTER_VALIDATE_INT);
		$this->view->assign('showAllEntries', $showAllEntries);
		if ($showAllEntries == 1) {
			// item list without pagination
			$entities = ModUtil::apiFunc($this->name, 'selection', 'getEntities', $selectionArgs);
			$objectCount = count($entities);
			$currentUrlArgs['all'] = 1;
		} else {
			// item list with pagination
	
			// the current offset which is used to calculate the pagination
			$currentPage = (int)(isset($args['pos']) && !empty($args['pos'])) ? $args['pos'] : $this->request->getGet()->filter('pos', 1, FILTER_VALIDATE_INT);
	
			// the number of items displayed on a page for pagination
			$resultsPerPage = (int)(isset($args['num']) && !empty($args['num'])) ? $args['num'] : $this->request->getGet()->filter('num', 0, FILTER_VALIDATE_INT);
			if ($resultsPerPage == 0) {
				$csv = (int)(isset($args['usecsv']) && !empty($args['usecsv'])) ? $args['usecsv'] : $this->request->getGet()->filter('usecsvext', 0, FILTER_VALIDATE_INT);
				$resultsPerPage = ($csv == 1) ? 999999 : $this->getVar('pagesize', 10);
			}
			
			// we get the pagesize for albums
			if ($objectType == 'album') {
				$resultsPerPage = ModUtil::getVar($this->name, 'pageSizeAdminAlbums');
			}
			
			if ($objectType == 'picture') {
				$resultsPerPage = ModUtil::getVar($this->name, 'pageSizeAdminPictures');
			}
	
			$selectionArgs['currentPage'] = $currentPage;
			$selectionArgs['resultsPerPage'] = $resultsPerPage;
			list($entities, $objectCount) = ModUtil::apiFunc($this->name, 'selection', 'getEntitiesPaginated', $selectionArgs);
	
			$this->view->assign('currentPage', $currentPage)
			->assign('pager', array('numitems'     => $objectCount,
					'itemsperpage' => $resultsPerPage));
		}
	
		// build ModUrl instance for display hooks
		$currentUrlObject = new Zikula_ModUrl($this->name, 'admin', 'view', ZLanguage::getLanguageCode(), $currentUrlArgs);
	
		// assign the object data, sorting information and details for creating the pager
		$this->view->assign('items', $entities)
		->assign('sort', $sort)
		->assign('sdir', $sdir)
		->assign('currentUrlObject', $currentUrlObject)
		->assign($repository->getAdditionalTemplateParameters('controllerAction', $utilArgs));
	
		// fetch and return the appropriate template
		return MUImage_Util_View::processTemplate($this->view, 'admin', $objectType, 'view', $args);
	}
	
	/**
	 * This method takes care of the application configuration.
	 *
	 * @return string Output
	 */
	public function modulealbums()
	{
		$this->throwForbiddenUnless(SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN));

		// Create new Form reference
		$view = FormUtil::newForm($this->name, $this);

		// Execute form using supplied template and page event handler
		return $view->execute('admin/album.tpl', new MUImage_Form_Handler_Admin_Base_ModuleAlbums());
	}

	/**
	 * This method takes care of the application configuration.
	 *
	 * @return string Output
	 */
	public function import()
	{
		$this->throwForbiddenUnless(SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN));

		// Create new Form reference
		$view = FormUtil::newForm($this->name, $this);

		// Execute form using supplied template and page event handler
		return $view->execute('admin/import.tpl', new MUImage_Form_Handler_Admin_Base_Import());
	}
}

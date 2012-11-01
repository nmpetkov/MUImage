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
 * @version Generated by ModuleStudio 0.5.4 (http://modulestudio.de) at Sat Feb 18 18:42:39 CET 2012.
 */

/**
 * Utility implementation class for view helper methods.
 */
class MUImage_Util_View extends MUImage_Util_Base_View
{
	/**
	 *
	 * Returning the step of import
	 */
	public static function getStep() {
		$view = new Zikula_Request_Http();
		$step = $view->getGet()->filter('step', 'first', FILTER_SANITIZE_STRING);

		return $step;
	}

	/**
	 *
	 * Returning the albums
	 */

	public static function getAlbums() {

		$repository = MUImage_Util_Model::getAlbumRepository();
		$albums = $repository->selectWhere();

		return $albums;
	}

	/**
	 * Counting pictures of an album
	 */

	public static function countAlbumPictures($albumid) {

		$view = new Zikula_Request_Http();
		$id = (int) $view->getGet()->filter('id', 0, FILTER_SANITIZE_STRING);
		$where = 'tbl.album_id = \'' . DataUtil::formatForStore($id) . '\'';

		$repository = MUImage_Util_Model::getAlbumRepository();
		$album = $repository->selectById();

		/*foreach ($album[picture] as $value) {
		 $pictures[] = $value;
		 }*/
		//$count = count($album[picture]);
		$count = 0;

		//LogUtil::registerStatus($album);

		return $count;
	}

	/**
	 *
	 * Counting of total pictures
	 */
	public static function countPictures()
	{
			
		$view = new Zikula_Request_Http();
		$id = (int) $view->getGet()->filter('id', 0, FILTER_SANITIZE_STRING);
		if ($id != 0) {

			$where = 'tbl.album_id = \'' . DataUtil::formatForStore($id) . '\'';

			$repository = MUImage_Util_Model::getPictureRepository();
			$count = $repository->selectCount();
		}
		return $count;
	}

	/**
	 *
	 * Counting of total albums
	 */
	public static function countAlbums()
	{
			
		$view = new Zikula_Request_Http();
		$id = (int) $view->getGet()->filter('id', 0, FILTER_SANITIZE_STRING);
		if ($id != 0) {

			$where = 'tbl.album_id = \'' . DataUtil::formatForStore($id) . '\'';

			$repository = MUImage_Util_Model::getAlbumRepository();
			$count = $repository->selectCount();
		}
		return $count;
	}

	/**
	 *this method checks if an user may create another main album
	 * return true or false or the contingent
	 * @param $kind           $kind   kind of check 1 for controlling links, other for get contingent
	 */
	public static function otherUserMainAlbums($kind = 1) {
		$dom = ZLanguage::getModuleDomain('MUImage');
		$numberMainAlbums = ModUtil::getVar('MUImage', 'numberParentAlbums');
		if ($numberMainAlbums != '') {
			$uid = UserUtil::getVar('uid');
			$gid = UserUtil::getGroupsForUser($uid);
			if (in_array(2, $gid)) {
				if ($kind == 1) {
					return true;
				}
				else {
					$out = __('unlimited', $dom);
					return $out;
				}
			}
			else {
				$albumrepository = MUImage_Util_Model::getAlbumRepository();
				$where = 'tbl.createdUserId = \'' . DataUtil::formatForStore($uid) . '\'';
				$where .= ' AND ';
				$where .= 'tbl.parent_id IS NULL';
				$albumcount = $albumrepository->selectCount($where);
				if ($kind == 1) {
					if ($albumcount < $numberMainAlbums) {
						return true;
					}
					else {
						return false;
					}
				}
				else {
					$contingentMainAlbums = $numberMainAlbums - $albumcount;
					if ($contingentMainAlbums > 0) {
						$out = $contingentMainAlbums;
					}
					else {
						$out = 0;
					}
					return $out;
				}
			}
		}
		else {
			if ($kind == 1) {
				return true;
			}
			else {
				$out = __('unlimited', $dom);
				return $out;
			}
		}
	}

	/**
	 * this method checks if an user may create another subalbum
	 * return true or false
	 */
	public static function otherUserSubAlbums($kind = 1) {
		$numberSubAlbums = ModUtil::getVar('MUImage', 'numberSubAlbums');
		if ($numberSubAlbums != '') {
			$uid = UserUtil::getVar('uid');
			$gid = UserUtil::getGroupsForUser($uid);
			if (in_array(2, $gid)) {
				if ($kind == 1) {
					return true;
				}
				else {
					$out = __('unlimited', $dom);
					return $out;
				}
			}
			else {
				$albumrepository = MUImage_Util_Model::getAlbumRepository();
				$where2 = 'tbl.createdUserId = \'' . DataUtil::formatForStore($uid) . '\'';
				$where2 .= ' AND ';
				$where2 .= 'tbl.parent_id > 0';
				$subalbumcount = $albumrepository->selectCount($where2);
				if ($kind == 1) {
					if ($subalbumcount < $numberSubAlbums) {
						return true;
					}
					else {
						return false;
					}
				}
				else {
					$contingentSubAlbums = $numberSubAlbums - $subalbumcount;
					if ($contingentMainAlbums > 0) {
						$out = $contingentSubAlbums;
					}
					else {
						$out = 0;
					}
					return $out;
				}
			}
		}
		else {
			if ($kind == 1) {
				return true;
			}
			else {
				$out = __('unlimited', $dom);
				return $out;
			}
		}
	}

	/**
	 *this method checks if an user may create another picture
	 * return true or false
	 */
	public static function otherUserPictures($kind = 1) {
		$numberPictures = ModUtil::getVar('MUImage', 'numberPictures');
		if ($numberPictures != '') {
			$uid = UserUtil::getVar('uid');
			$gid = UserUtil::getGroupsForUser($uid);
			if (in_array(2, $gid)) {
				if ($kind == 1) {
					return true;
				}
				else {
					$out = __('unlimited', $dom);
					return $out;
				}
			}
			else {
				$picturerepository = MUImage_Util_Model::getPictureRepository();
				$where3 = 'tbl.createdUserId = \'' . DataUtil::formatForStore($uid) . '\'';
				$picturecount = $picturerepository->selectCount($where3);
				if ($kind == 1) {
					if ($picturecount < $numberPictures) {
						return true;
					}
					else {
						return false;
					}
				}
				else {
					$contingentPictures = $numberPictures - $picturecount;
					if ($contingentPictures> 0) {
						$out = $contingentPictures;
					}
					else {
						$out = 0;
					}
					return $out;
				}
			}
		}
		else {
			if ($kind == 1) {
				return true;
			}
			else {
				$out = __('unlimited', $dom);
				return $out;
			}
		}
	}

	/**
	 *
	 */
	public static function myPicture($id) {
		$view = new Zikula_Request_Http();
		$picturerepository = MUImage_Util_Model::getPictureRepository();
		$myPicture = $picturerepository->selectById($id);

		if (in_array(2, UserUtil::getGroupsForUser(UserUtil::getVar('uid')))) {
			return true;
		}
		else {
			if (UserUtil::getVar('uid') == $myPicture->getCreatedUserId()) {
				return true;
			}
			else {
				return false;
			}
		}
	}

	/**
	 *
	 */
	public static function myAlbum($id) {
		$view = new Zikula_Request_Http();
		$albumrepository = MUImage_Util_Model::getAlbumRepository();
		$myAlbum = $albumrepository->selectById($id);

		if (in_array(2, UserUtil::getGroupsForUser(UserUtil::getVar('uid')))) {
			return true;
		}
		else {
			if (UserUtil::getVar('uid') == $myAlbum->getCreatedUserId()) {
				return true;
			}
			else {
				return false;
			}
		}
	}

	/**
	 *
	 */
	public static function contingent() {

		$dom = ZLanguage::getModuleDomain('MUImage');

		$uid = UserUtil::getVar('uid');
		$mainAlbum = ModUtil::getVar('MUImage', 'numberParentAlbums');
		if ($mainAlbum != '') {
			$numberMain = self::otherUserMainAlbums(2);
		}
		else {
			$numberMain = __('Main Albums: unlimited', $dom);
		}

		$subAlbum = ModUtil::getVar('MUImage', 'numberSubAlbums');
		if ($subAlbum != '') {
			$numberSub = self::otherUserSubAlbums(2);
		}
		else {
			$numberSub = __('Sub Albums: unlimited', $dom);
		}

		$pictures = ModUtil::getVar('MUImage', 'numberPictures');
		if ($pictures != '') {
			$numberPictures = self::otherUserPictures(2);
		}
		else {
			$numberPictures = __('Pictures: unlimited', $dom);
		}

		$out = __('Your Quota: ', $dom);
		$out .= __('Main Albums: ', $dom) . $numberMain . ', ' . __('Sub Albums: ', $dom) . $numberSub . ', ' . __('Pictures: ', $dom) . $numberPictures;

		return $out;

	}

	/**
	 * @param   $id          $id  of album or picture
	 * @param   $kind        $kind check for 1 = album or 2 = picture
	 * 
	 *  assign to template
	 */
	public static function checkForBlocksAndContent($id = 0, $kind = 1) {
		$serviceManager = ServiceUtil::getManager();
		$view = new Zikula_View($serviceManager);
		if ($id > 0) {
			$block = BlockUtil::getBlockInfo($id, 'content');
			LogUtil::registerStatus($block);				
			$view->assign('muimageblock', $block);
		}
	}
}

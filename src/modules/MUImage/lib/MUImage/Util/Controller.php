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
 * Utility implementation class for controller helper methods.
 */
class MUImage_Util_Controller extends MUImage_Util_Base_Controller
{
	
	/**
	 * this function chekcs for the given min width 
	 * for a picture in the configuration
	 * return string
	 */
	
	public static function minWidth() {
		
		$dom = ZLanguage::getModuleDomain('MUImage');
		
		$minWidth = ModUtil::getVar('MUImage', 'minWidth');
		if ($minWidth == '') {
			return __('Not set', $dom);
		}
		else {
			return $minWidth . ' ' . __('pixel');
		}
		
	}
    /**
     * Get allowed filesize
     */
    
    public static function maxSize() 
    {

    	$maxSize = ModUtil::getVar('MUImage', 'fileSize');
    	
    	$dom = ZLanguage::getModuleDomain('MUImage');

		if ($maxSize > 0) {

				$maxSizeKB = $maxSize / 1024;

				if ($maxSizeKB < 1024) {
					$maxSizeKB = DataUtil::formatNumber($maxSizeKB);

					$allowedSize = $maxSizeKB . ' KB';
					return $allowedSize;

				}

				$maxSizeMB = $maxSizeKB / 1024;
				$maxSizeMB = DataUtil::formatNumber($maxSizeMB);

				$allowedSize = $maxSizeMB . ' MB';
				return $allowedSize;

		}
		else {
			$allowedSize = __('No limit', $dom);
		}
		
		return $allowedSize;
    }
    
    
    /**
     * this function calculates the number of upload fields
     * @return number
     */
    public static function allowedFields() {
    	// we check the created pictures for this user
    	$uid = UserUtil::getVar('uid');
    	$gid = UserUtil::getGroupsForUser($uid);
    	if (in_array(2, $gid)) {
    		$allowedFields = 10 + 1;
    	}
    	else {
    		$picturerepository = MUImage_Util_Model::getPictureRepository();
    		$where3 = 'tbl.createdUserId = \'' . DataUtil::formatForStore($uid) . '\'';
    		$picturecount = $picturerepository->selectCount($where3);
    	
    		// we check for modvar numberPictures
    		$numberPictures = ModUtil::getVar('MUImage', 'numberPictures');
    	
    		$diff = $numberPictures - $picturecount;
    		if ($diff < 10) {
    			$allowedFields = $diff + 1;
    		}
    		else {
    			$allowedFields = 10 + 1;
    		}
    	}
    	
    	return $allowedFields;
    }
}

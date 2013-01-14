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
 * Upload handler implementation class.
 */
class MUImage_UploadHandler extends MUImage_Base_UploadHandler
{

	/**
	 * var array List of allowed file sizes per field.
	 */
	protected $allowedFileSizes;

	/**
	 * Constructor initialising the supported object types.
	 */
	public function __construct()
	{
		$this->allowedObjectTypes = array('picture');
		$this->imageFileTypes = array('gif', 'jpeg', 'jpg', 'png');
		$this->forbiddenFileTypes = array('cgi', 'pl', 'asp', 'phtml', 'php', 'php3', 'php4', 'php5', 'exe', 'com', 'bat', 'jsp', 'cfm', 'shtml');
		$filesize = ModUtil::getVar('MUImage', 'fileSize');
		$this->allowedFileSizes = array('picture' => array('imageUpload' => $filesize));

	}
	
    /**
     * Process a file upload.
     *
     * @param string $objectType Currently treated entity type.
     * @param string $fileData   Form data array.
     * @param string $fieldName  Name of upload field.
     *
     * @return array Resulting file name and collected meta data.
     */
    public function performFileUpload($objectType, $fileData, $fieldName)
    {
        $dom = ZLanguage::getModuleDomain('MUImage');

        $result = array('fileName' => '',
            'metaData' => array());

        // check whether uploads are allowed for the given object type
        if (!in_array($objectType, $this->allowedObjectTypes)) {
            return $result;
        }

        // perform validation
        if (!$this->validateFileUpload($objectType, $fileData[$fieldName], $fieldName)) {
            // skip this upload field
            return false; //$result;
        }

        // retrieve the final file name
        $fileName = $fileData[$fieldName]['name'];
        $fileNameParts = explode('.', $fileName);
        $extension = $fileNameParts[count($fileNameParts) - 1];
        $extension = str_replace('JPG', 'jpg', strtolower($extension));
        $fileNameParts[count($fileNameParts) - 1] = $extension;
        $fileName = implode('.', $fileNameParts);

        // retrieve the final file name
        $basePath = MUImage_Util_Controller::getFileBaseFolder($objectType, $fieldName);
        $fileName = $this->determineFileName($objectType, $fieldName, $basePath, $fileName, $extension);

        if (!move_uploaded_file($fileData[$fieldName]['tmp_name'], $basePath . $fileName)) {
            return LogUtil::registerError(__('Error! Could not move your file to the destination folder.', $dom));
        }

        // collect data to return
        $result['fileName'] = $fileName;
        $result['metaData'] = $this->readMetaDataForFile($fileName, $basePath . $fileName);

        return $result;
    }


	/**
	 * Check if an upload file meets all validation criteria.
	 *
	 * @param array $file Reference to data of uploaded file.
	 *
	 * @return boolean true if file is valid else false
	 */
	protected function validateFileUpload($objectType, $file, $fieldName)
	{
		$dom = ZLanguage::getModuleDomain('MUImage');

		// check if a file has been uploaded properly without errors
		if ((!is_array($file)) || (is_array($file) && ($file['error'] != '0'))) {
			if (is_array($file)) {
				return $this->handleError($file);
			}
			return LogUtil::registerError(__('Error! No file found.', $dom));
		}

		$maxSize = $this->allowedFileSizes[$objectType][$fieldName];

		if ($maxSize > 0) {

			$fileSize = filesize($file['tmp_name']);

			if ($fileSize > $maxSize) {
				$maxSizeKB = $maxSize / 1024;

				if ($maxSizeKB < 1024) {
					$maxSizeKB = DataUtil::formatNumber($maxSizeKB);
					$fileName = $file['name'];
                    LogUtil::registerError(__f('Error! Your file %s is too big. Please keep it smaller than %s kilobytes.', array($fileName, $maxSizeKB), $dom));
					return false;

				}

				$maxSizeMB = $maxSizeKB / 1024;
				$maxSizeMB = DataUtil::formatNumber($maxSizeMB);
				$fileName = $file['name'];
                LogUtil::registerError(__f('Error! Your file is too big. Please keep it smaller than %s megabytes.', array($fileName, $maxSizeKB), $dom));
				return false; 
			}

		}

		// extract file extension
		$fileName = $file['name'];
		$extensionarr = explode('.', $fileName);
		$extension = strtolower($extensionarr[count($extensionarr) - 1]);

		// validate extension
		$isValidExtension = $this->isAllowedFileExtension($objectType, $fieldName, $extension);
		if ($isValidExtension === false) {
			LogUtil::registerError(__('Error! This file type is not allowed. Please choose another file format.', $dom));
			
		}

		// validate image file
		$imgInfo = array();
		$isImage = in_array($extension, $this->imageFileTypes);
		if ($isImage) {
			$imgInfo = getimagesize($file['tmp_name']);
			if (!is_array($imgInfo) || !$imgInfo[0] || !$imgInfo[1]) {
				return LogUtil::registerError(__('Error! This file type seems not to be a valid image.', $dom));
			}
		}

		return true;
	}
}

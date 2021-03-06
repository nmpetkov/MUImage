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
 * Validator class for encapsulating entity validation methods.
 *
 * This is the concrete validation class for picture entities.
 */
class MUImage_Entity_Validator_Picture extends MUImage_Entity_Validator_Base_Picture
{
    /**
     * Performs all validation rules.
     *
     * @return mixed either array with error information or true on success
     */
    public function validateAll()
    {
        $errorInfo = array('message' => '', 'code' => 0, 'debugArray' => array());
        $dom = ZLanguage::getModuleDomain('MUImage');
        if (!$this->isStringNotLongerThan('title', 255)) {
            $errorInfo['message'] = __f('Error! Length of field value must not be higher than %2$s (%1$s).', array('title', 255), $dom);
            return $errorInfo;
        }
        if (!$this->isStringNotLongerThan('description', 2000)) {
            $errorInfo['message'] = __f('Error! Length of field value must not be higher than %2$s (%1$s).', array('description', 2000), $dom);
            return $errorInfo;
        }
        if (!$this->isValidBoolean('showTitle')) {
            $errorInfo['message'] = __f('Error! Field value must be a valid boolean (%s).', array('showTitle'), $dom);
            return $errorInfo;
        }
        if (!$this->isValidBoolean('showDescription')) {
            $errorInfo['message'] = __f('Error! Field value must be a valid boolean (%s).', array('showDescription'), $dom);
            return $errorInfo;
        }
        if (!$this->isStringNotLongerThan('imageUpload', 255)) {
            $errorInfo['message'] = __f('Error! Length of field value must not be higher than %2$s (%1$s).', array('imageUpload', 255), $dom);
            return $errorInfo;
        }
       /* if (!$this->isStringNotEmpty('imageUpload')) {
            $errorInfo['message'] = __f('Error! Field value must not be empty (%s).', array('imageUpload'), $dom);
            return $errorInfo;
        }*/
        if (!$this->isValidInteger('imageView')) {
            $errorInfo['message'] = __f('Error! Field value may only contain digits (%s).', array('imageView'), $dom);
            return $errorInfo;
        }
        if (!$this->isNumberNotLongerThan('imageView', 11)) {
            $errorInfo['message'] = __f('Error! Length of field value must not be higher than %2$s (%1$s).', array('imageView', 11), $dom);
            return $errorInfo;
        }
        return true;
    }
}

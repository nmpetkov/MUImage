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
 * Check permissions during the workflow.
 *
 * @param array $obj
 * @param int $permLevel
 * @param int $currentUser
 * @return bool
 */
function MUImage_workflow_simpleapproval_permissioncheck($obj, $permLevel, $currentUser)
{
    /** TODO */
    $component = 'MUImage::';
    // process $obj and calculate an instance
    /** TODO */
    $instance = '::';

    return SecurityUtil::checkPermission($component, $instance, $permLevel, $currentUser);
}

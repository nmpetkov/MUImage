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
 * Event handler implementation class for view-related events.
 */
class MUImage_Listener_View
{
    /**
     * Listener for the `view.init` event.
     *
     * Occurs just before `Zikula_View#__construct()` finishes.
     * The subject is the Zikula_View instance.
     */
    public static function init(Zikula_Event $event)
    {
    }

    /**
     * Listener for the `view.postfetch` event.
     *
     * Filter of result of a fetch. Receives `Zikula_View` instance as subject, args are
     * `array('template' => $template)`, $data was the result of the fetch to be filtered.
     */
    public static function postFetch(Zikula_Event $event)
    {
    }
}

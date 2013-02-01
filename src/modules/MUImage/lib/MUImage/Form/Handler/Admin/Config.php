
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
 * Configuration handler implementation class
 */
class MUImage_Form_Handler_Admin_Config extends MUImage_Form_Handler_Admin_Base_Config
{
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
    	$dom = ZLanguage::getModuleDomain('MUImage');
    	
    	// retrieve form data
    	$data = $this->view->getValues();
    	
    	$ending = $data['config']['ending'];
    	if ($ending != '') {
    		if ($ending != 'html' && $ending != 'htm') {
    			LogUtil::registerError(__('Sorry! Your enter for the ending is invalid!', $dom));
    			$url = ModUtil::url('MUImage', 'admin', 'config');
    			return System::redirect($url);
    		}
    	} 
    	
    	parent::handleCommand($view, $args);
    }
}

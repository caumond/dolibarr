<?php
/* todoist module for Dolibarr ERP&CRM
 * Copyright (C) 2014      Jean-François Ferry <jfefe@aternatik.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 	\defgroup	todoist	todoist module
 * 	\brief		todoist module descriptor.
 * 	\file		core/modules/modtodoist.class.php
 * 	\ingroup	todoist
 * 	\brief		Description and activation file for module todoist
 */
include_once DOL_DOCUMENT_ROOT . "/core/modules/DolibarrModules.class.php";

/**
 * Description and activation class for module todoist
 */
class modtodoist extends DolibarrModules
{

	/**
	 * 	Constructor. Define names, constants, directories, boxes, permissions
	 *
	 * 	@param	DoliDB		$db	Database handler
	 */
	public function __construct($db)
	{
		global $langs, $conf;

		$this->db = $db;

		// Id for module (must be unique).
		// Use a free id here
		// (See http://wiki.dolibarr.org/index.php/List_of_modules_id for available ranges).
		$this->numero = 577777;
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'todoist';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		// It is used to group modules in module setup page
		$this->family = "other";
		// Module label (no space allowed)
		// used if translation string 'ModuleXXXName' not found
		// (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i', '', get_class($this));
		// Module description
		// used if translation string 'ModuleXXXDesc' not found
		// (where XXX is value of numeric property 'numero' of module)
		$this->description = $langs->trans("Module577777Description");
		// Possible values for version are: 'development', 'experimental' or version
		$this->version = '0.2';
		// Key used in llx_const table to save module status enabled/disabled
		// (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_' . strtoupper($this->name);
		// Where to store the module in setup page
		// (0=common,1=interface,2=others,3=very specific)
		$this->special = 1;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png
		// use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png
		// use this->picto='pictovalue@module'
		$this->picto = 'todoist@todoist'; // mypicto@todoist
		// Defined all module parts (triggers, login, substitutions, menus, css, etc...)
		// for default path (eg: /todoist/core/xxxxx) (0=disable, 1=enable)
		// for specific path of parts (eg: /todoist/core/modules/barcode)
		// for specific css file (eg: /todoist/css/todoist.css.php)
		$this->module_parts = array(
			// Set this to 1 if module has its own trigger directory
			//'triggers' => 1,
			// Set this to 1 if module has its own login method directory
			//'login' => 0,
			// Set this to 1 if module has its own substitution function file
			//'substitutions' => 0,
			// Set this to 1 if module has its own menus handler directory
			//'menus' => 0,
			// Set this to 1 if module has its own theme directory (theme)
			// 'theme' => 0,
			// Set this to 1 if module overwrite template dir (core/tpl)
			// 'tpl' => 0,
			// Set this to 1 if module has its own barcode directory
			//'barcode' => 0,
			// Set this to 1 if module has its own models directory
			//'models' => 0,
			// Set this to relative path of css if module has its own css file
			//'css' => array('todoist/css/mycss.css.php'),
			// Set this to relative path of js file if module must load a js on all pages
			// 'js' => array('todoist/js/todoist.js'),
			// Set here all hooks context managed by module
			// 'hooks' => array('hookcontext1','hookcontext2'),
			// To force the default directories names
			// 'dir' => array('output' => 'othermodulename'),
			// Set here all workflow context managed by module
			// Don't forget to depend on modWorkflow!
			// The description translation key will be descWORKFLOW_MODULE1_YOURACTIONTYPE_MODULE2
			// You will be able to check if it is enabled with the $conf->global->WORKFLOW_MODULE1_YOURACTIONTYPE_MODULE2 constant
			// Implementation is up to you and is usually done in a trigger.
			// 'workflow' => array(
			//     'WORKFLOW_MODULE1_YOURACTIONTYPE_MODULE2' => array(
			//         'enabled' => '! empty($conf->module1->enabled) && ! empty($conf->module2->enabled)',
			//         'picto' => 'yourpicto@todoist',
			//         'warning' => 'WarningTextTranslationKey',
			//      ),
			// ),
		);

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/todoist/temp");
		$this->dirs = array();

		// Config pages. Put here list of php pages
		// stored into todoist/admin directory, used to setup module.
		$this->config_page_url = array("todoist.php@todoist");

		// Dependencies
		// A condition to hide module
		$this->hidden = false;
		// List of modules class name as string that must be enabled if this module is enabled
		// Example : $this->depends('modAnotherModule', 'modYetAnotherModule')
		$this->depends = array();
		// List of modules id to disable if this one is disabled
		$this->requiredby = array();
		// List of modules id this module is in conflict with
		$this->conflictwith = array();
		// Minimum version of PHP required by module
		$this->phpmin = array(5, 3);
		// Minimum version of Dolibarr required by module
		$this->need_dolibarr_version = array(3, 5);
		// Language files list (langfiles@todoist)
		$this->langfiles = array("todoist@todoist");
		// Constants
		// List of particular constants to add when module is enabled
		// (name, type ['chaine' or ?], value, description, visibility, entity ['current' or 'allentities'], delete on unactive)
		// Example:
		$this->const = array(
				0 => array(
					'todoist_LABEL',
					'chaine',
					'Todoist',
					'Text of todoist button',
					1,
			      'current',
			      0,
				),
				1 => array(
					'todoist_URL',
					'chaine',
					'https://todoist.com/app?lang=fr&v=731#start',
					'URL of todoist service',
					0,
				)
		);

		// Array to add new pages in new tabs
		// Example:
		$this->tabs = array(
		);

		// Dictionaries
		if (! isset($conf->todoist->enabled)) {
			$conf->todoist=new stdClass();
			$conf->todoist->enabled = 0;
		}
		$this->dictionaries = array();

		// Permissions
		$this->rights = array(); // Permission array used by this module
		$r = 0;

		// Add here list of permission defined by
		// an id, a label, a boolean and two constant strings.
		// Example:
		//// Permission id (must not be already used)
		$this->rights[$r][0] = 577777;
		//// Permission label
		$this->rights[$r][1] = 'See todoist button';
		//// Permission by default for new user (0/1)
		$this->rights[$r][3] = 1;
		//// In php code, permission will be checked by test
		//// if ($user->rights->permkey->level1->level2)
		$this->rights[$r][4] = 'see';
		//// In php code, permission will be checked by test
		//// if ($user->rights->permkey->level1->level2)
		//$this->rights[$r][5] = 'level2';
		$r++;
		
		// Main menu entries
		$this->menus = array(); // List of menus to add
		$r = 0;
		
		$this->menu[$r]=array(
				'fk_menu'=>0,
				'type'=>'top',
				'titre'=>'$conf->global->todoist_LABEL',
				'mainmenu'=>'todoist',
				'url'=>'/todoist/todoist_frame.php',
				'langs'=>'other',
				'position'=>100,
				'perms'=>'$user->rights->todoist->see',
				'enabled'=>'$conf->todoist->enabled',
				'target'=>'',
				'user'=>0
		);
		$r++;

		// Can be enabled / disabled only in the main company when multi-company is in use
		// $this->core_enabled = 1;
	}

	/**
	 * Function called when module is enabled.
	 * The init function add constants, boxes, permissions and menus
	 * (defined in constructor) into Dolibarr database.
	 * It also creates data directories
	 *
	 * 	@param		string	$options	Options when enabling module ('', 'noboxes')
	 * 	@return		int					1 if OK, 0 if KO
	 */
	public function init($options = '')
	{
		$sql = array();

		$result = $this->loadTables();

		return $this->_init($sql, $options);
	}

	/**
	 * Function called when module is disabled.
	 * Remove from database constants, boxes and permissions from Dolibarr database.
	 * Data directories are not deleted
	 *
	 * 	@param		string	$options	Options when enabling module ('', 'noboxes')
	 * 	@return		int					1 if OK, 0 if KO
	 */
	public function remove($options = '')
	{
		$sql = array();

		return $this->_remove($sql, $options);
	}

	/**
	 * Create tables, keys and data required by module
	 * Files llx_table1.sql, llx_table1.key.sql llx_data.sql with create table, create keys
	 * and create data commands must be stored in directory /todoist/sql/
	 * This function is called by this->init
	 *
	 * 	@return		int		<=0 if KO, >0 if OK
	 */
	private function loadTables()
	{
		return $this->_load_tables('/todoist/sql/');
	}
}

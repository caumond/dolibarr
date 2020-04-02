<?php
/* Copyright (C) 2019       Open-DSI            <support@open-dsi.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    htdocs/custom/core/modules/modHR.class.php
 * \ingroup HR
 * \brief   Fichier de description et activation du module HR
 */
include_once (DOL_DOCUMENT_ROOT . "/core/modules/DolibarrModules.class.php");

/**
 * \class modHR
 * \brief Classe de description et activation du module Time clock
 */
class modHR extends DolibarrModules
{
	/**
	 * Constructor.
	 * Define names, constants, directories, boxes, permissions
	 *
	 * @param DoliDB $db
	 */
	public function __construct($db)
	{
		global $langs, $conf;

		$this->db = $db;

		$langs->loadLangs(array('hr@hr','opendsi@hr'));

		$this->numero = 163901;
		$this->rights_class = 'hr';

		$this->family = "ecm";
		$this->familyinfo = array('opendsi' => array('position' => '001', 'label' => $langs->trans("OpenDsiFamily")));

		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace ( '/^mod/i', '', get_class ( $this ) );
		$this->description = "Human ressource management";
		$this->descriptionlong = "";
		$this->editor_name = 'Open-DSI';
		$this->editor_url = 'http://www.open-dsi.fr';
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '1.0.0';

		$this->const_name = 'MAIN_MODULE_' . strtoupper ( $this->name );
		$this->special = 0;
		$this->picto = 'opendsi@hr';

		// Defined if the directory /mymodule/inc/triggers/ contains triggers or not
		// $this->triggers = 1;

		// Data directories to create when module is enabled
		$this->dirs = array ();

		// Config pages
		$this->config_page_url = array(
				"admin_hr.php@hr"
		);

		// Dependencies
		$this->depends = array (); // List of modules id that must be enabled if this module is enabled
		$this->requiredby = array (); // List of modules id to disable if this one is disabled
		$this->phpmin = array (
			5,
			3 
		); // Minimum version of PHP required by module
		$this->need_dolibarr_version = array (
			3,
			6 
		); // Minimum version of Dolibarr required by module
		$this->langfiles = array("hr@hr","opendsi@hr");

		// Dictionnaries
		/*
		$this->dictionnaries=array(
			'langs'=>'hr@hr',
			'tabname'=>array(
				MAIN_DB_PREFIX."c_type_employment_contract"
			),
			'tablib'=>array(
				"TypeEmploymentContract"
			),
			'tabsql'=>array(
				'SELECT id as rowid, code, label, active FROM '.MAIN_DB_PREFIX.'c_type_employment_contract'
			),
			'tabsqlsort'=>array(
				'id ASC'
			),
			'tabfield'=>array(
				"code,label"
			),
			'tabfieldvalue'=>array(
				"code,label"
			),
			'tabfieldinsert'=>array(
				"code,label"
			),
			'tabrowid'=>array(
				"id"
			),
			'tabcond'=>array(
				'$conf->hr->enabled'
			)
		);
		*/

		// Constantes
		$this->const = array ();
		$r = 0;
		
		// Boxes
		/*
		$this->boxes = array (
			    0 => array(
				'file' => 'box_timeclock@hr',
				'note' => 'HRM - Timeclock',
				'enabledbydefaulton' => 'Home'
				)
		);
		*/
		
		// Permissions
		$this->rights = array(); // Permission array used by this module
		$r = 0;

		/**
		 * Navette RH
		 */

		/*
			$this->rights[$r][0] = 1130311;
			$this->rights[$r][1] = 'Manage navette RH';
			$this->rights[$r][2] = 'r';
			$this->rights[$r][3] = 0;
			$this->rights[$r][4] = 'nrh';
			$this->rights[$r][5] = 'manage';
			$r++;
		*/
			
		/**
		 * Contract
		 */
		 
		/*
			$this->rights[$r][0] = 1130321;
			$this->rights[$r][1] = 'See contract';
			$this->rights[$r][2] = 'r';
			$this->rights[$r][3] = 1;
			$this->rights[$r][4] = 'contract';
			$this->rights[$r][5] = 'read';
			$r++;

			$this->rights[$r][0] = 1130322;
			$this->rights[$r][1] = 'See all contracts';
			$this->rights[$r][2] = 'r';
			$this->rights[$r][3] = 0;
			$this->rights[$r][4] = 'contract';
			$this->rights[$r][5] = 'read_all';
			$r++;

			$this->rights[$r][0] = 1130323;
			$this->rights[$r][1] = 'Create/modify contract';
			$this->rights[$r][2] = 'w';
			$this->rights[$r][3] = 0;
			$this->rights[$r][4] = 'contract';
			$this->rights[$r][5] = 'write';
			$r++;

			$this->rights[$r][0] = 1130324;
			$this->rights[$r][1] = 'Remove contract';
			$this->rights[$r][2] = 'd';
			$this->rights[$r][3] = 0;
			$this->rights[$r][4] = 'contract';
			$this->rights[$r][5] = 'delete';
			$r++;
		*/

		/**
		 * Timeclock
		 */
		
			// Check arrival or exit
			$this->rights[$r][0] = 1130301;
			$this->rights[$r][1] = 'Check-in or check-out';
			$this->rights[$r][2] = 'r';
			$this->rights[$r][3] = 1;
			$this->rights[$r][4] = 'timeclock';
			$this->rights[$r][5] = 'check';
			$r++;

			// See historic
			$this->rights[$r][0] = 1130302;
			$this->rights[$r][1] = 'Show historic';
			$this->rights[$r][2] = 'r';
			$this->rights[$r][3] = 0;
			$this->rights[$r][4] = 'timeclock';
			$this->rights[$r][5] = 'see_historic';
			$r++;

			// Update historic
			$this->rights[$r][0] = 1130303;
			$this->rights[$r][1] = 'Update historic';
			$this->rights[$r][2] = 'w';
			$this->rights[$r][3] = 0;
			$this->rights[$r][4] = 'timeclock';
			$this->rights[$r][5] = 'update_historic';
			$r++;
			 
			// Delete historic
			$this->rights[$r][0] = 1130304;
			$this->rights[$r][1] = 'Delete historic';
			$this->rights[$r][2] = 'd';
			$this->rights[$r][3] = 0;
			$this->rights[$r][4] = 'timeclock';
			$this->rights[$r][5] = 'delete_historic';
			$r++;
			 
			 // Summary
			 $this->rights[$r][0] = 1130306;
			 $this->rights[$r][1] = 'See summary';
			 $this->rights[$r][2] = 'r';
			 $this->rights[$r][3] = 0;
			 $this->rights[$r][4] = 'timeclock';
			 $this->rights[$r][5] = 'summary';
			 $r++;

		// Tabs
		/*
		$this->tabs = array('user:+contract:Contract:hr@hr:$user->rights->hr->contract->read:/custom/hr/contract/index.php?id=__ID__');
		*/

		// Dir
		$this->dirs = array("/hr/");

		// Main menu entries
		$this->menus = array (); // List of menus to add
		$r = 0;

		/*
		$r ++;
		$this->menu[$r] = array (
				'fk_menu' => 'fk_mainmenu=hrm',
				'type' => 'left',
				'titre' => 'NRH',
				'leftmenu' => 'nrh',
				'url' => '/hr/navette/index.php',
				'langs' => 'hr@hr',
				'position' => 113001,
				'enabled' => '$conf->hr->enabled',
				'perms' => '',
				'target' => '',
				'user' => 0 
		);

		$r ++;
		$this->menu[$r] = array (
				'fk_menu' => 'fk_mainmenu=hrm,fk_leftmenu=nrh',
				'type' => 'left',
				'titre' => 'Administration',
				'leftmenu' => 'admin',
				'url' => '/hr/navette/admin/index.php',
				'langs' => 'hr@hr',
				'position' => 113002,
				'enabled' => '$conf->hr->enabled',
				'perms' => '$user->rights->hr->nrh->manage',
				'target' => '',
				'user' => 0 
		);
		*/

		$r ++;
		$this->menu[$r] = array (
				'fk_menu' => 'fk_mainmenu=hrm',
				'type' => 'left',
				'titre' => 'Timeclock',
				'leftmenu' => 'timeclock',
				'url' => '/hr/timeclock/index.php',
				'langs' => 'hr@hr',
				'position' => 113031,
				'enabled' => '$conf->hr->enabled',
				'perms' => '$user->rights->hr->timeclock->check',
				'target' => '',
				'user' => 0 
		);

		$r ++;
		$this->menu[$r] = array (
				'fk_menu' => 'fk_mainmenu=hrm,fk_leftmenu=timeclock',
				'type' => 'left',
				'titre' => 'History',
				'leftmenu' => 'history',
				'url' => '/hr/timeclock/history.php',
				'langs' => 'hr@hr',
				'position' => 113032,
				'enabled' => '$conf->hr->enabled',
				'perms' => '$user->rights->hr->timeclock->see_historic',
				'target' => '',
				'user' => 0 
		);

		$r ++;
		$this->menu[$r] = array (
				'fk_menu' => 'fk_mainmenu=hrm,fk_leftmenu=timeclock',
				'type' => 'left',
				'titre' => 'Summary',
				'leftmenu' => 'summary',
				'url' => '/hr/timeclock/summary.php',
				'langs' => 'hr@hr',
				'position' => 113034,
				'enabled' => '$conf->hr->enabled',
				'perms' => '$user->rights->hr->timeclock->summary',
				'target' => '',
				'user' => 0 
		);
		$r ++;
	}

	/**
	 * Function called when module is enabled.
	 * The init function add constants, boxes, permissions and menus
	 * (defined in constructor) into Dolibarr database.
	 * It also creates data directories
	 *
	 * @param string $options Enabling module ('', 'noboxes')
	 * @return int if OK, 0 if KO
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
	 * @param string $options Enabling module ('', 'noboxes')
	 * @return int if OK, 0 if KO
	 */
	public function remove($options = '')
	{
		$sql = array();
		
		return $this->_remove($sql, $options);
	}

	/**
	 * Create tables, keys and data required by module
	 * Files llx_table1.sql, llx_table1.key.sql llx_data.sql with create table, create keys
	 * and create data commands must be stored in directory /accountancy_plan_2014_FR_develop/sql/
	 * This function is called by this->init
	 *
	 * @return int if KO, >0 if OK
	 */
	private function loadTables()
	{
		return $this->_load_tables('/hr/sql/');
	}
}
?>

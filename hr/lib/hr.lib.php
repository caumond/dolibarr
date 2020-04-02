<?php
/* Copyright (C) 2019       Open-DSI	<support@open-dsi.fr>
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
 * \file		lib/hr.lib.php
 * \ingroup		HRM+
 * \brief		Library of HRM+
 */
function HRAdminPrepareHead()
{
	global $langs, $conf;
	
	$langs->load("hr@hr");
	$langs->load("admin");
	
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/hr/admin/admin_hr.php", 1);
	$head[$h][1] = $langs->trans("Parameters");
	$head[$h][2] = 'parameters';
	$h ++;

	$head[$h][0] = dol_buildpath("/hr/admin/admin_establishment.php", 1);
	$head[$h][1] = $langs->trans("Establishments");
	$head[$h][2] = 'establishments';
	$h ++;

/*	
	$head[$h][0] = dol_buildpath("/hr/admin/admin_timeclock.php", 1);
	$head[$h][1] = $langs->trans("Timeclock");
	$head[$h][2] = 'timeclock';
	$h ++;
*/

	$head[$h][0] = dol_buildpath("/hr/admin/about.php", 1);
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'about';
	$h ++;
	
	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array(
	// 'entity:+tabname:Title:@hr:/hr/mypage.php?id=__ID__'
	// ); // to add new tab
	// $this->tabs = array(
	// 'entity:-tabname:Title:@hr:/hr/mypage.php?id=__ID__'
	// ); // to remove a tab
	complete_head_from_modules($conf, $langs, null, $head, $h, 'hr_admin');
	
	return $head;
}

/**
 * Return head table for employment contract tabs screen
 *
 * @param   Emcontract		$object		Object related to tabs
 * @return  array						Array of tabs to show
 */
/*
function emcontract_prepare_head($object)
{
	global $langs, $conf, $user;

	$h = 0;
	$head = array();

    $head[$h][0] = DOL_URL_ROOT . '/custom/hr/contract/card.php?user_id='.$userstatic->id.'&id='.$object->id;
    $head[$h][1] = $langs->trans("Card");
    $head[$h][2] = 'card';
    $h++;

    // Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    // $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
    // $this->tabs = array('entity:-tabname);   												to remove a tab
    complete_head_from_modules($conf,$langs,$object,$head,$h,'emcontract');

	complete_head_from_modules($conf,$langs,$object,$head,$h,'emcontract','remove');
    
	$head[$h][0] = DOL_URL_ROOT.'/custom/hr/contract/document.php?user_id='.$userstatic->id.'&id='.$object->id;
	$head[$h][1] = $langs->trans("Documents");
	$head[$h][2] = 'documents';
	$h++;
    
    $head[$h][0] = DOL_URL_ROOT . '/custom/hr/contract/info.php?user_id='.$userstatic->id.'&id=' . $object->id;
	$head[$h][1] = $langs->trans("Info");
	$head[$h][2] = 'info';
	$h++;

	return $head;
}
*/

/**
 * Return head table for establishment tabs screen
 *
 * @param   Establishment	$object		Object related to tabs
 * @return  array						Array of tabs to show
 */
function establishment_prepare_head($object)
{
	global $langs, $conf;

	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT.'/custom/hr/establishment/card.php?id=' . $object->id;
	$head[$h][1] = $langs->trans("Card");
	$head[$h][2] = 'card';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
    // $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
    // $this->tabs = array('entity:-tabname);   												to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'establishment');

	$head[$h][0] = DOL_URL_ROOT.'/custom/hr/establishment/info.php?id=' . $object->id;
	$head[$h][1] = $langs->trans("Info");
	$head[$h][2] = 'info';
	$h++;

	complete_head_from_modules($conf,$langs,$object,$head,$h,'establishment','remove');

	return $head;
}

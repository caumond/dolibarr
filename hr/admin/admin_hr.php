<?php
/* Copyright (C) 2019	Open-DSI	<support@open-dsi.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file 	htdocs/custom/hr/admin/admin_hrm.php
 * \ingroup HRM+
 * \brief 	HRM module setup page
 */
// Dolibarr environment
$res = @include '../../main.inc.php'; // From htdocs directory
if (! $res) {
	$res = @include '../../../main.inc.php'; // From "custom" directory
}

require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once '../lib/hr.lib.php';

$langs->load("admin");
$langs->load("hrm");
$langs->load("hr@hr");

if (! $user->admin)
	accessforbidden();

$action = GETPOST('action', 'alpha');

// Other parameters HRM_*
$list = array (
		'HRM_EMAIL_EXTERNAL_SERVICE'   // To prevent your public accountant for example
);

/*
 * Actions
 */
if ($action == 'update') {
	$error = 0;
	
	foreach ($list as $constname) {
		$constvalue = GETPOST($constname, 'alpha');
		
		if (! dolibarr_set_const($db, $constname, $constvalue, 'chaine', 0, '', $conf->entity)) {
			$error ++;
		}
	}
	
	if (! $error) {
		setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
	} else {
		setEventMessages($langs->trans("Error"), null, 'errors');
	}
}

/*
 * View
 */
llxHeader('', $langs->trans('Parameters'));

$form = new Form($db);

dol_htmloutput_mesg($mesg);

// Subheader
$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">' . $langs->trans("BackToModuleList") . '</a>';
print load_fiche_titre($langs->trans("HRMSetup"), $linkback);

// Configuration header
$head = HRAdminPrepareHead();

print '<form action="' . $_SERVER["PHP_SELF"] . '" method="post">';
print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
print '<input type="hidden" name="action" value="update">';

dol_fiche_head($head, 'parameters', $langs->trans("Module113030Name"), 0, "hr@hr");

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td colspan="3">' . $langs->trans('Journaux') . '</td>';
print "</tr>\n";

foreach ( $list as $key ) {
	$var = ! $var;
	
	print '<tr ' . $bc[$var] . ' class="value">';
	
	// Param
	$label = $langs->trans($key);
	print '<td width="50%"><label for="' . $key . '">' . $label . '</label></td>';
	
	// Value
	print '<td>';
	print '<input type="text" size="20" id="' . $key . '" name="' . $key . '" value="' . $conf->global->$key . '">';
	print '</td></tr>';
}

print "</table>\n";

dol_fiche_end();

print '<div class="center"><input type="submit" class="button" value="' . $langs->trans('Modify') . '" name="button"></div>';

print '</form>';

llxFooter();
$db->close();
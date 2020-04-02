<?php
/* Copyright (C) 2019             Open-DSI            <support@open-dsi.fr>
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
 * \file		admin/about.php
 * \ingroup		HRM+
 * \brief		About Page
 */
// Dolibarr environment
$res = @include '../../main.inc.php'; // From htdocs directory
if (! $res) {
	$res = @include '../../../main.inc.php'; // From "custom" directory
}

// Libraries
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once '../lib/hr.lib.php';

dol_include_once('/hr/lib/php-markdown/markdown.php');

// Langs
$langs->load("admin");
$langs->load('hrm');
$langs->load('hr@hr');
$langs->load("opendsi@hr");

// Access control
if (! $user->admin)
  accessforbidden();

// Parameters
$action = GETPOST('action', 'alpha');

/*
 * Actions
 */

/*
 * View
 */
$page_name = "HRAbout";
llxHeader('', $langs->trans($page_name));

// Subheader
$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">' . $langs->trans("BackToModuleList") . '</a>';
print_fiche_titre($langs->trans($page_name), $linkback);

// Configuration header
$head = HRAdminPrepareHead();
dol_fiche_head($head, 'about', $langs->trans("Module113900Name"), 0, 'oblyon@oblyon');

print '<table class="noborder" width="100%">';

print '<tr class="liste_titre"><td colspan="2">' . $langs->trans("Authors") . '</td>';
print '</tr>';

// Alexandre Spangaro
print '<td width="310px"><img src="../img/opendsi_dolibarr_preferred_partner.png" /></td>'."\n";
print '<td align="left" valign="top"><p>'.$langs->trans("OpenDsiAboutDesc").'</p></td>'."\n";
print '</tr></table>'."\n";

print '<br>';

$buffer = file_get_contents(dol_buildpath('/hr/README.md', 0));
echo Markdown($buffer);
print '<br>';

echo '<br>', '<a href="' . dol_buildpath('/hr/COPYING', 1) . '">', '<img src="' . dol_buildpath('/hr/img/gplv3.png', 1) . '"/>', '</a>';

print '<h2>Licence</h2>';
print $langs->trans("LicenseMessage");
print '<h2>Bugs / comments</h2>';
print $langs->trans("AboutMessage");

$buffer = file_get_contents(dol_buildpath('/hr/CHANGELOG', 0));
echo Markdown($buffer);

llxFooter();

$db->close();

<?php
/* Copyright (C) 2014      Jean-François Ferry <jfefe@aternatik.fr>
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
 *    \file       todoist/admin/todoist.php
 *    \ingroup    todoist
 *    \brief      Page de configuration du module todoist
 */

// Dolibarr environment
$res='';
$res = @include("../../main.inc.php"); // From htdocs directory
if (! $res) {
	$res = @include("../../../main.inc.php"); // From "custom" directory
}
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';


if (!$user->admin)
    accessforbidden();


$langs->load("admin");
$langs->load("other");
$langs->load("todoist@todoist");

$def = array();

$action = GETPOST('action','alpha');

// Save parameters
if ($action == 'update')
{
    $i=0;

    $db->begin();

	$label  = GETPOST('todoist_LABEL','alpha');
    $exturl = GETPOST('todoist_URL','alpha');

    $i+=dolibarr_set_const($db,'todoist_LABEL',trim($label),'chaine',0,'',$conf->entity);
    $i+=dolibarr_set_const($db,'todoist_URL',trim($exturl),'chaine',0,'',$conf->entity);

    if ($i >= 2)
    {
        $db->commit();
        $mesg = "<font class=\"ok\">".$langs->trans("SetupSaved")."</font>";
    }
    else
    {
        $db->rollback();
        $mesg="<div class=\"error\">".$db->lasterror()."</div>";
    }
}


/**
 * View
 */

llxHeader();

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("todoistSetup"),$linkback,'setup');
print '<br>';

$h = 0;

$head[$h][0] = DOL_URL_ROOT."/admin/todoist.php";
$head[$h][1] = $langs->trans("Miscellaneous");
$head[$h][2] = 'todoist';
$hselected=$h;
$h++;

dol_fiche_head($head, $hselected, $langs->trans("Module577777Name"));

print $langs->trans("Module577777Description")."<br>\n";
print '<br>';

print '<form name="externalsiteconfig" action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="update">';
print "<table class=\"noborder\" width=\"100%\">";

print "<tr class=\"liste_titre\">";
print "<td width=\"30%\">".$langs->trans("Parameter")."</td>";
print "<td>".$langs->trans("Value")."</td>";
print "<td>".$langs->trans("Examples")."</td>";
print "</tr>";

$var=true;

$var=!$var;
print "<tr ".$bc[$var].">";
print '<td class="fieldrequired">'.$langs->trans("Label")."</td>";
print "<td><input type=\"text\" class=\"flat\" name=\"todoist_LABEL\" value=\"". (GETPOST('todoist_LABEL','alpha')?GETPOST('todoist_LABEL','alpha'):((empty($conf->global->todoist_LABEL) || $conf->global->todoist_LABEL=='ExternalSite')?'':$conf->global->todoist_LABEL)) . "\" size=\"12\"></td>";
print "<td>Todoist</td>";
print "</tr>";

$var=!$var;
print "<tr ".$bc[$var].">";
print '<td class="fieldrequired">'.$langs->trans("todoistURL")."</td>";
print "<td><input type=\"text\" class=\"flat\" name=\"todoist_URL\" value=\"". (GETPOST('todoist_URL','alpha')?GETPOST('todoist_URL','alpha'):(empty($conf->global->todoist_URL)?'':$conf->global->todoist_URL)) . "\" size=\"40\"></td>";
print "<td>https://todoist.com/app?lang=fr&v=731#start";
print "</td>";
print "</tr>";

print "</table>";


print '<br><center>';
print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
print "</center>";

print "</form>\n";


dol_htmloutput_mesg($mesg);


llxFooter();

$db->close();
?>

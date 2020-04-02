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
 * \file		htdocs/custom/timeclock/index.php
 * \ingroup		Time clock
 * \brief		Time clock index
 */

// Dolibarr environment
$res = @include("../../main.inc.php"); // From htdocs directory
if (! $res) {
  $res = @include("../../../main.inc.php"); // From "custom" directory
}

// Class
require_once '../class/timeclock.class.php';

// Langs
$langs->loadLangs(array('main','hrm','hr@hr'));

$action = GETPOST('action', 'alpha');
$id = GETPOST('id', 'int');
$userid = (GETPOST('userid')?GETPOST("userid"):'');
$enablepointage = 1; //On affiche le bouton Pointer
$nbEnreg = 0;
$num = -1;

$object=new Timeclock($db);

// Security check
if ($user->societe_id > 0)
	accessforbidden();

// Adresse IP
if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	$ip = $_SERVER['HTTP_X_FORWARDED_FOR']; 
elseif(isset($_SERVER['HTTP_CLIENT_IP']))   
	$ip = $_SERVER['HTTP_CLIENT_IP'];   
else
	$ip = $_SERVER['REMOTE_ADDR'];  

// Actions

// Nous regardons si un pointage est en cours pour cet user
$sql = 'SELECT rowid, fk_user, checking_arrival, ip_arrival, checking_exit, ip_exit, status, fk_user_modif, tms';
$sql.= ' FROM '.MAIN_DB_PREFIX.'timeclock';
$sql.= ' WHERE fk_user = '.$user->id;
$sql.= ' AND status = 1';

dol_syslog("custom/hr/index.php: view_pointage", LOG_DEBUG);
$resql=$db->query($sql);

if ($resql)
{
	$num = $db->num_rows($resql);

	if ($num > 0)
	{
		while ($nbEnreg < $num)
		{
			$obj = $db->fetch_object($resql);
			$nbEnreg++;
		}

		$object->fetch($obj->rowid);
		// On affiche le bouton Dépointer
		$enablepointage = 0;
	}
	else
	{
		// On affiche le bouton Pointer
		$enablepointage = 1;
	}
}
else
{
	dol_print_error($db);
}

if ($action == 'add')
{
	$now = dol_now();
	$now = strtotime(dol_print_date($now,'%Y-%m-%d %H:%M:%S','tzuser'));

	if ($num > 0) // Un pointage existe en base
	{
		$object->checking_exit = $now;
		$object->status = 2;
		$object->ip_exit = $ip;

		$result = $object->update($user);

		if ($result > 0)
		{
			// Creation OK
			$enablepointage = 1;
		}
		else
		{
			// Creation KO
			$mesg = $object->error;
		}
	}
	else // Aucun pointage pour cet user connu en base
	{
		$object->id = $id;
		$object->fk_user = $userid;
		$object->checking_arrival = $now;
		$object->ip_arrival = $ip;

		$object->status = 1;

		$result = $object->create($user);

		if ($result > 0)
		{
			// Creation OK
			$enablepointage = 0;
		}
		else
		{
			// Creation KO
			$mesg = $object->error;
		}
	}
}


/*
 * View
 */
$form=new Form($db);

$arrayofjs=array('custom/hr/js/timeclock.js','custom/hr/js/moment.min.js');
$arrayofcss=array('custom/hr/css/timeclock.css');

$title_page = $langs->trans("Timeclock");
llxHeader('', $title_page, '', '', 0, 0, $arrayofjs, $arrayofcss);

print load_fiche_titre($langs->trans("Timeclock"),'','title_hr@hr');

print '<div id="clock" class="light">';
print '<div class="display">';
print '<div class="weekdays"></div>';
// print '<div class="ampm"></div>';
// print '<div class="alarm"></div>';
print '<div class="digits"></div>';
print '</div>';
print '</div>';

print '<form name="crea_pointage" action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<input type="hidden" name="action" value="add">';
print '<input type="hidden" name="userid" value="'.$user->id.'">';

print '<table class="nobordernopadding">';
if (dol_strlen($object->checking_arrival)!=0 && $object->status==1)
{
	print '<tr><td> '.$langs->trans("LastpointageDo").': </td>';
	print '<td>'.dol_print_date($object->checking_arrival, '%d/%m/%Y %H:%M:%S').'</td></tr>';
}

print '<div class="center">';
if ($enablepointage == 1)
{
	print '<input type="submit" class="butAction" name="bouton_pointer" value="'.$langs->trans("Pointer").'">';
}
else
{
	print '<input type="submit" class="butAction" name="bouton_depointer" value="'.$langs->trans("Depointer").'">';
}
print '</div></td>';

print '</table>';
print '</div>';
 
print '</form>';

llxFooter();
$db->close();

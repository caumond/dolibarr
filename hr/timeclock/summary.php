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

// Dolibarr environment
$res = @include("../../main.inc.php"); // From htdocs directory
if (! $res) {
  $res = @include("../../../main.inc.php"); // From "custom" directory
}

// Class
require_once '../class/timeclock.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

// Langs
$langs->loadLangs(array('main','hrm','hr@hr'));

$action = GETPOST('action', 'alpha');

//Format d'affichage de la date
$myDateFormat="%d/%m/%Y %H:%M:%S";

// Get parameters
$datefrom=-1;
$dateto=-1;

// Security check
if ($user->societe_id > 0)
	accessforbidden();

function getDateNow()
{
	$now = dol_now();
	return strtotime(dol_print_date($now,'%Y-%m-%d %H:%M:%S','tzuser'));
}
	
function getRecap($datefrom,$dateto,$myselectedUserId,$db,$user)
{
	$sql = "SELECT";
	$sql.= " l.fk_user,";
	$sql.= " SUM(l.worktime) as worktime,";
	$sql.= " l.lastname,";
	$sql.= " l.firstname,";
	$sql.= " l.login";
	$sql.= " FROM (SELECT r.fk_user, TIMESTAMPDIFF(second,r.checking_arrival,r.checking_exit) as worktime, u.lastname, u.firstname, u.login";
		$sql.= " FROM ".MAIN_DB_PREFIX."timeclock as r,";
		$sql.= " ".MAIN_DB_PREFIX."user as u";
		$sql.=" WHERE r.fk_user = u.rowid AND r.status = 2 ";


 	// Gestion des droits :
 	if ($user->rights->hr->timeclock->summary)
	{
		if(empty($myselectedUserId) || $myselectedUserId=='' || $myselectedUserId==-1)
		{
			// Cas 2: l'utilisateur connecté a les droits requis : la page est affiché pour la premiere fois
			// On affiche tous les utilisateurs dans le selectBox			
		}
		else
		{
			// Un utilisateur a été sélectioné on affiche les pointage de ce dernier
			$sql.= ' AND r.fk_user = '.$myselectedUserId;
		}
	}
	else
	{		 
		// Cas1: Le selectBox est caché : on est dans le cas où l'utilisateur n'a pas les droits requis
		// On affiche seulement les lignes de l'utilisateur connecté
		$sql.= ' AND r.fk_user = '.$user->id;
	}
		 
	if ($datefrom!=-1)
		$sql.= "   AND r.checking_arrival >='".$db->idate($datefrom)."'";

	if ($dateto!=-1)
	 	$sql.= "   AND r.checking_exit <='". $db->idate($dateto)."'";
			
	$sql.= ') as l group by l.fk_user ORDER BY l.lastname, l.firstname ASC';
		
	return $sql;
}

function getmytime($ptimeInsec)
{
	$myHeure=intval($ptimeInsec/3600);
	$myMinute=intval(($ptimeInsec - $myHeure*3600)/60);
	$mySeconde= intval(($ptimeInsec - $myHeure*3600 -$myMinute*60));
	
	return $myHeure .'H ' . $myMinute . 'M ' .$mySeconde . 'S';
}

// Actions

//cas: rechercher
if ($user->rights->hr->timeclock->summary)
if ($action == 'find' || $action == '')
{
	if($_POST['datefrommonth']!='')
	{
		$datefrom=dol_mktime(0,0,0,$_POST['datefrommonth'],$_POST['datefromday'],$_POST['datefromyear']);
		$dateto= dol_mktime(23,59,59,$_POST['datetomonth'],$_POST['datetoday'],$_POST['datetoyear']);
	}
	else
	{
		$yearCurrent = strftime("%Y",getDateNow());
		$monthCurrent = strftime("%m",getDateNow());
		
		$datefrom=dol_mktime(0,0,0,$monthCurrent,1,$yearCurrent);
		$dateto=getDateNow();
	}
	
	$myselectedUserId=GETPOST('selectedUser');

	//génération de la requete
	//$sql = getBaseReqUserPointage();
	$sql=getRecap($datefrom,$dateto,$myselectedUserId,$db,$user);
}

/*
 * View
 */
llxHeader('',$langs->trans("Summary"));

$form=new Form($db);

print '<form name="crea_histo" action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<input type="hidden" name="action" value="find">';
print '<input type="hidden" name="userid" value="'.$user->id.'">';

print load_fiche_titre($langs->trans("Summary"),'','title_hr@hr');

if ($user->rights->hr->timeclock->summary)
{
	print '<div class="liste_titre liste_titre_bydiv centpercent">';
	print '<div class="divsearchfield">';
	print $langs->trans('User'). ': ';
	print $form->select_users(GETPOST('selectedUser'),'selectedUser',1,'');
	print '</div>';
	print '<div class="divsearchfield">';
	print $langs->trans('DateFrom'). ': ';
	if ($datefrom!=-1)
	{
		print $form->select_date($datefrom,'datefrom',0,0,0,"find");
	}
	else
	{
		print $form->select_date($datefrom,'datefrom',0,0,0,"find");
	}
	print $langs->trans('To'). ': ';
	if ($dateto!=-1)
	{
		print $form->select_date($dateto,'dateto',0,0,0,"find");
	}
	else
	{
		print $form->select_date($dateto,'dateto',0,0,0,"find");	
	}
	print '</div>';
	print '<div class="center divsearchfield">';
	print '<input type="submit" class="butAction" name="search" value="'.$langs->trans('Search').'">';
	print '</div>';
	print '</div>';
}

print '<br>';
print '<table class="border" width="100%">';

print '<tr class="liste_titre">';
print '<td class="liste_titre" align="center">'.$langs->trans("User") .' &nbsp;</td>';
print '<td class="liste_titre" align="center">'.$langs->trans("WorkingTimeOverThePeriod").'</td>';
print "</tr>\n";

$resql = $db->query($sql) ;
if ($resql)
{
	$num = $db->num_rows($resql);
	if ($num > 0)
	{
		$nbEnreg=1;
		while ($nbEnreg <= $num)
		{
			// Coloration alternée des lignes du tableau
			if($nbEnreg%2==0)
				print "<tr class='pair' >";
			else
				print "<tr class='impair' >";

			$objp = $db->fetch_object($resql);

			$userstatic = new User($db);
			$userstatic->fetch($objp->fk_user);

			print '<td>' . $userstatic->getNomUrl(1,'') .'</td>';
			print '<td align="right">'. getmytime($objp->worktime) .' </td>';			

			$nbEnreg++;
			print "</tr>\n";
		}
	}
}
else
{
	//dol_print_error($db);
}

print '</table>';
print '</div>';
print '</form>';

llxFooter();
$db->close();

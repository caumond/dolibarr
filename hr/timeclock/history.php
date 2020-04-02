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

// Langs
$langs->loadLangs(array('main','hrm','hr@hr'));

$action = GETPOST('action', 'alpha');

// Load variable for pagination
$limit = GETPOST('limit','int')?GETPOST('limit','int'):$conf->liste_limit;
$sortfield = GETPOST('sortfield', 'alpha');
$sortorder = GETPOST('sortorder', 'alpha');
$page = GETPOST('page','int');
if (empty($page) || $page < 0) { $page = 0; }
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if ($sortorder == "") $sortorder = "ASC";
if ($sortfield == "") $sortfield = "t.piece_num,t.rowid";

//Format d'affichage de la date
$myDateFormat="%d/%m/%Y %H:%M:%S";

// Get parameters
$datefrom=-1;
$dateto=-1;

// Security check
if ($user->societe_id > 0)
	accessforbidden();

if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter.x") || GETPOST("button_removefilter")) // Both test are required to be compatible with all browsers
{
	$myselecteduserId="";
}

function getDateNow()
{
	$now = dol_now();
	return strtotime(dol_print_date($now,'%Y-%m-%d %H:%M:%S','tzuser'));
}

function getBaseReqUserPointage()
{
	return 'SELECT t.rowid, t.fk_user, t.checking_arrival, t.checking_exit, t.status, u.rowid as userId, u.lastname, u.firstname ,u.login'
	.' FROM '.MAIN_DB_PREFIX.'timeclock as t , ' .MAIN_DB_PREFIX. 'user as u'
	.' WHERE t.fk_user = u.rowid';
}

function getResteReqUserPointage($datefrom,$dateto,$myselecteduserId,$db,$user)
{
	// Gestion des droits :
	if ($user->rights->hr->timeclock->see_historic)
	{
		if(empty($myselecteduserId) || $myselecteduserId=='' || $myselecteduserId==-1)
		{
			// Cas 2: l'utilisateur connecté a les droits requis : la page est affiché pour la premiere fois
			// On affiche tous les utilisateurs dans le selectBox
		}
		else
		{
			// Un utilisateur a été sélectioné on affiche les pointage de ce dernier
			$sql1.= ' AND t.fk_user = '.$myselecteduserId;
		}
	}
	else
	{
		// Cas1: Le selectBox est caché : on est dans le cas où l'utilisateur n'a pas les droits requis
		// On affiche seulement les lignes de l'utilisateur connecté
		$sql1.= ' AND t.fk_user = '.$user->id;
	}

	$sql1.= '   AND  t.status = 2'; // status du pointage fini.

	if ($datefrom!=-1)
		$sql1.= " AND t.checking_arrival >='".$db->idate($datefrom)."'";

	if ($dateto!=-1)
		$sql1.= " AND t.checking_exit <='". $db->idate($dateto)."'";

	$sql1.= " ORDER BY u.lastname, u.firstname ASC";

	return $sql1;
}


// Actions

//gestion de droit : si lutilisateur n'a pas les droits.
if ($user->rights->hr->timeclock->see_historic)
//updateline: update de la ligne sélectionné dans le tableau
if ($action == 'updateline')
{
	$myIdRow=GETPOST("lineid");
	$myPointageRow=new Timeclock($db);
	$myPointageRow->fetch($myIdRow) ;

	$datedebToSave=dol_mktime($_POST['datedebToSavehour'],$_POST['datedebToSavemin'],0,$_POST['datedebToSavemonth'],$_POST['datedebToSaveday'],$_POST['datedebToSaveyear']);

	$datefinToSave= dol_mktime($_POST['datefinToSavehour'],$_POST['datefinToSavemin'],0,$_POST['datefinToSavemonth'],$_POST['datefinToSaveday'],$_POST['datefinToSaveyear']);

	//r.rowid, r.fk_user, r.datedeb, r.datefin, r.status', fk_user_modify_by , datemodif ,ip_user
	$myPointageRow->checking_arrival=$datedebToSave;
	$myPointageRow->checking_exit=$datefinToSave;
	$myPointageRow->fk_user_modify_by=$user->id;
	$myPointageRow->datemodif=getDateNow();

	// Nous recuperons le nom du bouton ayant appeler cette action
	$myButtonName = GETPOST('bouton_update');
	
	if (empty($myButtonName))
	{
		$action='';
	}
	else 
	{
		$result=$myPointageRow->update($user);
	
		if ($result > 0)
		{
			// Creation OK
			//Réaffichage du tableau après une mise à jour
			$action = '';
		}
		else
		{
			// Creation KO
			$mesg=$myobject->error;
		}
	}
}

// Cas de la suppression action : deleteline
if ($user->rights->hr->timeclock->see_historic) //remplacer par delete
if ($action == 'deleteline')
{
	if(GETPOST('lineid')!='')
	{
		$myIdRow=GETPOST('lineid');
		$myPointageRow=new Timeclock($db);

		if($myPointageRow->fetch($myIdRow) ==1)
		{
			//On ne suprimme pas mais on passe le status à 0
			$myPointageRow->status=-1; // status du pointage supprimer
			//
			$myPointageRow->fk_user_modify_by=$user->id;
			$myPointageRow->datemodif=getDateNow();

			$result=$myPointageRow->update($user);

			if ($result > 0)
			{
				// Creation OK
				//Réaffichage du tableau après une mise à jour
				$action = '';
			}
			else
			{
				// Creation KO
				$mesg=$myobject->error;
			}
		}
	}
}

//
if ($action == 'addline')
{
	$datefrom=GETPOST('datefrom');
	$dateto=GETPOST('dateto');
	$myselecteduserId=GETPOST('selecteduser');

	//génération de la requete
	$sql = getBaseReqUserPointage();
	$sql.= getResteReqUserPointage($datefrom,$dateto,$myselecteduserId,$db,$user);
}

//
if ($action == 'saveline')
{
	$selecteduserId=$_POST['selecteduserId'];

	$myNewPointage=new Timeclock($db);

	$datedebCreate=dol_mktime($_POST['datedebCreatehour'],$_POST['datedebCreatemin'],0,$_POST['datedebCreatemonth'],$_POST['datedebCreateday'],$_POST['datedebCreateyear']);
	$datefinCreate= dol_mktime($_POST['datefinCreatehour'],$_POST['datefinCreatemin'],0,$_POST['datefinCreatemonth'],$_POST['datefinCreateday'],$_POST['datefinCreateyear']);

	//r.rowid, r.fk_user, r.datedeb, r.datefin, r.status', fk_user_modify_by , datemodif ,ip_user
	$myNewPointage->fk_user=$selecteduserId;
	$myNewPointage->checking_arrival=$datedebCreate;
	$myNewPointage->checking_exit=$datefinCreate;
	$myNewPointage->status=2; //Pointage fini: Entrer et sortie effectuée
	$myNewPointage->fk_user_modify_by=$user->id;
	$myNewPointage->datemodif=getDateNow();

	//$myNewPointage->ip_user //A faire
	//Nous recuperons le nom du bouton ayant appeler cette action
	$myButtonName = GETPOST('bouton_update');

	if (empty($myButtonName))
	{
		$action='';
	}
	else 
	{
		$result=$myNewPointage->create($user);

		if ($result > 0)
		{
			// Creation OK
			//Réaffichage du tableau après une mise à jour
			$action = '';
		}
		else
		{
			// Creation KO
			$mesg=$myobject->error;
		}
	}
}

// cas: rechercher
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

	$myselecteduserId=GETPOST('selecteduser');

	//génération de la requete
	$sql = getBaseReqUserPointage();
	$sql.=getResteReqUserPointage($datefrom,$dateto,$myselecteduserId,$db,$user);
}

// Cas de la  modification d'une ligne
if ($user->rights->hr->timeclock->see_historic)
if ($action == 'editline')
{
	if(GETPOST('datefrom')!='')
	{
		$datefrom=GETPOST('datefrom');
		$dateto=GETPOST('dateto');
		$myselecteduserId=GETPOST('selecteduser');

		//génération de la requete
		$sql = getBaseReqUserPointage();
		$sql.= getResteReqUserPointage($datefrom,$dateto,$myselecteduserId,$db,$user);
	}
}

/***************************************************
View
****************************************************/
$title_page = $langs->trans("History");

llxHeader('',$title_page);

$form=new Form($db);
$object=new Timeclock($db);

if (! $action == 'addline' && $user->rights->hr->timeclock->see_historic)
{
	$newcardbutton = '<a class="butActionNew" href="'.$_SERVER['PHP_SELF'].'?action=addline' . '&amp;datefrom='. $datefrom .'&amp;dateto='. $dateto .'&amp;selecteduser='.GETPOST('selecteduser') .'"><span class="valignmiddle">'. $langs->trans("AddManualEntry").'</span>';
	$newcardbutton.= '<span class="fa fa-plus-circle valignmiddle"></span>';
	$newcardbutton.= '</a>';
}

print_barre_liste($title_page, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, $button, $result, $nbtotalofrecords, 'title_hr@hr', 0, $groupby.$newcardbutton, '', $limit);

// Put here content of your page
print '<form name="crea_histo" action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<input type="hidden" name="action" value="find">';
print '<input type="hidden" name="idUser" value="'.$user->id.'">';

print '<div class="div-table-responsive">';
print '<table class="tagtable liste" width="100%">';

print '<tr class="liste_titre">'; 
print '<td class="liste_titre" align="left">'.$langs->trans("User") .'</td>';
print '<td class="liste_titre" align="left">'.$langs->trans("Checking_arrival").'</td>';
print '<td class="liste_titre" align="left">'.$langs->trans("Checking_exit").'</td>';
print '<td class="liste_titre" align="left">&nbsp;</td>';
print "</tr>\n";

print '<tr class="liste_titre">';
print '<td>';
if ($user->rights->hr->timeclock->see_historic)
	print $form->select_users(GETPOST('selecteduser'),'selecteduser',1,'');
print '&nbsp;</td>';

print '<td class="liste_titre">';
if ($datefrom!=-1)
	print $form->select_date($datefrom,'datefrom',0,0,0,"find");
else
{
	print $form->select_date($datefrom,'datefrom',0,0,0,"find");
}
print '&nbsp;</td>';

print '<td class="liste_titre" >';
if ($dateto!=-1)
	print $form->select_date($dateto,'dateto',0,0,0,"find");
else
{
	print $form->select_date($dateto,'dateto',0,0,0,"find");
}
print '</td>';

// Afficher bouton rechercher ou annuler
print '<td class="liste_titre" align="right">';
if(! $action == 'addline')
{
	$searchpicto=$form->showFilterAndCheckAddButtons(0);
	print $searchpicto;
}
print '</td>';
print '</td>';
print '</tr>';
print '</form>';

print '<form name="insertpointage" action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<input type="hidden" name="action" value="saveline">';
print '<input type="hidden" name="idUser" value="'.$user->id.'">';

if ($action == 'addline' && $user->rights->hr->timeclock->see_historic)
{
	// Coloration alternée des lignes du tableau
	print "<tr class='pair'>";

	// fk_user 	datedeb 	datefin 	commentaire_in 	commentaire_out fk_user_modify_by 	datemodif 	ip_user
	print '<td>';
	print $form->select_users(GETPOST('selecteduserId'),'selecteduserId',0,'');
	print '&nbsp;</td>';

	print '<td>';
	print $form->select_date(getDateNow(),'datedebCreate',1,1,0,"find");
	print '</td>';

	print '<td>';
	print $form->select_date(getDateNow(),'datefinCreate',1,1,0,"find");
	print '</td>';

	// print '<input type="hidden" name="lineid" value="'.$objp->rowid.'">';
	print '<input type="hidden" name="selecteduser" value="'.GETPOST('selecteduser').'">';

	// Passage des date dans le post pour les récupérer car elles ne font pas partie de cette form
	print '<input type="hidden" name="datefrom" value="'.$datefrom.'">';
	print '<input type="hidden" name="dateto" value="'.$dateto.'">';

	// Buttons
	print '<td align="center"><input type="submit" class="button" name="bouton_update" value="'.$langs->trans('Save').'">';
	print '&nbsp;<input type="submit" class="button" name="cancel" value="'.$langs->trans('Cancel').'"></td>';

	print '</tr>';
}
print '</form>';

// Entête des texbox de recherche : Fin
print '<form name="update_histo" action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<input type="hidden" name="action" value="updateline">';
print '<input type="hidden" name="idUser" value="'.$user->id.'">';

// Affichage du tableau contenant la liste des pointages : Debut
// Lance la requete
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
				print "<tr class='pair'>";
			else
				print "<tr class='impair'>";

			if($action== 'editline')
			{
				$objp = $db->fetch_object($resql);
				// Cas où l'utilisateur choisit de modifier une ligne
				if($objp->rowid==$_GET['lineid'])
				{
					$userstatic = new User($db);
					$userstatic->fetch($objp->fk_user);

					// r.rowid, r.fk_user, r.datedeb, r.datefin, r.commentaire_in, r.commentaire_out, r.status '
					print '<td>' .$userstatic->getNomUrl(1,'').'</td>';

					print '<td>';
					print $form->select_date(strftime($db->jdate($objp->checking_arrival)),'datedebToSave',1,1,0,"find");
					print '</td>';

					print '<td>';
					print $form->select_date(strftime($db->jdate($objp->checking_exit)),'datefinToSave',1,1,0,"find");
					print '</td>';
					print '<input type="hidden" name="lineid" value="'.$objp->rowid.'">';
					print '<input type="hidden" name="selecteduser" value="'.GETPOST('selecteduser').'">';

					//Passage des date dans le post pour les récupérer car elles ne font pas partie de cette form
					print '<input type="hidden" name="datefrom" value="'.$datefrom.'">';
					print '<input type="hidden" name="dateto" value="'.$dateto.'">';

					// Search button
					print '<td class="right"><input type="submit" class="button" name="bouton_update" value="'.$langs->trans('Save').'">'.
					'&nbsp;<input type="submit" class="button" name="cancel" value="'.$langs->trans('Cancel').'"></td>';
				}
				// Cas classique (lignes qui ne sont pas à modifier)
				else
				{
					$userstatic = new User($db);
					$userstatic->fetch($objp->fk_user);

					// r.rowid, r.fk_user, r.datedeb, r.datefin, r.commentaire_in, r.commentaire_out, r.status'
					print '<td>' .$userstatic->getNomUrl(1,'').'</td>'
					 .'<td>'. adodb_strftime($myDateFormat,$db->jdate($objp->checking_arrival)) .' &nbsp;</td>'
					 .'<td>'. adodb_strftime($myDateFormat,$db->jdate($objp->checking_exit)) .' &nbsp;</td>';

					// Action buttons
					print '<td align="right" class="liste_titre">';
					if ($user->rights->hr->timeclock->update_historic)  //gestion de droit
						print '<a href="'.$_SERVER['PHP_SELF'].'?lineid='.$objp->rowid.
						'&amp;action=editline' . '&amp;datefrom='. $datefrom .'&amp;dateto='. $dateto
						.'&amp;selecteduser='.GETPOST('selecteduser') .'">'. img_edit() .'</a>';

					if ($user->rights->hr->timeclock->delete_historic)  //gestion de droit
						print '&nbsp;&nbsp;&nbsp;'.'<a href="'.$_SERVER['PHP_SELF'].'?lineid='.$objp->rowid.
						'&amp;action=deleteline' /* . '&amp;datefrom='. $datefrom .'&amp;dateto='. $dateto  */
						.'&amp;selecteduser='.GETPOST('selecteduser') .'">'. img_delete() .'</a>'.'</td>';
					print '&nbsp;</td>';
				}
			}
			else
			{
				$objp = $db->fetch_object($resql);

				$userstatic = new User($db);
				$userstatic->fetch($objp->fk_user);

				// r.rowid, r.fk_user, r.datedeb, r.datefin, r.commentaire_in, r.commentaire_out, r.status'
				print '<td>' .$userstatic->getNomUrl(1,'').'</td>'
					 .'<td>'.adodb_strftime($myDateFormat,$db->jdate($objp->checking_arrival)) .' &nbsp;</td>'
					 .'<td>'.adodb_strftime($myDateFormat,$db->jdate($objp->checking_exit)) .' &nbsp;</td>';

				// Action buttons
				print '<td align="right" class="liste_titre">';
				if ($user->rights->hr->timeclock->update_historic)  //gestion de droit
					print '<a href="'.$_SERVER['PHP_SELF'].'?lineid='.$objp->rowid.
					'&amp;action=editline' . '&amp;datefrom='. $datefrom .'&amp;dateto='. $dateto
					.'&amp;selecteduser='.GETPOST('selecteduser') .'">'. img_edit() .'</a>';

					if ($user->rights->hr->timeclock->delete_historic)  //gestion de droit
						print '&nbsp;&nbsp;&nbsp;'.'<a href="'.$_SERVER['PHP_SELF'].'?lineid='.$objp->rowid.
						'&amp;action=deleteline' /* . '&amp;datefrom='. $datefrom .'&amp;dateto='. $dateto  */
						.'&amp;selecteduser='.GETPOST('selecteduser') .'">'. img_delete() .'</a>'.'</td>';
					print '&nbsp;</td>';
			}
			$nbEnreg++;
			print "</tr>\n";
		}
	}
}
else
{
	//dol_print_error($db);
}
// Affichage du tableau contenant la liste des pointages : FIN

print '</table>';
print '</div>';
 
print '</form>';

llxFooter();
$db->close();

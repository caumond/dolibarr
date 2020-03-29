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
 *
 */

/**
 *     \file       htdocs/todoist/frames.php
 *     \ingroup    todoist
 *     \brief      Page that build two frames: One for menu, the other for the target page to show
 *     \author	   Laurent Destailleur
 */

// Dolibarr environment
$res='';
$res = @include("../main.inc.php"); // From htdocs directory
if (! $res) {
	$res = @include("../../main.inc.php"); // From "custom" directory
}

$langs->load("todoist@todoist");

if (empty($conf->global->todoist_URL))
{
	llxHeader();
	print '<div class="error">'.$langs->trans('todoistModuleConfigurationNotComplete').'</div>';
	llxFooter();
}

$mainmenu=GETPOST('mainmenu', 'alpha');
$leftmenu=GETPOST('leftmenu', 'alpha');
$idmenu=GETPOST('idmenu', 'int');
$theme=GETPOST('theme', 'alpha');
$codelang=GETPOST('lang', 'alpha');

print "
<html>
<head>
<title>Dolibarr frame for todoist</title>
</head>

<frameset rows=\"".$heightforframes.",*\" border=0 framespacing=0 frameborder=0>
    <frame name=\"barre\" src=\"todoist_frametop.php?mainmenu=".$mainmenu."&leftmenu=".$leftmenu."&idmenu=".$idmenu.($theme?'&theme='.$theme:'').($codelang?'&lang='.$codelang:'')."&nobackground=1\" noresize scrolling=\"NO\" noborder>
    <frame name=\"main\" src=\"".$conf->global->todoist_URL."\">
    <noframes>
    <body>

    </body>
    </noframes>
</frameset>

<noframes>
<body>
	<br><center>
	Sorry, your browser is too old or not correctly configured to view this area.<br>
	Your browser must support frames.<br>
	</center>
</body>
</noframes>

</html>
";


?>

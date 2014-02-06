<?php
/*************************************************************************************/
/*                                                                                   */
/*      Module VOD Infomaniak pour Thelia	                                         */
/*                                                                                   */
/*      Copyright (c) Openstudio 		                                     		 */
/*      Développement : Christophe LAFFONT		                                     */
/*		email : claffont@openstudio.fr	        	                             	 */
/*      web : http://www.openstudio.fr					   							 */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 2 of the License, or            */
/*      (at your option) any later version.                                          */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*      along with this program; if not, write to the Free Software                  */
/*      Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA    */
/*                                                                                   */
/*************************************************************************************/

// Vérifier authorisation
include_once __DIR__ . "/../../../fonctions/authplugins.php";
autorisation("prepayment");

include_once __DIR__ . "/admin/action.php";
?>

<div id="contenu_int">
<?php
	switch($_REQUEST['action_prepayment'])
	{
		case 'configuration' :
			include_once(__DIR__ . "/admin/configuration.php");
			break;

        case 'historique' :
            include_once(__DIR__ . "/admin/historique.php");
            break;

        case 'solde' :
            include_once(__DIR__ . "/admin/solde.php");
            break;

        default:
			include_once(__DIR__ . "/admin/default.php");
	}
?>
</div>


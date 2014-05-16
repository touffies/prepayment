<?php
/*************************************************************************************/
/*                                                                                   */
/*      Module de Prépaiement pour Thelia	                                         */
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

// Constante
define("PREPAYMENT_ID", 1);

define("PREPAYMENT_CREDIT", 1);
define("PREPAYMENT_DEBIT", 2);

define("PREPAYMENT_TYPE_PRIX", 0); // On utilise le prix
define("PREPAYMENT_TYPE_QUANTITE", 1); // On utilise la quantité

define("PREPAYMENT_MINIMUM", 0); // Minimum autorisé pour le compte client

// Nom du module à utiliser si on veut avoir aucun frais de livraison
define("PREPAYMENT_LIVRAISON_ZERO", "livraison_zero");

define("PREPAYMENT_URL_SUCCES", "merci");
define("PREPAYMENT_URL_ERREUR", "regret");

// Exclure les prepayments dans les statuts ci-dessous (Non payé et Annulé)
define('PREPAYMENT_STATUT_EXCLUSION', '1,5');
?>

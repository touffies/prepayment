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

include_once __DIR__ . "/../../../../classes/Baseobj.class.php";

/**
 * Class Prepayment_produit
 *
 * Cette classe permet de définir les produits autorisés à utiliser la méthode de paiement prépayé.
 */
class Prepayment_produit extends Baseobj {

    public $prepayment_id;
    public $produit_id;

	const TABLE = "prepayment_produit";

	public $table = self::TABLE;

	public $bddvars = array("prepayment_id", "produit_id");

    /**
     * Constructeur
     *
     * @param int/null $id Possibilité de charger une commande de prépayement en passant son identifiant
     */
    function __construct($id = null) {

		parent::__construct();

		if (intval($id) > 0) $this->charger($id);
	}

    /**
     * Chargé un objet Prepayment_produit en fonction de l'identifiant d'un produit
     *
     * @param int $produit_id  Identifiant d'un produit
     *
     * @return objet Un objet Prepayment_produit
     */
    function charger_produit($produit_id){

        return $this->getVars("SELECT * FROM $this->table WHERE produit_id=" . intval($produit_id));
    }

    /**
     * Initialisation du plugin, création de la table si elle n'existe pas encore
     *
     * @return  none
     */
    public function init() {

        $query = "CREATE TABLE IF NOT EXISTS `$this->table` (
			 `prepayment_id` INT UNSIGNED NOT NULL,
			 `produit_id` INT UNSIGNED NOT NULL,
			PRIMARY KEY (  `prepayment_id` )
			);";

        $this->query($query);

    }

    /**
     * Méthode appelée quand on désactive le plugin
     *
     * @return none
     */
    public function destroy() {
		// Rien
	}
}
?>
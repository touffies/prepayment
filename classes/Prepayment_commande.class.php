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
 * Class Prepayment_commande
 *
 * Cette classe permet de gérer les commandes de type debit (Un achat) ou
 * les commandes de type credit (Une recharge) pour les comptes prépayés
 */
class Prepayment_commande extends Baseobj {

    public $id;
    public $prepayment_id;
    public $commande_id;
    public $client_id;
    public $type;
    public $valeur;

	const TABLE = "prepayment_commande";

	public $table = self::TABLE;

	public $bddvars = array("id", "prepayment_id", "commande_id", "client_id", "type", "valeur");

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
     * Chargé un objet Prepayment_commande en fonction dde l'identifiant d'une commande
     *
     * @param int $commande_id  Identifiant d'une commande
     *
     * @return objet Un objet Prepayment_commande
     */
    function charger_commande($commande_id){

        return $this->getVars("SELECT * FROM $this->table WHERE commande_id=" . intval($commande_id));
    }

    /**
     * Initialisation du plugin, création de la table si elle n'existe pas encore
     *
     * @return  none
     */
    public function init() {

        $query = "CREATE TABLE IF NOT EXISTS `$this->table` (
			 `id` INT NOT NULL auto_increment,
			 `prepayment_id` INT UNSIGNED NOT NULL,
			 `commande_id` INT UNSIGNED NOT NULL ,
			 `client_id` INT UNSIGNED NOT NULL ,
		  	 `type` TINYINT(1) UNSIGNED NOT NULL,
		  	 `valeur` FLOAT NOT NULL,
			PRIMARY KEY (  `id` )
			) AUTO_INCREMENT=1 ;";

        $this->query($query);

    }

    /**
     * Méthode appelée pour calculer le nombre de crédit d'un client en faissant la somme des crédits et des débits
     * d'un client ayant des commandes en statut payé
     *
     * @param int Identifiant du client
     * @param int Identifiant du prepayment
     *
     * @return int Total de crédit d'un client
     */
    public function credit_total($client_id, $prepayment_id){

        $type_credit = defined('PREPAYMENT_CREDIT') ? PREPAYMENT_CREDIT : 1;
        $query = "SELECT co.id, SUM(CASE WHEN pc.type = ".$type_credit." THEN +pc.valeur ELSE -pc.valeur END) AS Total FROM ".self::TABLE." pc INNER JOIN ".Commande::TABLE." co ON co.id = pc.commande_id WHERE pc.client_id = $client_id AND pc.prepayment_id = $prepayment_id AND co.statut = '2'";
        $res = $this->query_liste($query);
        return intval($res[0]->Total);
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
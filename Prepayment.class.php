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

include_once __DIR__ . "/config.php";

// Classes du plugin
include_once __DIR__ . "/classes/Prepayment_produit.class.php";
include_once __DIR__ . "/classes/Prepayment_commande.class.php";

// Classes de Thelia
include_once __DIR__ . "/../../../classes/PluginsPaiements.class.php";
include_once __DIR__ . "/../../../classes/Caracteristiquedesc.class.php";
include_once __DIR__ . "/../../../classes/Produit.class.php";
include_once __DIR__ . "/../../../classes/Produitdesc.class.php";
include_once __DIR__ . "/../../../classes/Client.class.php";
include_once __DIR__ . "/../../../classes/Modules.class.php";
include_once __DIR__ . "/../../../classes/Caracval.class.php";
include_once __DIR__ . "/../../../classes/Venteprod.class.php";
include_once __DIR__ . "/../../../classes/Devise.class.php";

// Fonctions de thelia
include_once __DIR__ . "/../../../fonctions/lire.php";

/**
 * Class Prepayment
 *
 * Cette classe permet de d'associer une caratéristique à un prepayment.
 */
class Prepayment extends PluginsPaiements {

	const MODULE = "prepayment";

    public $id;
    public $caracteristique_id;
    public $type;

    const TABLE = "prepayment";
    public $table = self::TABLE;

    public $bddvars = array("id", "caracteristique_id", "type");

    /**
     * Constructeur
     *
     * @param int/null $id Possibilité de passer un identifiant pour charger un objet Prepayment
     */
    function __construct($id = null)
    {

        parent::__construct(self::MODULE);

        if (intval($id) > 0) $this->charger_id($id);
    }

    /**
     * Initialisation du plugin, création des tables si elles n'existent pas encore
     *
     * @return none
     */
    function init()
    {

        $this->ajout_desc("Débiter mon compte prépayé", "prepayment", "", 1);

        // Table de liaison entre la méthode de prépaiement et sa caractéristique
        $query =
            "CREATE TABLE IF NOT EXISTS `$this->table` (
			 `id` INT NOT NULL auto_increment,
			 `caracteristique_id` INT UNSIGNED NOT NULL,
			 `type` SMALLINT(6) UNSIGNED NOT NULL,
			PRIMARY KEY (  `id` )
			) AUTO_INCREMENT=1 ;";
        $this->query($query);

        // On initialise
        $prepayment_produit = new Prepayment_produit();
        $prepayment_produit->init();

        $prepayment_commande = new Prepayment_commande();
        $prepayment_commande->init();

    }

    /**
     * Chargé un objet Prepayment en fonction d'une caractéristique'
     *
     * @param int $caracteristique_id  Identifiant d'un produit
     *
     * @return objet Un objet Prepayment
     */
    function charger_caracteristique($caracteristique_id){
        return $this->getVars("SELECT * FROM $this->table WHERE caracteristique_id=" . intval($caracteristique_id));
    }

    /**
     * Méthode appelée lors de la sauvegarde d'une commande, on vérifie si un produit est associé à
     * une caratéristique permettant de créditer un compte client
     *
     * @param $commande Objet de type commande (Info de la commande courante)
     *
     * @return none
     */
    public function aprescommande($commande)
    {

        foreach($_SESSION['navig']->panier->tabarticle as &$art) {

            // On recherche toutes les caractéristiques de prepayment
            $query = "SELECT * FROM $this->table";
            $resul = $this->query($query);
            while($resul && $prepayment = $this->fetch_object($resul)){
                $caracval = new Caracval();
                if($caracval->charger($art->produit->id, $prepayment->caracteristique_id))
                {
                    $prepayment_commande = new Prepayment_commande();
                    $prepayment_commande->prepayment_id = $prepayment->id;
                    $prepayment_commande->commande_id = $commande->id;
                    $prepayment_commande->client_id = $commande->client;
                    $prepayment_commande->type = defined('PREPAYMENT_CREDIT') ? PREPAYMENT_CREDIT : 1;
                    $prepayment_commande->valeur = $caracval->valeur;
                    $prepayment_commande->add();
                }
            }
        }
    }

    /**
     * Méthode appelée lors de la modification d'une fiche produit
     *
     * @param Produit $produit Objet de type produit
     *
     * @return none
     */
    public function modprod(Produit $produit)
    {
        $select_prepayment = trim(lireParam("select_prepayment", "int"));

        // On met à jour la table de liaison prepayment_produit
        $prepayment_produit = new Prepayment_produit();

        // On ne peut associer qu'un prepayment
        if($prepayment_produit->charger_produit($produit->id))
            $prepayment_produit->delete_produit($produit->id);


        // Une caractéristique de prépaiement est séléctionnée
        if (intval($select_prepayment) > 0)
        {
            // On ajoute une entrée
            $prepayment_produit = new Prepayment_produit();
            $prepayment_produit->produit_id = $produit->id;
            $prepayment_produit->prepayment_id = $select_prepayment;
            $prepayment_produit->add();
        }
    }

    /**
     * Méthode appelée lors du paiement d'unce commande
     *
     * @param $commande Objet de type commande
     *
     * @return none
     */
    function paiement($commande)
    {
        $paiement_valide = true;
        $port = $commande->port;
        $credit = array();

        // On vérifie si on peut valider la commande
        $venteprod = new Venteprod();
        $query = "SELECT * FROM $venteprod->table WHERE commande=" . intval($commande->id);
        $resul = $venteprod->query_liste($query);

        // ##### CETTE VERIFICATION EST REDONDANTE ET ON POURRAIT EVENTUELLEMENT LA SUPPRIMER #### //
        foreach($resul as $prod)
        {
            $produit = new Produit($prod->ref);
            $prepayment_produit = new Prepayment_produit();
            if($prepayment_produit->charger_produit($produit->id))
            {
                // On vérifie si le client à suffisamment de crédit
                $prepayment_commande = new Prepayment_commande();

                // On vérifie si le calcul de crédit est déja fait pour ce type de prepayment, sinon on le calcul
                if(!array_key_exists($prepayment_produit->prepayment_id, $credit)){
                    $total = $prepayment_commande->credit_total($commande->client, $prepayment_produit->prepayment_id);
                    $credit[$prepayment_produit->prepayment_id] = ($total !== null) ? $total : 0;
                }

                // vérifier si le crédit est suffisant
                $this->charger_id($prepayment_produit->prepayment_id);
                $type_quantite = defined('PREPAYMENT_TYPE_QUANTITE') ? PREPAYMENT_TYPE_QUANTITE : 1;
                switch ($this->type) {
                    case $type_quantite:
                        $debit = $prod->quantite;
                        break;
                    default:
                        $debit = ($prod->prixu * $prod->quantite) + $port; // On déduit aussi les frais de port (1 seul fois)
                        $port = 0;
                }

                // On n'a pas suffisamment de crédit
                $credit[$prepayment_produit->prepayment_id] -= $debit;
                $prepayment_minimum = defined('PREPAYMENT_MINIMUM') ? PREPAYMENT_MINIMUM : 0;
                if ($credit[$prepayment_produit->prepayment_id] < $prepayment_minimum) {
                    $paiement_valide = false;
                    break 1;
                }
            } else {
                // On arrête et on ne valide pas la commande car elle contient des produits n'acceptant pas le mode de paiement prépayé
                $paiement_valide = false;
                break 1;
            }
        }

        // Tout est ok
        if($paiement_valide)
        {
            $port = $commande->port;

            // On met à jour le statut de la commande
            $commande = new Commande($commande->id);
            $commande->statut = 2;
            $commande->maj();

            // On sauvegarde la commande prépayé de type débit
            foreach($resul as $prod)
            {
                $produit = new Produit($prod->ref);
                $prepayment_produit = new Prepayment_produit();
                if($prepayment_produit->charger_produit($produit->id))
                {
                    $prepayment_commande = new Prepayment_commande();
                    $prepayment_commande->prepayment_id = $prepayment_produit->prepayment_id;
                    $prepayment_commande->commande_id = $commande->id;
                    $prepayment_commande->client_id = $commande->client;
                    $prepayment_commande->type = defined('PREPAYMENT_DEBIT') ? PREPAYMENT_DEBIT : 2;;
                    // On vérifie le type
                    $this->charger_id($prepayment_produit->prepayment_id);
                    $type_quantite = defined('PREPAYMENT_TYPE_QUANTITE') ? PREPAYMENT_TYPE_QUANTITE : 1;
                    switch ($this->type) {
                        case $type_quantite:
                            $debit = $prod->quantite;
                            break;
                        default:
                            $debit = ($prod->prixu * $prod->quantite) + $port; // On déduit aussi les frais de port (1 seul fois)
                            $port = 0;
                    }
                    $prepayment_commande->valeur = $debit;
                    $prepayment_commande->add();
                }
            }

            ActionsModules::instance()->appel_module("confirmation", $commande);
            $fond_succes = defined('PREPAYMENT_URL_SUCCES') ? PREPAYMENT_URL_SUCCES : "merci";
            header("Location: " . urlfond($fond_succes));
            exit;

        } else {
            $fond_erreur = defined('PREPAYMENT_URL_ERREUR') ? PREPAYMENT_URL_ERREUR : "regret";
            header("Location: " . urlfond($fond_erreur));
            exit;
        }
    }

    function boucle($texte, $args)
    {

        $boucle = strtolower(lireTag($args, 'boucle'));

        switch($boucle)
        {
            case 'paiement':
                return $this->bouclePaiement($texte, $args);
                break;

            case 'transport':
                return $this->boucleTransport($texte, $args);
                break;

            default:
                return $this->boucleDefaut($texte, $args);
        }
    }

    /**
     * Boucle permettant de proposer le type de paiement prépayé au client
     * en fonction du type de produit dans le panier. On vérifie également
     * que le client a suffisamment de crédit.
     *
     * @param $texte
     * @param $args
     *
     * @return string
     */
    private function bouclePaiement($texte, $args)
    {

        // Récupération des arguments
        $id = lireTag($args, "id", "int");
        $exclusion = lireTag($args, "exclusion", "string_list");

        if($id == "")
        {

            // Tableau temporaire
            $arrExclusion = array();
            if($exclusion != "")
                $arrExclusion = explode(",", $exclusion);

            $port = $_SESSION['navig']->commande->port;
            $credit = array();

            // On vérifie si on peut utiliser le mode de paiement prépayé
            foreach($_SESSION['navig']->panier->tabarticle as &$art)
            {
                $prepayment_produit = new Prepayment_produit();
                if($prepayment_produit->charger_produit($art->produit->id))
                {
                    // On vérifie si le client à suffisamment de crédit
                    $client = $_SESSION['navig']->client;
                    $prepayment_commande = new Prepayment_commande();

                    // On vérifie si le calcul de crédit est déja fait pour ce type de prepayment, sinon on le calcul
                    if(!array_key_exists($prepayment_produit->prepayment_id, $credit)) {
                        $total = $prepayment_commande->credit_total($client->id, $prepayment_produit->prepayment_id);
                        $credit[$prepayment_produit->prepayment_id] = ($total !== null) ? $total : 0;
                    }

                    // vérifier si le crédit est suffisant
                    $this->charger_id($prepayment_produit->prepayment_id);
                    $type_quantite = defined('PREPAYMENT_TYPE_QUANTITE') ? PREPAYMENT_TYPE_QUANTITE : 1;
                    switch ($this->type) {
                        case $type_quantite:
                            $debit = $art->quantite;
                            break;
                        default:
                            $debit = $art->produit->prix - $port; // On déduit aussi les frais de port (1 seul fois)
                            $port = 0;
                    }

                    $prepayment_minimum = defined('PREPAYMENT_MINIMUM') ? PREPAYMENT_MINIMUM : 0;
                    $credit[$prepayment_produit->prepayment_id] -= $debit;
                    if ($credit[$prepayment_produit->prepayment_id] < $prepayment_minimum) {
                        $arrExclusion[] =  self::MODULE;
                        break 1;
                    }
                } else {
                    // On arrête et on exclue ce module car le panier contient des produits n'acceptant pas le mode de paiement prépayé
                    $arrExclusion[] =  self::MODULE;
                    break 1;
                }
            }

            // Substitutions
            $texte = str_replace("#ID", "", $texte);
            $texte = str_replace("#EXCLUSION", implode(",", $arrExclusion), $texte);

            return $texte;
        }

        // Substitutions
        $texte = str_replace("#ID", $id, $texte);
        $texte = str_replace("#EXCLUSION", "", $texte);;

        return $texte;
    }


    /**
     * Boucle permettant de filtrer le type de transport à proposer au client
     * en fonction du type de produit dans le panier.
     *
     * @param $texte
     * @param $args
     *
     * @return string
     */
    private function boucleTransport($texte, $args)
    {
        $livraison_zero = defined('PREPAYMENT_LIVRAISON_ZERO') ? PREPAYMENT_LIVRAISON_ZERO :  "livraison_zero";

        // Récupération des arguments
        $id = lireTag($args, "id", "int");
        $exclusion = lireTag($args, "exclusion", "string_list");

        if($id == "")
        {
            // Tableau temporaire
            $arrExclusion = array();
            if($exclusion != "")
                $arrExclusion = explode(",", $exclusion);

            $arrExclusion[] = $livraison_zero;

            // On vérifie si le panier ne contient que des recharges de crédits . Si oui, on propose uniquement $livraison_zero
            $nb_livraison_zero = 0;
            foreach($_SESSION['navig']->panier->tabarticle as &$art) {
                if($art->livraison_zero)
                {
                    $nb_livraison_zero++;

                } else {
                    $caracval = new Caracval();
                    $query = "SELECT count(*) AS nb FROM $caracval->table WHERE produit = " . intval($art->produit->id);
                    $res = $caracval->query($query);
                    $nb = $res ? $this->get_result($res,0,"nb") : 0;

                    if($nb > 0){
                        $nb_livraison_zero++;
                        $art->livraison_zero = true;
                    }
                }
            }

            // On compare le nombre d'article du panier et le nombre de produits de type recharge trouvé
            if($_SESSION['navig']->panier->nbart == $nb_livraison_zero)
            {
                // On recherche l'id du module $livraison_zero
                $mod = new Modules();
                if($mod->charger($livraison_zero))
                    $id = $mod->id;

            } else {
                // Substitutions
                $texte = str_replace("#ID", "", $texte);
                $texte = str_replace("#EXCLUSION", implode(",", $arrExclusion), $texte);

                return $texte;
            }
        }

        // Substitutions
        $texte = str_replace("#ID", $id, $texte);
        $texte = str_replace("#EXCLUSION", "", $texte);

        return $texte;
    }

    /**
     * Boucle permettant d'afficher le nombre de crédit d'un client.
     *
     * @param $texte
     * @param $args
     *
     * @return string
     */
    private function boucleDefaut($texte, $args)
    {
        // Récupération des arguments
        $id = lireTag($args, "id", "int");
        $client_id = lireTag($args, "client", "int");
        $prepayment_id = lireTag($args, "prepayment", "int");
        $caracteristique_id = lireTag($args, "caracteristique", "int");
        $module = lireTag($args, "module", "string");

        // Inclusion dans la boucle Paiement
        if($module && $module != self::MODULE)
            return;

        // Préparation de la requète
        $where = "";
        $return = "";

        if (intval($id) > 0) $where .= " AND pre_cmd.id=" . intval($id);
        if (intval($client_id) > 0) $where .= " AND pre_cmd.client_id=" . intval($client_id);
        if (intval($prepayment_id) > 0) $where .= " AND pre_cmd.prepayment_id=" . intval($prepayment_id);
        if (intval($caracteristique_id) > 0) $where .= " AND pre_cmd.prepayment_id IN (SELECT prepayment_id FROM ".Prepayment::TABLE." WHERE caracteristique_id=" . intval($caracteristique_id).")";

        // Requète
        $prepayment_commande = new Prepayment_commande();
        $query = "SELECT DISTINCT(pre_cmd.client_id), pre_cmd.prepayment_id FROM $prepayment_commande->table AS pre_cmd WHERE 1=1 $where";
        $res_prepayment_commande = $prepayment_commande->query_liste($query);

        // On loupe car un client pourrait avoir plusieurs type de prépaiement
        foreach($res_prepayment_commande as $pre_cmd) {

            // Calcul du total de crédit
            $total = $prepayment_commande->credit_total($pre_cmd->client_id, $pre_cmd->prepayment_id);

            if($this->charger_id($pre_cmd->prepayment_id) && $total !== null)
            {
                $caracteristiquedesc = new Caracteristiquedesc($this->caracteristique_id);

                $valeur = $total;

                // Dans le cas d'un prépaiement de type prix
                $type_prix = defined('PREPAYMENT_TYPE_PRIX') ? PREPAYMENT_TYPE_PRIX : 0;
                if($this->type == $type_prix){
                    $devise = new Devise();
                    $devise->charger(1);
                    $valeur = formatter_somme($total) . " " . $devise->symbole;
                }

                // Cache
                $tmp = $texte;

                // Substitutions
                $tmp = str_replace("#LABEL", $caracteristiquedesc->titre, $tmp);
                $tmp = str_replace("#CREDIT", "$valeur", $tmp);

                $return .= $tmp;
            }
        }

        return $return;
    }

    /**
     * Méthode appelée après confirmation du paiement
     *
     * @param $commande Objet de type commande
     *
     * @return none
     */
    function mail($commande)
    {
        // Ne rien faire
    }

     /**
     * Méthode appelée quand on désactive le plugin
     *
     * @return none
     */
    function destroy()
    {
        // Rien
    }
}
?>
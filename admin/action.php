<?php
if(isset($action))
{

    // On ajoute un nouveau Prepayment
    if ($action == "configuration_add") {

        $caracteristique_id = trim(lireParam("select_caracteristique", "int"));
        $type = trim(lireParam("select_type", "int"));
        if (intval($caracteristique_id) > 0)
        {
            $prepayment = new Prepayment();
            $prepayment->caracteristique_id = $caracteristique_id;
            $prepayment->type = $type;
            $prepayment->add();
        }

    }
    // On supprime un Prepayment
    elseif ($action == "configuration_remove"){

        $prepayment_id = trim(lireParam("id", "int"));

        $prepayment = new Prepayment();
        if($prepayment->charger_id($prepayment_id)){
            $prepayment->delete();
        }

    }
    // On ajoute des crédits pour tous les utilisateurs
    elseif ($action == "credit_add"){

        $credit_value = trim(lireParam("credit_value", "int"));
        $client_id = trim(lireParam("client_id", "string"));
        
        if (intval($credit_value) > 0)
        {
            if($client_id=="all"){
                $client = new Client();
                $query = "select id from $client->table";
                $resul = $client->query($query);
                while($resul && $row = $client->fetch_object($resul)){
                    $prepayment_commande = new Prepayment_commande();
                    $prepayment_commande->prepayment_id = 1;
                    $prepayment_commande->commande_id = 0;
                    $prepayment_commande->client_id = $row->id;
                    $prepayment_commande->type = defined('PREPAYMENT_CREDIT') ? PREPAYMENT_CREDIT : 1;
                    $prepayment_commande->valeur = $credit_value;
                    $prepayment_commande->add();
                }
            }
            else{
                $prepayment_commande = new Prepayment_commande();
                $prepayment_commande->prepayment_id = 1;
                $prepayment_commande->commande_id = 0;
                $prepayment_commande->client_id = $client_id;
                $prepayment_commande->type = defined('PREPAYMENT_CREDIT') ? PREPAYMENT_CREDIT : 1;
                $prepayment_commande->valeur = $credit_value;
                $prepayment_commande->add();
            }
            
            $_SESSION['return']= 'ok';
        }
    }

    // Redirection
    redirige($_REQUEST['redirect'] ? $_REQUEST['redirect'] : "module.php?nom=prepayment");
}
?>
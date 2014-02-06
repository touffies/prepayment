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

    // Redirection
	redirige($_REQUEST['redirect'] ? $_REQUEST['redirect'] : "module.php?nom=prepayment");
}
?>
<?php
    $prepayment_commande = new Prepayment_commande();
    if($page=="") $page=1;
    
    $query = "select count(*) from $prepayment_commande->table";
  	$resul = mysql_query($query, $prepayment_commande->link);
  	$num = mysql_result($resul,0);
  	$nbpage = 20;
  	$totnbpage = ceil($num/20);

  	$debut = ($page-1) * 20;

  	if($page>1) $pageprec=$page-1;
  	else $pageprec=$page;

  	if($page<$totnbpage) $pagesuiv=$page+1;
  	else $pagesuiv=$page;
?>
<p>
	<a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" />
	<a href="module_liste.php" class="lien04"><?php echo trad('Modules', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" />
	<a href="module.php?nom=prepayment" class="lien04"><?php echo trad('Prépaiement', 'prepayment'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" />
	<span style="color: #4E6172; font-size: 12px;margin-left:3px;"><?php echo trad('Historique', 'prepayment'); ?></span>
</p>

<div class="entete_liste_config">
    <div class="titre"><?php echo trad('Historique' , 'prepayment'); ?></div>
</div>

<ul class="Nav_bloc_description">
    <li style="width:35px;"><?php echo trad('ID', 'prepayment'); ?></li>
    <li style="width:155px;"><?php echo trad('Date', 'prepayment'); ?></li>
    <li style="width:195px;"><?php echo trad('Description', 'prepayment'); ?></li>
    <li style="width:70px;"><?php echo trad('Montant', 'prepayment'); ?></li>
    <li style="width:220px;"><?php echo trad('Nom du client', 'prepayment'); ?></li>
    <li style="width:150px;"><?php echo trad('Email', 'prepayment'); ?></li>
</ul>

<div class="bordure_bottom">
    <?php
    $pre_commandes = $prepayment_commande->query_liste("SELECT * FROM $prepayment_commande->table ORDER BY id DESC limit $debut,20");

    if(count($pre_commandes) > 0)
    {
        $fond = 'claire';
        foreach($pre_commandes as $pre_commande) {
            $client = new Client($pre_commande->client_id);
            $commande = new Commande($pre_commande->commande_id);

            $statut_exclusion = defined('PREPAYMENT_STATUT_EXCLUSION') ? PREPAYMENT_STATUT_EXCLUSION : '1,5';
            if(!in_array($commande->statut, explode(',', $statut_exclusion)))
            {
                $prepayment = new Prepayment($pre_commande->prepayment_id);
                $caracteristiquedesc = new Caracteristiquedesc($prepayment->caracteristique_id);
            ?>
            <ul class="ligne_<?php echo $fond; ?>_rub">
                <li style="width:30px;"><?php echo $pre_commande->id; ?></li>
                <li style="width:150px;"><?php 
                    if(is_null($pre_commande->date)){
                        echo strftime("%d/%m/%Y %H:%M:%S", strtotime($commande->date));
                    }
                    else{
                        echo strftime("%d/%m/%Y %H:%M:%S", strtotime($pre_commande->date));
                    }
                ?></li>
                <li style="width:190px;"><?php echo $caracteristiquedesc->titre; ?></li>
                <li style="width:60px;"><?php
                $prepayment = new Prepayment($pre_commande->prepayment_id);
                $caracteristiquedesc = new Caracteristiquedesc($prepayment->caracteristique_id);
                $valeur = $pre_commande->valeur;
                // Dans le cas d'un prépaiement de type prix
                $type_prix = defined('PREPAYMENT_TYPE_PRIX') ? PREPAYMENT_TYPE_PRIX : 0;
                if($prepayment->type == $type_prix){
                    $devise = new Devise();
                    $devise->charger($commande->devise);

                    $valeur = formatter_somme($pre_commande->valeur) . " " . $devise->symbole;
                }

                $type_debit = defined('PREPAYMENT_DEBIT') ? PREPAYMENT_DEBIT : 2;
                if($pre_commande->type == $type_debit) {
                    echo("<span style=\"background-color:red;color:#fff; padding:2px 5px;\">-".$valeur."</span");
                } else {
                    echo("<span style=\"background-color:green;color:#fff; padding:2px 6px;\">".$valeur."</span");
                } ?>
                </li>
                <li style="width:210px;"><?php echo ucfirst(strtolower($client->prenom)) . " " .mb_strtoupper($client->nom, 'UTF-8'); ?></li>
                <li style="width:150px;"><?php echo $client->email; ?></li>
            </ul>
            <?php
            $fond = ($fond == 'claire') ?  'fonce' : 'claire';
            }
        }
    } else {
    // Pas encore d'historique
    echo "<ul class=\"ligne_claire_rub\"><li>". trad('Il n\'y a pas encore d\'historique de prépaiement.', 'prepayment')."</li></ul>";
    }
    ?>
</div>

<br clear="both"/>
<p id="pages">
<?php if($page>1){ ?>
<a href="<?php echo($_SERVER['PHP_SELF']); ?>?nom=prepayment&action_prepayment=historique&page=<?php echo($pageprec); ?>">Page précédente</a> |
<?php } ?>
<?php if($totnbpage > $nbpage){?>
	<?php if($page>1) {?><a href="<?php echo($_SERVER['PHP_SELF']); ?>?nom=prepayment&action_prepayment=historique&page=1">...</a> | <?php } ?>
	<?php if($page+$nbpage-1 > $totnbpage){ $max = $totnbpage; $min = $totnbpage-$nbpage;} else{$min = $page-1; $max=$page+$nbpage-1; }?>
<?php for($i=$min; $i<$max; $i++){ ?>
	 <?php if($page != $i+1){ ?>
	  		 <a href="<?php echo($_SERVER['PHP_SELF']); ?>?nom=prepayment&action_prepayment=historique&page=<?php echo($i+1); ?>"><?php echo($i+1); ?></a> |
	 <?php } else {?>
		  <span class="selected"><?php echo($i+1); ?></span>
		|
		  <?php } ?>
<?php } ?>
	<?php if($page < $totnbpage){?><a href="<?php echo($_SERVER['PHP_SELF']); ?>?nom=prepayment&action_prepayment=historique&page=<?php echo $totnbpage; ?>">...</a> | <?php } ?>
<?php }
else{
	for($i=0; $i<$totnbpage; $i++){ ?>
    	 <?php if($page != $i+1){ ?>
  	  		 <a href="<?php echo($_SERVER['PHP_SELF']); ?>?nom=prepayment&action_prepayment=historique&page=<?php echo($i+1); ?>"><?php echo($i+1); ?></a> |
    	 <?php } else {?>
    		 <span class="selected"><?php echo($i+1); ?></span>
    		 |
   		  <?php } ?>
     <?php } ?>
<?php } ?>


<?php if($page < $totnbpage){ ?>
<a href="<?php echo($_SERVER['PHP_SELF']); ?>?nom=prepayment&action_prepayment=historique&page=<?php echo($pagesuiv); ?>">Page suivante</a></p>
<?php } ?>

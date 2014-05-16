<?php
    $prepayment_commande = new Prepayment_commande();
    if($page=="") $page=1;
    
    $query = "select count(DISTINCT client_id) from $prepayment_commande->table";
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
	<span style="color: #4E6172; font-size: 12px;margin-left:3px;"><?php echo trad('Solde', 'prepayment'); ?></span>
</p>

<div class="entete_liste_config">
    <div class="titre"><?php echo trad('Solde des clients' , 'prepayment'); ?></div>
</div>

<ul class="Nav_bloc_description">
    <li style="width:220px;"><?php echo trad('Nom du client', 'prepayment'); ?></li>
    <li style="width:220px;"><?php echo trad('Email du client', 'prepayment'); ?></li>
    <li style="width:500px;"><?php echo trad('Description', 'prepayment'); ?></li>
</ul>

<div class="bordure_bottom">
    <?php
    $result = $prepayment_commande->query_liste("SELECT DISTINCT(client_id), prepayment_id FROM $prepayment_commande->table limit $debut,20");

    if(count($result) > 0)
    {
        $fond = 'claire';
        foreach($result as $row) {
            $client = new Client($row->client_id);

            $total = $prepayment_commande->credit_total($client->id, $row->prepayment_id);
            $prepayment = new Prepayment();
            if($prepayment->charger_id($row->prepayment_id) && $total !== null)
            {
                $caracteristiquedesc = new Caracteristiquedesc($prepayment->caracteristique_id);
                ?>
                <ul class="ligne_<?php echo $fond; ?>_rub">

                    <li style="width:215px;"><?php echo ucfirst(strtolower($client->prenom)) . " " . mb_strtoupper($client->nom, 'UTF-8') ; ?></li>
                    <li style="width:215px;"><?php echo $client->email; ?></li>
                    <li style="width:400px;"><?php
                        $valeur = $total;
                        // Dans le cas d'un prépaiement de type prix
                        $type_prix = defined('PREPAYMENT_TYPE_PRIX') ? PREPAYMENT_TYPE_PRIX : 0;
                        if($prepayment->type == $type_prix){
                            $devise = new Devise();
                            $devise->charger(1);

                            $valeur = formatter_somme($total) . " " . $devise->symbole;
                        }

                        if(intval($total) > 0 ) {
                            echo($caracteristiquedesc->titre . " <span style=\"background-color:green;color:#fff; padding:2px 6px;\">".$valeur."</span");
                        } else {
                            echo($caracteristiquedesc->titre . " <span style=\"background-color:red;color:#fff; padding:2px 5px;\">".$valeur."</span");
                        } ?>
                    </li>
                </ul>
                <?php
                $fond = ($fond == 'claire') ?  'fonce' : 'claire';
            }
        }
    } else {
        // Pas encore de soldes
        echo "<ul class=\"ligne_claire_rub\"><li>". trad('Aucun client n\'utilise le prépaiement.', 'prepayment')."</li></ul>";
    }
    ?>
</div>
<br clear="both"/>
<p id="pages">
<?php if($page>1){ ?>
<a href="<?php echo($_SERVER['PHP_SELF']); ?>?nom=prepayment&action_prepayment=solde&page=<?php echo($pageprec); ?>">Page précédente</a> |
<?php } ?>
<?php if($totnbpage > $nbpage){?>
	<?php if($page>1) {?><a href="<?php echo($_SERVER['PHP_SELF']); ?>?nom=prepayment&action_prepayment=solde&page=1">...</a> | <?php } ?>
	<?php if($page+$nbpage-1 > $totnbpage){ $max = $totnbpage; $min = $totnbpage-$nbpage;} else{$min = $page-1; $max=$page+$nbpage-1; }?>
<?php for($i=$min; $i<$max; $i++){ ?>
	 <?php if($page != $i+1){ ?>
	  		 <a href="<?php echo($_SERVER['PHP_SELF']); ?>?nom=prepayment&action_prepayment=solde&page=<?php echo($i+1); ?>"><?php echo($i+1); ?></a> |
	 <?php } else {?>
		  <span class="selected"><?php echo($i+1); ?></span>
		|
		  <?php } ?>
<?php } ?>
	<?php if($page < $totnbpage){?><a href="<?php echo($_SERVER['PHP_SELF']); ?>?nom=prepayment&action_prepayment=solde&page=<?php echo $totnbpage; ?>">...</a> | <?php } ?>
<?php }
else{
	for($i=0; $i<$totnbpage; $i++){ ?>
    	 <?php if($page != $i+1){ ?>
  	  		 <a href="<?php echo($_SERVER['PHP_SELF']); ?>?nom=prepayment&action_prepayment=solde&page=<?php echo($i+1); ?>"><?php echo($i+1); ?></a> |
    	 <?php } else {?>
    		 <span class="selected"><?php echo($i+1); ?></span>
    		 |
   		  <?php } ?>
     <?php } ?>
<?php } ?>


<?php if($page < $totnbpage){ ?>
<a href="<?php echo($_SERVER['PHP_SELF']); ?>?nom=prepayment&action_prepayment=solde&page=<?php echo($pagesuiv); ?>">Page suivante</a></p>
<?php } ?>


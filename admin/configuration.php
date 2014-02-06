<p>
	<a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" />
	<a href="module_liste.php" class="lien04"><?php echo trad('Modules', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" />
	<a href="module.php?nom=prepayment" class="lien04"><?php echo trad('Prépaiement', 'prepayment'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" />
	<span style="color: #4E6172; font-size: 12px;margin-left:3px;"><?php echo trad('Configuration', 'prepayment'); ?></span>
</p>

<!-- bloc déclinaisons / colonne gauche -->
<div id="bloc_description">
	<div class="entete_liste_config">
		<div class="titre"><?php echo trad('Caractéristique utilisée pour le prépaiement', 'prepayment'); ?></div>
	</div>

	<div class="bordure_bottom">
		<form id="frm_add" action="" method="post">
		    <input type="hidden" name="action" value="configuration_add" />
			<input type="hidden" name="redirect" value="<?php echo $_SERVER['REQUEST_URI'] ?>" />

			<?php
            /**
             * On recherche toutes les caractéritstiques du site et on vérifie si celle-ce est déjà associée
             * à un prépaiement.
             * - Si oui, on recherche la description de la caractéristique et on la propose dans
             * la liste déroulante.
             * - Si non, on ne fait rien.
             */
            ?>
            <?php $caracteristique = new Caracteristique(); ?>
            <ul class="ligne1">
                <li>
                    <select name="select_caracteristique" class="form_select">
                        <?php
                        $query = "SELECT * FROM $caracteristique->table";
                        $resul = $caracteristique->query($query);
                        while($resul && $caract = $caracteristique->fetch_object($resul))
                        {
                            $prepayment = new Prepayment();
                            if(! $prepayment->charger_caracteristique($caract->id)){
                                $caracteristiquedesc = new Caracteristiquedesc($caract->id);
                                ?>
                                <option value="<?php echo $caract->id; ?>"><?php echo $caracteristiquedesc->titre; ?></option>
                            <?php
                            }
                        }
                        ?>
                    </select>
                </li>
                <li>
                    <?php
                    /**
                     * On liste les différents type de prépaiement possible :
                     * - Prix (défaut) : on débite ou on crédite le compte en utilisant le prix.
                     * - Quantité : on débite ou on crédite le compte en utilisant la quantité (Example : achat de 10 vidéos)
                     */
                    ?>
                    <select name="select_type" class="form_select">
                        <option value="<?php echo defined('PREPAYMENT_TYPE_PRIX') ? PREPAYMENT_TYPE_PRIX : 0; ?>"><?php echo trad('Prépaiement par prix', 'prepayment'); ?></option>
                        <option value="<?php echo defined('PREPAYMENT_TYPE_QUANTITE') ? PREPAYMENT_TYPE_QUANTITE : 1; ?>"><?php echo trad('Prépaiement par quantité', 'prepayment'); ?></option>
                    </select>
                </li>
                <li><a href="#" onclick="jQuery('#frm_add').submit(); $(this).removeAttr('onclick').css( 'cursor', 'progress' ); return false;"><?php echo trad('Ajouter', 'admin'); ?></a></li>
            </ul>
         </form>

        <?php
        /**
         * On liste tous les prépaiements existant, il est possible d'en supprimer.
         *
         */
        $prepayment = new Prepayment();
        $query = "SELECT * FROM $prepayment->table";
        $resul = $prepayment->query($query);
        $prepaymentListID = array();
        $i = 0;
        while($resul && $row = $prepayment->fetch_object($resul)){
            $prepaymentListID[] = $row->id;
            $caracteristiquedesc = new Caracteristiquedesc($row->caracteristique_id);

            $fond="ligne_".($i++%2 ? "fonce":"claire")."_BlocDescription";
            $type_quantite = defined('PREPAYMENT_TYPE_QUANTITE') ? PREPAYMENT_TYPE_QUANTITE : 1;
            switch ($row->type) {
                case $type_quantite:
                    $type_label = trad('quantité', 'prepayment');
                    break;
                default:
                    $type_label = trad('prix', 'prepayment');
            }
            ?>
            <form id="frm_remove_<?php echo $row->id; ?>" action="" method="post">
                <input type="hidden" name="action" value="configuration_remove" />
                <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
                <input type="hidden" name="redirect" value="<?php echo $_SERVER['REQUEST_URI'] ?>" />
                <ul class="<?php echo $fond; ?>">
                    <li style="width:492px;"><?php echo $caracteristiquedesc->titre ." (".$type_label.")"?></li>
                    <li style="width:32px;"><a href="#" onclick="jQuery('#frm_remove_<?php echo $row->id; ?>').submit(); $(this).removeAttr('onclick').css( 'cursor', 'progress' ); return false;"><?php echo trad('Supprimer', 'admin'); ?></a></li>
                </ul>
            </form>
        <?php
        }
        ?>
	</div>
</div>

<?php
// On recherche si des produits actifs sont associés à avec des prépaiements qui n'existent plus
$prod = new Produit();
$where = count($prepaymentListID) > 0 ? "pre.prepayment_id NOT IN (".implode(',', $prepaymentListID).") AND" : "" ;
$query = "SELECT prod.id, prod.ref FROM $prod->table AS prod INNER JOIN ".Prepayment_produit::TABLE." AS pre ON prod.id = pre.produit_id WHERE $where prod.ligne=1";
$produits = $prod->query_liste($query);

if(count($produits) > 0)
{
    ?>

    <div id="bloc_colonne_droite">

        <div class="entete_config">
            <div class="titre"><?php echo trad('Produit à vérifier.', 'prepayment'); ?></div>
        </div>

        <ul class="Nav_bloc_description">
            <li style="width:280px;"><?php echo trad('Nom', 'prepayment'); ?></li>
        </ul>

        <div class="bordure_bottom">
            <?php
            $fond = 'claire';
            foreach($produits as $produit) {
                $proddesc = new Produitdesc($produit->id);
                ?>
                <ul class="ligne_<?php echo $fond; ?>_BlocDescription">
                    <li style="width:280px;"><a href="produit_modifier.php?ref=<?php echo $produit->ref; ?>" class="txt_vert_11"><?php echo($proddesc->titre); ?></a></li>
                </ul>
                <?php
                $fond = ($fond == 'claire') ?  'fonce' : 'claire';
            }
            ?>
        </div>
    </div>
<?php
}
?>

<br clear="both" />

<p><small>
	<?php echo trad('- Pour fonctionner, vous devez associer une caractéristique à un prépaiement.<br />
- La valeur saisie sur la fiche produit, pour cette caractéristique, sera utilisée pour créditer ou débiter le compte du client.<br />
- Vous pouvez utiliser le prix ou la quantité, ce qui permet d\'avoir un système de prépaiement par crédit.', 'prepayment'); ?>
</small></p>
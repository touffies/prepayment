<p>
	<a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" />
	<a href="module_liste.php" class="lien04"><?php echo trad('Modules', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" />
	<a href="module.php?nom=prepayment" class="lien04"><?php echo trad('Prépaiement', 'prepayment'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" />
	<span style="color: #4E6172; font-size: 12px;margin-left:3px;"><?php echo trad('Ajout de Credit', 'prepayment'); ?></span>
</p>

<div id="bloc_description">
	<div class="entete_liste_config">
		<div class="titre"><?php echo trad('Ajout de Credit' , 'prepayment'); ?></div>
		<div class="fonction_valider">
			<a onclick="$('#frm_configuration').submit(); $(this).removeAttr('onclick').css( 'cursor', 'progress' ); return false;" href="#"><?php echo trad('VALIDER', 'admin'); ?></a>
		</div>
	</div>
	
	<div class="bordure_bottom">
		<form id="frm_configuration" action="" method="post">
		    <input type="hidden" name="action" value="credit_add" />
			<input type="hidden" name="redirect" value="<?php echo $_SERVER['REQUEST_URI'] ?>" />
			<ul class="ligne_claire_BlocDescription">
                <li style="width:195px;"><label for="credit_value">Nombre de crédit à ajouter</label></li>
                <li style="width:360px; border-left:1px solid #96A8B5;">
                    <input style="width: 355px;" name="credit_value" id="credit_value" type="text" class="form" value="">
                </li>
            </ul>
        </form>
	</div>
	<?php
		if($_SESSION['return']=="ok"):
		unset($_SESSION['return']);
	?>
		<br clear="both" />
		<p style="color:green;">Le crédit a bien été ajouté à tous les Clients</p>
	<?php
		endif;
	?>
</div>
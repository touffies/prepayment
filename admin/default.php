<p>
	<a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" />
	<a href="module_liste.php" class="lien04"><?php echo trad('Modules', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" />
	<span style="color: #4E6172; font-size: 12px;margin-left:3px;"><?php echo trad('Prépaiement', 'prepayment'); ?></span>
</p>

<div id="bloc_informations">
	<ul style="width: 50%">
		<li class="entete_configuration" style="width: 445px"><?php echo trad('Prépaiement', 'prepayment'); ?></li>
 		<li class="claire" style="width:390px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo trad('Configuration', 'prepayment'); ?></li>
		<li class="claire" style="width:50px;"><a href="module.php?nom=prepayment&action_prepayment=configuration"><?php echo trad('éditer'); ?> </a></li>
        <li class="fonce" style="width:390px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo trad('Historique', 'prepayment'); ?></li>
        <li class="fonce" style="width:50px;"><a href="module.php?nom=prepayment&action_prepayment=historique"><?php echo trad('éditer'); ?> </a></li>
        <li class="claire" style="width:390px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo trad('Solde', 'prepayment'); ?></li>
        <li class="claire" style="width:50px;"><a href="module.php?nom=prepayment&action_prepayment=solde"><?php echo trad('éditer'); ?> </a></li>
	</ul>
</div>

<p>
    <a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" />
    <a href="module_liste.php" class="lien04"><?php echo trad('Modules', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" />
    <a href="module.php?nom=prepayment" class="lien04"><?php echo trad('Prépaiement', 'prepayment'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" />
    <span style="color: #4E6172; font-size: 12px;margin-left:3px;"><?php echo trad('Gestion du Credit', 'prepayment'); ?></span>
</p>

<div id="bloc_description">
    <div class="entete_liste_config">
        <div class="titre"><?php echo trad('Gestion du Credit' , 'prepayment'); ?></div>
        <div class="fonction_valider">
            <a onclick="$('#frm_configuration').submit(); $(this).removeAttr('onclick').css( 'cursor', 'progress' ); return false;" href="#"><?php echo trad('VALIDER', 'admin'); ?></a>
        </div>
    </div>
    
    <div class="bordure_bottom">
        <form id="frm_configuration" action="" method="post">
            <input type="hidden" name="action" value="credit_add" />
            <input type="hidden" name="redirect" value="<?php echo $_SERVER['REQUEST_URI'] ?>" />
            <ul class="ligne_claire_BlocDescription">
                <li style="width:195px;"><label for="credit_value"><?php echo trad('Nombre de crédit à ajouter', 'prepayment'); ?></label></li>
                <li style="width:360px; border-left:1px solid #96A8B5;">
                    <input style="width: 355px;" name="credit_value" id="credit_value" type="text" class="form" value="">
                </li>
                <li style="width:195px;"><label for="client_id"><?php echo trad('Sélection du client', 'prepayment'); ?></label></li>
                <li style="width:360px; border-left:1px solid #96A8B5;">
                    <select style="width: 355px;" name="client_id" id="client_id" type="text" class="form">
                        <option value="all" selected="selected"><?php echo trad('Tous', 'prepayment'); ?></option>
                        <?php
                            $client = new Client();
                            $query = "select id,nom,prenom from $client->table ORDER by nom ASC";
                            $resul = $client->query($query);
                            while($resul && $row = $client->fetch_object($resul)){
                                echo '<option value='.$row->id.'>'.$row->nom.' '.$row->prenom.'</option>';
                            }
                        ?>
                    </select>
                </li>
            </ul>
        </form>
    </div>
    <?php
        if($_SESSION['return']=="ok"):
        unset($_SESSION['return']);
    ?>
        <br clear="both" />
        <p style="color:green;"><?php echo trad('Votre ajout de crédit(s) a été pris en compte', 'prepayment'); ?></p>
    <?php
        endif;
    ?>
</div>
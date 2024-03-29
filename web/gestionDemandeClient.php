<?php
require_once '../lib/bib_connect.php';

require_once '../lib/bib_sql.php';
require_once '../bibappli/lib_metier.php';
require_once '../lib_page/header.php';
require_once '../class/classUtilisateur.php';
require_once '../class/classClientDemande.php';

//Récup info de SESSION : identification, contrôle d'accès, expérience utilisateur personnalisé, sesssion persistante évitant de se reconnecter
if (isset($_SESSION['clUser']))
    $clUser = unserialize($_SESSION['clUser']);
else
    $clUser = null;

/**
 * Cette variable a 4 possibilités de statuts
 * A ajouter
 * M modifier
 * S supprimer
 * C consulter
 * 
 * @var string 
 */
$actionGlobal = '';

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($clUser) {

    }
}

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    // pour des raisons de sécurité, une page peut être appelé soit par le site, soit nativement par un pirate
    // sauf si l'on recontrole sur le serveur qu'il est bien authentifié et ADMIn alors OK sinon dommage pour le hackeur
    if ($clUser) {
        $fct = POST::get("fct");
        if ($fct !=null && $fct="changeStatut"){
            $idxContactClient  = POST::get('idxContactClient');
            $statut = POST::get("statut");
            
            ClientDemande::modifierStatus($pdo,$idxContactClient,$statut);
            exit;
        }
    }
    else
        $message = "Accès à la page non autorisé";
}

if ($clUser == null )
{
    header("Location:index.php");
    exit;
}
?>
<body>
	<div>
<?php if (isset($message)) { ?>
    <div>
        <?= $message ?>
    </div>
<?php } ?>

<?php
// Affichage de la liste des demandes clients donc je fais une instance de la class ClientDemande
$clientDemande = new ClientDemande();

$allDemande = $clientDemande->getListe($pdo);

if ($allDemande) {
    ?>
	<div class="tbl">
			<div>
				<div>Liste des demandes d'informations</div>
			</div>
			<table>
				<tr>
					<td>Nom</td>
					<td>Prenom</td>
                    <td>Vehicule</td>
                    <td>Statut</td>
				</tr>
            <?php 
                $listeStatut = ClientDemande::getListeStatut();            
                foreach ($allDemande as $raw) { ?>
                <tr>
					<td><?=$raw['nom']?></td>
					<td><?=$raw['prenom']?></td>
                    <td>
                        <a href="fiche_voiture.php?code_vehicule=<?=$raw['idx_vehicule']?>">
                            <?=$raw['description']?>
                        </a>
                    </td>
					<td>
						<select name="statut" onchange="ClassWorkFlow.changeStatutDemande(this,<?=$raw['idx_contact_client']?>)">
						<?php
                        $statutOrigine = $raw['status'];
                        
                        $indexStatutOrigine =  array_search($statutOrigine, array_keys($listeStatut));                     

						foreach ($listeStatut as $key => $value) {
                            $indexStatutNouveau =  array_search($key, array_keys($listeStatut)); 
                            if ($indexStatutNouveau >= $indexStatutOrigine){
						    echo "<option value='$key'".($raw['status']==$key ? " selected" : "").">".htmlentities($value);
						    echo "</option>";
                            }
						}
						?>
						</select>
					</td>
				</tr>
            <?php
    }
    ?>
        </table>
		</div>
<?php } ?>
<!-- Permet de savoir si un utilisateur s'est authentifié et que ce dernier et admin, donc je lui donne l'autorisation de créer ... AINSI l'ensemble du html FORM ci-dessous n'est pas envoyé au poste client-->
<?php
if ($clUser) {
    if ($clUser->isAdmin()) {
        if ($actionGlobal == 'C'){
            
        }
        else{
        ?>
		<form action="#" method="POST" id="formulaire">
			<div class="clFormSaisie">
                <!--
				<div class="button">
					<label></label>
					<input type="submit" value="Envoyer" />
				</div>
                -->
			</div>
		</form>
<?php
        }
    }
}
?>
</div>
<div>
	<a href="index.php">Retour</a>
</div>
    <?php
    require_once '../lib_page/footer.php';
    ?>
<script
		src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="js/plugins/bootstrap.bundle.min.js"></script>
    <script src="js/sandrine.js"></script>
</body>
</html>

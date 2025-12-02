<h1>infos internaute</h1>

<?php if($user): ?>
  <p>ID: <?=$user->id ?></p>
  <p>Nom : <?= $user->prenom ?></p>
  <p>Prenom: <?= $user->nom ?></p>
  <p>Pseudo: <?= $user->pseudo?></p>
  <p>mail: <?=$user->mail?></p>
  <p>permis: <?=$user->permis?></p>
  
  <h1>voyages proposés: </h1>
  <?php if($user->permis && count($user->voyages)>0):?> 
    <?php foreach($user->voyages as $v): ?>
      <p><h3>id voyage: <?=$v->id ?></h3></p>
      <p>tarif <?=$v->tarif ?>$</p>
      <p>nombre de places dispo: <?=$v->nbplacedispo?></p>
      <p>nombre de bagages: <?=$v->nbbagage?></p>
      <p>heure de depart: <?=$v->heuredepart?>h</p>
      <p>contraintes: <?=$v->contraintes?>h</p>

      <p>ville de depart: <?=$v->trajetObj->depart?></p>
      <p>ville d'arrivée: <?=$v->trajetObj->arrivee?></p>
      <p>type vehicule: <?=$v->typeVehicule->typev?></p>
      <p>marque vehicule: <?=$v->marqueVehicule->marquev?></p>
      
    <?php endforeach;?>
  <?php else:?>  
    <p>aucun trajet</p>
  <?php endif;?>







  <?php if (count($user->reservations) > 0): ?>
    <?php foreach ($user->reservations as $r): ?>
      <?php $v = $r->voyageObj; ?>

      <?php if ($v && $v->trajetObj): ?>
        <h3>Réservation n° <?= $r->id ?> (<?= $r->nbplaceresa ?> place(s))</h3>

        <p>Trajet :
          <?= $v->trajetObj->depart ?>
          → <?= $v->trajetObj->arrivee ?>
          (<?= $v->trajetObj->distance ?> km)
        </p>

        <p>Conducteur :
          <?= $v->conducteurObj->prenom ?? '' ?>
          <?= $v->conducteurObj->nom ?? '' ?>
        </p>

        <p>Véhicule :
          <?= $v->marqueVehicule->marquev ?? '' ?>
          – <?= $v->typeVehicule->typev ?? '' ?>
        </p>

        <p>Tarif : <?= $v->tarif ?> € / place</p>
        <hr>
      <?php else: ?>
        <p>Voyage ou trajet introuvable pour la réservation <?= $r->id ?></p>
      <?php endif; ?>

    <?php endforeach; ?>
  <?php else: ?>
    <p>Aucune réservation pour cet internaute.</p>
  <?php endif; ?>








  
<?php else: ?>
  <p>aucun internaute trouvé .</p>
<?php endif; ?>














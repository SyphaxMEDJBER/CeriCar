<h1>infos internaute</h1>

<?php if($user): ?>
  <p>Nom : <?= $user->prenom ?></p>
  <p>Prenom: <?= $user->nom ?></p>
  <p>Pseudo: <?= $user->pseudo?></p>


  
<?php else: ?>
  <p>aucun internaute trouvé .</p>
<?php endif; ?>














<?php
use yii\helpers\Html;
use yii\helpers\Url;
/** @var $user app\models\Internaute */
/** @var $vdep array */
/** @var $varr array */
/** @var $types array */
/** @var $marques array */
/** @var $form array */
/** @var $notif array|null */
/** @var $errors array */
/** @var $embedded bool */
?>

<!-- Formulaire proposer (page complète ou embarqué via AJAX) -->
<div class="page-shell">
    <div class="section-card">
        <div class="section-header">
            <h2>Proposer un voyage</h2>
            <p class="section-subtitle">Renseigne les details de ton trajet.</p>
        </div>

        <!-- Champs du formulaire pour le nouveau voyage -->
        <form method="post" action="<?= Url::to(['site/proposer']) ?>" class="proposer-form">
            <!-- Jeton CSRF -->
            <input type="hidden" name="_csrf" value="<?= Html::encode(Yii::$app->request->getCsrfToken()) ?>">

            <div class="row g-3">
                <!-- Depart / arrivee -->
                <div class="col-md-6">
                    <label class="form-label">Depart</label>
                    <input type="text" name="depart" list="villesDepart"
                           class="form-control"
                           value="<?= Html::encode($form['depart']) ?>"
                           placeholder="Ville de depart">
                    <datalist id="villesDepart">
                        <?php foreach ($vdep as $v): ?>
                            <option value="<?= Html::encode($v) ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Arrivee</label>
                    <input type="text" name="arrivee" list="villesArrivee"
                           class="form-control"
                           value="<?= Html::encode($form['arrivee']) ?>"
                           placeholder="Ville d'arrivee">
                    <datalist id="villesArrivee">
                        <?php foreach ($varr as $v): ?>
                            <option value="<?= Html::encode($v) ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>

                <!-- Type et marque du vehicule -->
                <div class="col-md-6">
                    <label class="form-label">Type vehicule</label>
                    <select name="idtypev" class="form-select">
                        <option value="">Choisir un type</option>
                        <?php foreach ($types as $t): ?>
                            <option value="<?= Html::encode($t->id) ?>" <?= ((int)$form['idtypev'] === (int)$t->id) ? 'selected' : '' ?>>
                                <?= Html::encode($t->typev ?? $t->id) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Marque vehicule</label>
                    <select name="idmarquev" class="form-select">
                        <option value="">Choisir une marque</option>
                        <?php foreach ($marques as $m): ?>
                            <option value="<?= Html::encode($m->id) ?>" <?= ((int)$form['idmarquev'] === (int)$m->id) ? 'selected' : '' ?>>
                                <?= Html::encode($m->marquev ?? $m->id) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Informations de prix et capacite -->
                <div class="col-md-4">
                    <label class="form-label">Tarif (EUR / km)</label>
                    <input type="number" name="tarif" class="form-control" step="0.01" min="0"
                           value="<?= Html::encode($form['tarif']) ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Places dispo</label>
                    <input type="number" name="nbplacedispo" class="form-control" min="1"
                           value="<?= Html::encode($form['nbplacedispo']) ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Bagages</label>
                    <input type="number" name="nbbagage" class="form-control" min="0"
                           value="<?= Html::encode($form['nbbagage']) ?>">
                </div>

                <!-- Heure de depart -->
                <div class="col-md-4">
                    <label class="form-label">Heure depart</label>
                    <input type="number" name="heuredepart" class="form-control" min="0" max="23"
                           value="<?= Html::encode($form['heuredepart']) ?>"
                           placeholder="Ex: 14">
                </div>

                <!-- Contraintes du conducteur -->
                <div class="col-12">
                    <label class="form-label">Contraintes</label>
                    <textarea name="contraintes" class="form-control" rows="3"
                              placeholder="Ex: pas de tabac, animaux..."><?= Html::encode($form['contraintes']) ?></textarea>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <!-- Actions du formulaire -->
                <button type="submit" class="btn btn-primary">Proposer</button>
                <?php if (empty($embedded)): ?>
                    <!-- Retour au profil (page complète) -->
                    <a href="<?= Url::to(['site/profil']) ?>" class="btn btn-outline-light">Retour profil</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<?php if (!empty($notif) || !empty($errors)): ?>
<!-- Affiche les notifications côté client -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    var notif = document.getElementById("notif");
    if (!notif) {
        return;
    }

    notif.classList.remove("d-none", "alert-success", "alert-warning", "alert-danger");
    notif.classList.add("alert-<?= Html::encode($notif['type'] ?? 'danger') ?>");
    <?php if (!empty($errors)): ?>
    var errors = <?= json_encode(array_values($errors)) ?>;
    var html = "<strong>Erreurs:</strong><ul class=\"mb-0\">";
    errors.forEach(function (err) {
        var li = document.createElement("li");
        li.textContent = err;
        html += li.outerHTML;
    });
    html += "</ul>";
    notif.innerHTML = html;
    <?php else: ?>
    notif.textContent = <?= json_encode($notif['message'] ?? '') ?>;
    <?php endif; ?>
});
</script>
<?php endif; ?>

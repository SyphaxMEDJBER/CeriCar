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
?>

<div class="page-shell">
    <div class="section-card">
        <div class="section-header">
            <h2>Proposer un voyage</h2>
            <p class="section-subtitle">Renseigne les details de ton trajet.</p>
        </div>

        <?php if (!empty($notif)): ?>
            <div class="alert alert-<?= Html::encode($notif['type']) ?> proposer-alert">
                <?= Html::encode($notif['message']) ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger proposer-alert">
                <strong>Erreurs:</strong>
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= Html::encode($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= Url::to(['site/proposer']) ?>" class="proposer-form">
            <input type="hidden" name="_csrf" value="<?= Html::encode(Yii::$app->request->getCsrfToken()) ?>">

            <div class="row g-3">
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

                <div class="col-md-4">
                    <label class="form-label">Heure depart</label>
                    <input type="number" name="heuredepart" class="form-control" min="0" max="23"
                           value="<?= Html::encode($form['heuredepart']) ?>"
                           placeholder="Ex: 14">
                </div>

                <div class="col-12">
                    <label class="form-label">Contraintes</label>
                    <textarea name="contraintes" class="form-control" rows="3"
                              placeholder="Ex: pas de tabac, animaux..."><?= Html::encode($form['contraintes']) ?></textarea>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Proposer</button>
                <a href="<?= Url::to(['site/profil']) ?>" class="btn btn-outline-light">Retour profil</a>
            </div>
        </form>
    </div>
</div>

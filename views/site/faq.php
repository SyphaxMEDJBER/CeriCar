<?php
use yii\helpers\Url;

$this->title = 'FAQ';
?>

<style>
    .faq-hero {
        display: grid;
        gap: 12px;
        margin-bottom: 18px;
    }
    .faq-hero h2 {
        margin: 0;
        letter-spacing: 0.5px;
    }
    .faq-hero p {
        margin: 0;
        color: rgba(255, 255, 255, 0.7);
    }
    .faq-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 16px;
    }
    .faq-card {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 16px;
        padding: 18px;
        display: grid;
        gap: 10px;
        min-height: 160px;
    }
    .faq-chip {
        width: fit-content;
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 999px;
        background: rgba(0, 255, 200, 0.15);
        border: 1px solid rgba(0, 255, 200, 0.35);
        color: #a9fff0;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .faq-question {
        font-weight: 600;
        font-size: 1.02rem;
        margin: 0;
    }
    .faq-answer {
        margin: 0;
        color: rgba(255, 255, 255, 0.78);
        line-height: 1.5;
    }
    .faq-cta {
        margin-top: 22px;
        display: flex;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
    }
    .faq-cta small {
        color: rgba(255, 255, 255, 0.6);
    }
</style>

<div class="page-shell">
    <div class="section-card">
        <div class="faq-hero">
            <h2>FAQ</h2>
            <p>Des reponses simples, rapides et claires pour vos trajets.</p>
        </div>

        <div class="faq-grid">
            <div class="faq-card">
                <div class="faq-chip">Compte</div>
                <p class="faq-question">Comment verifier mon compte conducteur ?</p>
                <p class="faq-answer">
                    Ajoutez votre permis, une piece d'identite et un numero valide. La verification
                    apparait en general sous 24 h.
                </p>
            </div>
            <div class="faq-card">
                <div class="faq-chip">Trajet</div>
                <p class="faq-question">Puis-je modifier un voyage publie ?</p>
                <p class="faq-answer">
                    Oui, tant qu'aucune reservation n'est confirmee. Apres confirmation, prevenez
                    les passagers avant tout changement.
                </p>
            </div>
            <div class="faq-card">
                <div class="faq-chip">Reservation</div>
                <p class="faq-question">Comment fonctionne la reservation ?</p>
                <p class="faq-answer">
                    Le passager envoie une demande, vous acceptez, puis la place est bloquee.
                    Toutes les infos sont visibles dans votre espace.
                </p>
            </div>
            <div class="faq-card">
                <div class="faq-chip">Paiement</div>
                <p class="faq-question">Quels moyens de paiement sont acceptes ?</p>
                <p class="faq-answer">
                    Carte bancaire et portefeuille en ligne. Le paiement est securise et transfere
                    apres confirmation du trajet.
                </p>
            </div>
            <div class="faq-card">
                <div class="faq-chip">Annulation</div>
                <p class="faq-question">Quelle est la politique d'annulation ?</p>
                <p class="faq-answer">
                    Annulation gratuite jusqu'a 24 h avant le depart. Ensuite, des frais peuvent
                    s'appliquer selon le motif.
                </p>
            </div>
            <div class="faq-card">
                <div class="faq-chip">Avis</div>
                <p class="faq-question">Comment fonctionnent les evaluations ?</p>
                <p class="faq-answer">
                    Conducteurs et passagers se notent apres le trajet. Les notes restent visibles
                    pour garantir la transparence.
                </p>
            </div>
            <div class="faq-card">
                <div class="faq-chip">Securite</div>
                <p class="faq-question">Mes donnees sont-elles protegees ?</p>
                <p class="faq-answer">
                    Oui. Les donnees sensibles sont chiffrees et partagees uniquement avec les
                    personnes concernees par la reservation.
                </p>
            </div>
            <div class="faq-card">
                <div class="faq-chip">Support</div>
                <p class="faq-question">Comment contacter l'assistance ?</p>
                <p class="faq-answer">
                    Utilisez le formulaire de contact ou ecrivez a support@cericar.fr. Pensez a
                    indiquer votre identifiant et la reference du voyage.
                </p>
            </div>
        </div>

        <div class="faq-cta">
            <small>Besoin d'aide supplementaire ? Nous repondons sous 48 h.</small>
            <a href="<?= Url::to(['site/contact']) ?>" class="btn btn-outline-light">Contacter le support</a>
        </div>
    </div>
</div>

<?php

namespace app\models; // Espace de noms du modèle.

use Yii; // Accès aux composants Yii.
use yii\base\Model; // Base des formulaires Yii.
use app\models\trajet; // Modèle Trajet (pas utilisé ici).
use app\models\voyage; // Modèle Voyage (pas utilisé ici).

/**
 * ContactForm valide et envoie les messages de contact.
 */
class ContactForm extends Model // Formulaire de contact.
{
    public $name; // Nom de l'expéditeur.
    public $email; // Email de l'expéditeur.
    public $subject; // Sujet du message.
    public $body; // Contenu du message.
    public $verifyCode; // Captcha.


    /**
     * Règles de validation du formulaire de contact.
     *
     * @return array
     */
    public function rules() // Règles de validation.
    {
        return [
            // nom, email, sujet et message sont obligatoires
            [['name', 'email', 'subject', 'body'], 'required'], // Champs requis.
            // email doit être une adresse valide
            ['email', 'email'], // Format email.
            // verifyCode doit être saisi correctement
            ['verifyCode', 'captcha'], // Validation captcha.
        ];
    }

    /**
     * Libellés des champs (utilisés par ActiveForm).
     *
     * @return array
     */
    public function attributeLabels() // Libellés des champs.
    {
        return [
            'verifyCode' => 'Verification Code', // Libellé du captcha.
        ];
    }

    /**
     * Envoie l’email de contact à l’adresse cible.
     *
     * @param string $email
     * @return bool
     */
    public function contact($email) // Envoie l'email.
    {
        if ($this->validate()) { // Valide avant envoi.
            // Compose et envoie l’email avec les champs soumis.
            Yii::$app->mailer->compose()
                ->setTo($email) // Destinataire.
                ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']]) // Expéditeur.
                ->setReplyTo([$this->email => $this->name]) // Réponse vers l'utilisateur.
                ->setSubject($this->subject) // Sujet.
                ->setTextBody($this->body) // Corps du message.
                ->send(); // Envoi.

            return true; // Succès.
        }
        return false; // Échec de validation.
    }









}

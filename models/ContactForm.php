<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\trajet;
use app\models\voyage;

/**
 * ContactForm is the model behind the contact form.
 */
class ContactForm extends Model
{
    public $name;
    public $email;
    public $subject;
    public $body;
    public $verifyCode;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['name', 'email', 'subject', 'body'], 'required'],
            // email has to be a valid email address
            ['email', 'email'],
            // verifyCode needs to be entered correctly
            ['verifyCode', 'captcha'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'verifyCode' => 'Verification Code',
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     * @param string $email the target email address
     * @return bool whether the model passes validation
     */
    public function contact($email)
    {
        if ($this->validate()) {
            Yii::$app->mailer->compose()
                ->setTo($email)
                ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                ->setReplyTo([$this->email => $this->name])
                ->setSubject($this->subject)
                ->setTextBody($this->body)
                ->send();

            return true;
        }
        return false;
    }










public function actionRecherche()
{
    $request = Yii::$app->request;

    // données pour les listes déroulantes
    $vdep = trajet::getDepart();
    $varr = trajet::getArrivee();

    $resultats = [];
    $depart = $arrivee = null;
    $nb = null;

    if ($request->isPost) {

        $depart = $request->post('depart');
        $arrivee = $request->post('arrivee');
        $nb = (int)$request->post('voyageurs');

        // 1. trouver le trajet
        $trajet = trajet::getTrajet($depart, $arrivee);

        if ($trajet) {

            // 2. récupérer les voyages du trajet
            $voyages = voyage::getVoyagesByTrajetId($trajet->id);

            foreach ($voyages as $v) {

                // 3. capacité totale suffisante ?
                if ($v->nb_place < $nb) {
                    continue;
                }

                // 4. places restantes
                $placesRestantes = $v->getPlacesRestantes();

                // 5. statut
                $complet = ($placesRestantes < $nb);

                // 6. coût total
                $prixTotal = $trajet->distance * $v->tarif * $nb;

                $resultats[] = [
                    'conducteur' => $v->conducteurObj->prenom,
                    'places'     => $placesRestantes,
                    'complet'    => $complet,
                    'prix'       => $prixTotal
                ];
            }
        }
    }

    return $this->render('recherche', [
        'vdep'      => $vdep,
        'varr'      => $varr,
        'resultats' => $resultats,
        'depart'    => $depart,
        'arrivee'   => $arrivee,
        'nb'        => $nb
    ]);
}

}

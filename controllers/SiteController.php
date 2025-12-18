<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\internaute;
use app\models\trajet;
use app\models\voyage;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
            $vdep= trajet::getDepart();
            $varr= trajet::getArrivee();

            return $this->render('index', [
                    'vdep'  => $vdep,
                    'varr' => $varr,
                ]);
                    
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }




    public function actionTest($pseudo =null){
        $pseudo="Loup";

        $user=internaute::getUserByIdentifiant($pseudo);


        return $this->render('test',['user'=>$user]);   // charge la vue test.php, injecte la variable $user et construit la page html 
    }



public function actionSignup()
        {
            $model = new \app\models\SignupForm();   // même vide c’est OK
            return $this->render('signup', ['model' => $model]);
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
                if ($v->nbplacedispo < $nb) {
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

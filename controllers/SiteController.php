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
        // Si l’utilisateur est déjà connecté
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        // Crée le formulaire de connexion
        $model = new LoginForm();

        // Si le formulaire est envoyé et les identifiants sont corrects
        if ($model->load(Yii::$app->request->post()) && $model->login()) {

            // Connexion réussie → accueil
            return $this->redirect(['site/index']);
        }

        // Vide le champ mot de passe
        $model->password = '';

        // Affiche le formulaire de connexion
        return $this->render('login', [
            'model' => $model
        ]);
    }








    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        // Déconnecte l’utilisateur
        Yii::$app->user->logout();

        // Retour à l’accueil
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
        $model = new \app\models\SignupForm();

        if (Yii::$app->request->isPost && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Compte créé avec succès.');
            return $this->redirect(['site/login']);
        }

        return $this->render('signup', ['model' => $model]);
    }




                
   public function actionRecherche()
{
    $request = Yii::$app->request;

    $vdep = trajet::getDepart();
    $varr = trajet::getArrivee();

    $resultatsDirects = [];
    $resultatsCorrespondances = [];
    $resultats = [];

    $depart = $arrivee = null;
    $nb = null;
    $cores = false;

    if ($request->isPost) {

        $depart = $request->post('depart');
        $arrivee = $request->post('arrivee');
        $nb = (int)$request->post('voyageurs');
        $cores = (bool)$request->post('correspondance', false);

        /* =========================
           1️⃣ VOYAGES DIRECTS A → C
           ========================= */
        $trajetDirect = trajet::getTrajet($depart, $arrivee);

        if ($trajetDirect) {
            $voyages = voyage::getVoyagesByTrajetId($trajetDirect->id);

            foreach ($voyages as $v) {
                $placesRestantes = $v->getPlacesRestantes();

                if ($nb <= $placesRestantes) {
                    $resultatsDirects[] = [
                        'type'        => 'direct',
                        'voyage_ids'  => [$v->id],
                        'conducteur' => $v->conducteurObj->prenom,
                        'places'     => $placesRestantes,
                        'complet'    => false,
                        'prix'       => $trajetDirect->distance * $v->tarif * $nb,
                        'heure'      => $v->heuredepart,
                        'marque'     => $v->marqueVehicule->marquev,
                        'typev'      => $v->typeVehicule->typev,
                        'bagages'    => $v->nbbagage,
                        'contraintes'=> $v->contraintes
                    ];
                }
            }
        }

        /* ======================================
           2️⃣ CORRESPONDANCES A → B → C (si cochée)
           ====================================== */
        if ($cores) {

            // Tous les trajets A → B
            $trajetsAB = trajet::getTrajetsDepuis($depart);

            foreach ($trajetsAB as $t1) {

                $villeB = $t1->arrivee;

                // Trajet B → C obligatoire
                $trajetBC = trajet::getTrajet($villeB, $arrivee);
                if (!$trajetBC) continue;

                $voyagesAB = voyage::getVoyagesByTrajetId($t1->id);
                $voyagesBC = voyage::getVoyagesByTrajetId($trajetBC->id);

                foreach ($voyagesAB as $v1) {
                    foreach ($voyagesBC as $v2) {

                        if (
                            $nb <= $v1->getPlacesRestantes() &&
                            $nb <= $v2->getPlacesRestantes() &&
                            $v1->heuredepart < $v2->heuredepart
                        ) {
                            $resultatsCorrespondances[] = [
                                'type'        => 'correspondance',
                                'voyage_ids'  => [$v1->id, $v2->id],
                                'conducteur' => $v1->conducteurObj->prenom . ' / ' . $v2->conducteurObj->prenom,
                                'places'     => min($v1->getPlacesRestantes(), $v2->getPlacesRestantes()),
                                'complet'    => false,
                                'prix'       => (
                                    $t1->distance * $v1->tarif +
                                    $trajetBC->distance * $v2->tarif
                                ) * $nb,
                                'heure'      => $v1->heuredepart . ' → ' . $v2->heuredepart,
                                'marque'     => $v1->marqueVehicule->marquev . ' / ' . $v2->marqueVehicule->marquev,
                                'typev'      => $v1->typeVehicule->typev . ' / ' . $v2->typeVehicule->typev,
                                'bagages'    => min($v1->nbbagage, $v2->nbbagage),
                                'contraintes'=> trim($v1->contraintes . ' ' . $v2->contraintes)
                            ];
                        }
                    }
                }
            }
        }

        /* =========================
           3️⃣ FUSION + NOTIFICATION
           ========================= */
        $resultats = array_merge($resultatsDirects, $resultatsCorrespondances);

        $notif = empty($resultats)
            ? ['type' => 'warning', 'message' => 'Aucun voyage trouvé.']
            : ['type' => 'success', 'message' => count($resultats) . ' voyage(s) trouvé(s).'];

        if ($request->isAjax) {
            return $this->asJson([
                'html' => $this->renderPartial('_resultats', [
                    'resultats' => $resultats,
                    'depart'    => $depart,
                    'arrivee'   => $arrivee
                ]),
                'notif' => $notif
            ]);
        }

        return $this->render('recherche', compact(
            'vdep', 'varr', 'resultats', 'depart', 'arrivee', 'nb'
        ));
    }
}


































    public function actionProfil()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        $user = Yii::$app->user->identity;

        return $this->render('profil', [
            'user' => $user
        ]);
    }

































    //part 5


    public function actionReserver(){
        
    }




}
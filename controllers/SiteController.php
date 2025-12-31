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


        $request = Yii::$app->request;//objet yii permet de lire post,get,ajax

        $vdep = trajet::getDepart();//liste distinct des ville de dep pour la datalist
        $varr = trajet::getArrivee();// ville arr

        $resultats = [];//tableau final des vyg a afficher 
        $resultats1 = [];
        $resultats2 = [];
        $depart = $arrivee = null;//valeurs saisies init
        $nb = null;//nombre de voyageurs init
        $cores=null;

        if ($request->isPost) {//on ne calcule la recherche que si on  a soumis le formulaire
            
            $depart = $request->post('depart');//recupere la ville de dep envoyee par le form
            $arrivee = $request->post('arrivee');//arr
            $nb = (int)$request->post('voyageurs');//nbr de voyageurs
            $cores = (bool)$request->post('correspondance',false);//si on veut des correspondances
            
            $trajet = trajet::getTrajet($depart, $arrivee);//on ramene l'objet trajet correspondant 
            
            if ($trajet) {//si le trajet existe 
                $voyages = voyage::getVoyagesByTrajetId($trajet->id);// Récupère tous les voyages proposés pour ce trajet
                
                
                foreach ($voyages as $v) {//on parcourt chaque voyage correspondant au trajet 
                    if($nb<=$v->nbplacedispo){
                        
                        
                        $placesRestantes = $v->getPlacesRestantes();//
                        $complet = ($placesRestantes < $nb);//true si complet
                        
                        $prixTotal = $trajet->distance * $v->tarif * $nb;//ptot
                        
                        $resultats[] = [
                            'conducteur'  => $v->conducteurObj->prenom,
                            'conducteurnom'  => $v->conducteurObj->nom,
                            'places'      => $placesRestantes,
                            'complet'     => $complet,
                            'prix'        => $prixTotal,
                            'heure'       => $v->heuredepart,
                            'marque'      => $v->marqueVehicule->marquev,
                                'type'        => $v->typeVehicule->typev,
                                'bagages'     => $v->nbbagage,
                                'contraintes' => $v->contraintes
                            ];
                        }
                    }
                    
                    $notif = empty($resultats)//si la liste des resultats est vide
                    ? ['type'=>'warning','message'=>'Aucun voyage disponible pour ce trajet.']//on affiche ca
                    : ['type'=>'success','message'=>count($resultats).' voyage(s) trouvé(s).'];//sinon ca
                    
                } else {//si le trajet nexite pas 
                    $notif = ['type'=>'danger','message'=>'Trajet introuvable.'];//on affiche cette notif
                }
                
                if($cores){

                    foreach($varr as $va){
                        if($va!=$arrivee && $va!=$depart){
                            foreach ($vdep as $vd){
                                if($va==$vd){
                                    $trajet1=trajet::getTrajet($depart,$va);
                                    $trajet2=trajet::getTrajet($va,$arrivee);

                                    if($trajet1 && $trajet2){

                                        $voyages1=voyage::getVoyagesByTrajetId($trajet1->id);
                                        $voyages2=voyage::getVoyagesByTrajetId($trajet2->id);

                                        foreach($voyages1 as $v1){
                                            foreach($voyages2 as $v2){
                                                if($nb<=$v1->nbplacedispo && $nb<=$v2->nbplacedispo && $v1->heuredepart < $v2->heuredepart){

                                                    $placesRestantes1 = $v1->getPlacesRestantes();
                                                    $placesRestantes2 = $v2->getPlacesRestantes();
                                                    $complet = ($placesRestantes1 < $nb || $placesRestantes2 < $nb);

                                                    $prixTotal = ($trajet1->distance * $v1->tarif + $trajet2->distance * $v2->tarif) * $nb;

                                                    $resultats[] = [
                                                        'conducteur'  => $v1->conducteurObj->prenom . " / " . $v2->conducteurObj->prenom,
                                                        'conducteurnom'  => $v1->conducteurObj->nom . " / " . $v2->conducteurObj->nom,
                                                        'places'      => min($placesRestantes1, $placesRestantes2),
                                                        'complet'     => $complet,
                                                        'prix'        => $prixTotal,
                                                        'heure'       => $v1->heuredepart . " / " . $v2->heuredepart,
                                                        'marque'      => $v1->marqueVehicule->marquev . " / " . $v2->marqueVehicule->marquev,
                                                        'type'        => $v1->typeVehicule->typev . " / " . $v2->typeVehicule->typev,
                                                        'bagages'     => min($v1->nbbagage, $v2->nbbagage),
                                                        'contraintes' => $v1->contraintes . " / " . $v2->contraintes
                                                    ];
                                                }
                                            }
                                        }

                                }

                            }


                        }




                        


                    }


                }
                // retour de serveur
                if ($request->isAjax) {//si lappel vient dajax
                    return $this->asJson([   //reponse json pas de layout
                        'html' => $this->renderPartial('_resultats', [//html partiel : que les cartes resultats
                            'resultats' => $resultats,//donnees pour la vue partielle
                            'depart'    => $depart,//affichage du trajet sur les cartes
                            'arrivee'   => $arrivee
                        ]),
                        'notif' => $notif ?? null// le massage pour le bondeau
                    ]);
                }
                
                return $this->render('recherche', compact('vdep','varr','resultats','depart','arrivee','nb'));//appel normal sans ajax , variables injectées dans recherche.php
            }
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
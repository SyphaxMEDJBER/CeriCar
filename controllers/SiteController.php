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
use app\models\typevehicule;
use app\models\marquevehicule;


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

            $returnUrl = Yii::$app->request->get('returnUrl');
            if (!empty($returnUrl)) {
                Yii::$app->user->setReturnUrl($returnUrl);
            }

            // Crée le formulaire de connexion
            $model = new LoginForm();

            // Si le formulaire est envoyé et les identifiants sont corrects
            if ($model->load(Yii::$app->request->post())) {
                if (Yii::$app->request->isAjax) {
                    if ($model->login()) {
                        $redirect = Yii::$app->user->getReturnUrl(['site/index']);
                        return $this->asJson([
                            'status' => 'success',
                            'message' => 'Connexion reussie.',
                            'redirect' => $redirect
                        ]);
                    }

                    return $this->asJson([
                        'status' => 'error',
                        'message' => 'Identifiants invalides.',
                        'errors' => $model->getErrors(),
                    ]);
                }

                if ($model->login()) {
                    // Connexion réussie → accueil
                    return $this->goBack();
                }
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

            if (Yii::$app->request->isPost) {
                if (Yii::$app->request->isAjax) {
                    if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                        $user = $model->signup();
                        if ($user) {
                            return $this->asJson([
                                'status' => 'success',
                                'message' => 'Compte cree avec succes.',
                                'redirect' => Yii::$app->urlManager->createUrl(['site/login'])
                            ]);
                        }

                        return $this->asJson([
                            'status' => 'error',
                            'message' => "Erreur lors de l'inscription.",
                        ]);
                    }

                    return $this->asJson([
                        'status' => 'error',
                        'message' => 'Veuillez corriger les erreurs.',
                        'errors' => $model->getErrors(),
                    ]);
                }

                if ($model->signup()) {
                    Yii::$app->session->setFlash('success', 'Compte créé avec succès.');
                    return $this->redirect(['site/login']);
                }
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
            $directCount = 0;
            $corrCount = 0;

            $departInput = $request->post('depart', $request->get('depart'));
            $arriveeInput = $request->post('arrivee', $request->get('arrivee'));
            $nbInput = $request->post('voyageurs', $request->get('voyageurs'));
            $coresInput = $request->post('correspondance', $request->get('correspondance', null));

            if ($request->isPost || ($request->isGet && ($departInput !== null || $arriveeInput !== null || $nbInput !== null || $coresInput !== null))) {//on ne calcule la recherche que si on  a soumis le formulaire
                
                $depart = $departInput;//recupere la ville de dep envoyee par le form
                $arrivee = $arriveeInput;//arr
                $nb = (int)$nbInput;//nbr de voyageurs
                $cores = !empty($coresInput);//si on veut des correspondances
                
                $trajet = trajet::getTrajet($depart, $arrivee);//on ramene l'objet trajet correspondant 
                
                if ($trajet) {//si le trajet existe 
                    $voyages = voyage::getVoyagesByTrajetId($trajet->id);// Récupère tous les voyages proposés pour ce trajet
                    
                    
                    foreach ($voyages as $v) {//on parcourt chaque voyage correspondant au trajet 
                        if($nb<=$v->nbplacedispo){
                            
                            
                            $placesRestantes = $v->getPlacesRestantes();//
                            $complet = ($placesRestantes < $nb);//true si complet
                            
                            $prixTotal = $trajet->distance * $v->tarif * $nb;//ptot
                            
                            $resultats[] = [
                                'type'        => 'direct',
                                'voyage_ids'  => [$v->id],
                                'conducteur'  => $v->conducteurObj->prenom,
                                'conducteurnom'  => $v->conducteurObj->nom,
                                'places'      => $placesRestantes,
                                'complet'     => $complet,
                                'prix'        => $prixTotal,
                                'heure'       => $v->heuredepart,
                                'marque'      => $v->marqueVehicule->marquev,
                                    'typev'        => $v->typeVehicule->typev,
                                    'bagages'     => $v->nbbagage,
                                    'contraintes' => $v->contraintes
                                ];
                            $directCount++;
                            }
                        }
                    } else {//si le trajet nexite pas 
                        $notif = ['type'=>'danger','message'=>'Trajet introuvable.'];//on affiche cette notif
                    }
                    
                


                    
                $buildCorrespondance = function (array $segments) use ($nb) {
                    $voyageIds = [];
                    $conducteurs = [];
                    $heures = [];
                    $marques = [];
                    $types = [];
                    $contraintes = [];
                    $placesMin = null;
                    $bagagesMin = null;
                    $prixTotal = 0;

                    foreach ($segments as $segment) {
                        $v = $segment['voyage'];
                        $t = $segment['trajet'];
                        if (!$v || !$t) {
                            continue;
                        }

                        $voyageIds[] = $v->id;
                        $conducteurs[] = $v->conducteurObj->prenom;
                        $heures[] = $v->heuredepart;
                        $marques[] = $v->marqueVehicule->marquev;
                        $types[] = $v->typeVehicule->typev;
                        $places = $v->getPlacesRestantes();
                        $placesMin = $placesMin === null ? $places : min($placesMin, $places);
                        $bagagesMin = $bagagesMin === null ? $v->nbbagage : min($bagagesMin, $v->nbbagage);
                        if (!empty($v->contraintes)) {
                            $contraintes[] = $v->contraintes;
                        }
                        $prixTotal += $t->distance * $v->tarif;
                    }

                    return [
                        'type' => 'correspondance',
                        'voyage_ids' => $voyageIds,
                        'conducteur' => implode(' / ', $conducteurs),
                        'places' => $placesMin ?? 0,
                        'complet' => $placesMin !== null ? $placesMin < $nb : true,
                        'prix' => $prixTotal * $nb,
                        'heure' => implode(' → ', $heures),
                        'marque' => implode(' / ', $marques),
                        'typev' => implode(' / ', $types),
                        'bagages' => $bagagesMin ?? 0,
                        'contraintes' => trim(implode(' ', $contraintes))
                    ];
                };


                /* ==============================
                2️⃣ CORRESPONDANCES A → B → C
                ============================== */
                if ($cores) {

                    foreach (trajet::getTrajetsDepuis($depart) as $t1) {

                        $villeB = $t1->arrivee;
                        if ($villeB === $arrivee || $villeB === $depart) {
                            continue;
                        }

                        $trajetBC = trajet::getTrajet($villeB, $arrivee);
                        if ($trajetBC) {
                            foreach (voyage::getVoyagesByTrajetId($t1->id) as $v1) {
                                foreach (voyage::getVoyagesByTrajetId($trajetBC->id) as $v2) {

                                    if (
                                        $nb <= $v1->getPlacesRestantes() &&
                                        $nb <= $v2->getPlacesRestantes() &&
                                        $v1->heuredepart < $v2->heuredepart
                                    ) {
                                        $resultats[] = $buildCorrespondance([
                                            ['trajet' => $t1, 'voyage' => $v1],
                                            ['trajet' => $trajetBC, 'voyage' => $v2],
                                        ]);
                                        $corrCount++;
                                    }
                                }
                            }
                        }

                        /* ==============================
                        3️⃣ CORRESPONDANCES A → B → C → D
                        ============================== */
                        foreach (trajet::getTrajetsDepuis($villeB) as $t2) {
                            $villeC = $t2->arrivee;
                            if ($villeC === $arrivee || $villeC === $depart || $villeC === $villeB) {
                                continue;
                            }

                            $trajetCD = trajet::getTrajet($villeC, $arrivee);
                            if (!$trajetCD) {
                                continue;
                            }

                            foreach (voyage::getVoyagesByTrajetId($t1->id) as $v1) {
                                foreach (voyage::getVoyagesByTrajetId($t2->id) as $v2) {
                                    if (
                                        $nb > $v1->getPlacesRestantes() ||
                                        $nb > $v2->getPlacesRestantes() ||
                                        $v1->heuredepart >= $v2->heuredepart
                                    ) {
                                        continue;
                                    }

                                    foreach (voyage::getVoyagesByTrajetId($trajetCD->id) as $v3) {
                                        if (
                                            $nb <= $v3->getPlacesRestantes() &&
                                            $v2->heuredepart < $v3->heuredepart
                                        ) {
                                            $resultats[] = $buildCorrespondance([
                                                ['trajet' => $t1, 'voyage' => $v1],
                                                ['trajet' => $t2, 'voyage' => $v2],
                                                ['trajet' => $trajetCD, 'voyage' => $v3],
                                            ]);
                                            $corrCount++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                    if ($directCount === 0 && $corrCount === 0) {
                        if ($trajet) {
                            $notif = ['type'=>'warning','message'=>'Aucun voyage disponible pour ce trajet.'];
                        } else {
                            $notif = ['type'=>'danger','message'=>'Trajet introuvable.'];
                        }
                    } else {
                        $parts = [];
                        if ($directCount > 0) {
                            $parts[] = $directCount.' direct(s)';
                        }
                        if ($corrCount > 0) {
                            $parts[] = $corrCount.' correspondance(s)';
                        }
                        $notif = ['type'=>'success','message'=>implode(' · ', $parts).' trouvé(s).'];
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

            return $this->render('recherche', compact('vdep','varr','resultats','depart','arrivee','nb'));

        
    }

        public function actionCorrespondanceDetails()
        {
            $request = Yii::$app->request;
            $idsParam = $request->get('ids', '');
            $nb = (int)$request->get('nb', 1);

            $ids = array_values(array_filter(array_map('intval', explode(',', $idsParam))));
            $segments = [];
            $total = 0.0;

            foreach ($ids as $id) {
                $voyage = voyage::findOne($id);
                if (!$voyage) {
                    continue;
                }

                $trajet = $voyage->trajetObj;
                $prix = null;
                if ($trajet) {
                    $prix = $trajet->distance * $voyage->tarif * max($nb, 1);
                    $total += $prix;
                }

                $segments[] = [
                    'depart' => $trajet ? $trajet->depart : null,
                    'arrivee' => $trajet ? $trajet->arrivee : null,
                    'heure' => $voyage->heuredepart,
                    'marque' => $voyage->marqueVehicule ? $voyage->marqueVehicule->marquev : null,
                    'typev' => $voyage->typeVehicule ? $voyage->typeVehicule->typev : null,
                    'bagages' => $voyage->nbbagage,
                    'contraintes' => $voyage->contraintes,
                    'places' => $voyage->getPlacesRestantes(),
                    'prix' => $prix,
                ];
            }

            return $this->renderPartial('_correspondance_details', [
                'segments' => $segments,
                'total' => $total,
                'nb' => $nb,
            ]);
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

        public function actionProposer()
        {
            if (Yii::$app->user->isGuest) {
                return $this->redirect(['site/login']);
            }

            $user = Yii::$app->user->identity;
            if (!$user->permis) {
                return $this->redirect(['site/profil']);
            }

            $request = Yii::$app->request;
            $notif = null;
            $errors = [];
            $embedded = (bool)$request->get('embed', false);
            $form = [
                'depart' => '',
                'arrivee' => '',
                'idtypev' => '',
                'idmarquev' => '',
                'tarif' => '',
                'nbplacedispo' => '',
                'nbbagage' => '',
                'heuredepart' => '',
                'contraintes' => '',
            ];

            if ($request->isPost) {
                $form['depart'] = trim($request->post('depart', ''));
                $form['arrivee'] = trim($request->post('arrivee', ''));
                $form['idtypev'] = (int)$request->post('idtypev', 0);
                $form['idmarquev'] = (int)$request->post('idmarquev', 0);
                $form['tarif'] = $request->post('tarif', '');
                $form['nbplacedispo'] = (int)$request->post('nbplacedispo', 0);
                $form['nbbagage'] = (int)$request->post('nbbagage', 0);
                $form['heuredepart'] = (int)$request->post('heuredepart', 0);
                $form['contraintes'] = trim($request->post('contraintes', ''));

                if ($form['depart'] === '') {
                    $errors[] = 'Le depart est obligatoire.';
                }
                if ($form['arrivee'] === '') {
                    $errors[] = "L'arrivee est obligatoire.";
                }
                if ($form['idtypev'] <= 0) {
                    $errors[] = 'Le type de vehicule est obligatoire.';
                }
                if ($form['idmarquev'] <= 0) {
                    $errors[] = 'La marque de vehicule est obligatoire.';
                }
                if ($form['tarif'] === '' || (float)$form['tarif'] <= 0) {
                    $errors[] = 'Le tarif doit etre superieur a 0.';
                }
                if ($form['nbplacedispo'] <= 0) {
                    $errors[] = 'Le nombre de places doit etre superieur a 0.';
                }
                if ($form['nbbagage'] < 0) {
                    $errors[] = 'Le nombre de bagages doit etre positif.';
                }
                if ($form['heuredepart'] < 0 || $form['heuredepart'] > 23) {
                    $errors[] = "L'heure de depart doit etre comprise entre 0 et 23.";
                }

                $trajet = null;
                if (empty($errors)) {
                    $trajet = trajet::getTrajet($form['depart'], $form['arrivee']);
                    if (!$trajet) {
                        $errors[] = 'Trajet introuvable.';
                    }
                }

                if (!empty($errors)) {
                    $notif = ['type' => 'danger', 'message' => 'Veuillez corriger les erreurs.'];
                } else {
                    $voyage = new voyage();
                    $voyage->conducteur = $user->id;
                    $voyage->trajet = $trajet->id;
                    $voyage->idtypev = $form['idtypev'];
                    $voyage->idmarquev = $form['idmarquev'];
                    $voyage->tarif = (float)$form['tarif'];
                    $voyage->nbplacedispo = $form['nbplacedispo'];
                    $voyage->nbbagage = $form['nbbagage'];
                    $voyage->heuredepart = $form['heuredepart'];
                    $voyage->contraintes = $form['contraintes'];

                    if ($voyage->save()) {
                        $notif = ['type' => 'success', 'message' => 'Voyage propose avec succes.'];
                        $form = [
                            'depart' => '',
                            'arrivee' => '',
                            'idtypev' => '',
                            'idmarquev' => '',
                            'tarif' => '',
                            'nbplacedispo' => '',
                            'nbbagage' => '',
                            'heuredepart' => '',
                            'contraintes' => '',
                        ];
                    } else {
                        $notif = ['type' => 'danger', 'message' => 'Erreur lors de la creation du voyage.'];
                    }
                }

                if ($request->isAjax) {
                    if (!empty($errors)) {
                        return $this->asJson([
                            'status' => 'error',
                            'message' => $notif['message'] ?? 'Erreur.',
                            'errors' => $errors,
                        ]);
                    }

                    return $this->asJson([
                        'status' => ($notif['type'] ?? '') === 'success' ? 'success' : 'error',
                        'message' => $notif['message'] ?? 'Erreur.',
                        'reset' => ($notif['type'] ?? '') === 'success',
                    ]);
                }
            }

            if ($embedded) {
                return $this->renderPartial('proposer', [
                    'user' => $user,
                    'vdep' => trajet::getDepart(),
                    'varr' => trajet::getArrivee(),
                    'types' => typevehicule::find()->all(),
                    'marques' => marquevehicule::find()->all(),
                    'form' => $form,
                    'notif' => $notif,
                    'errors' => $errors,
                    'embedded' => true,
                ]);
            }

            return $this->render('proposer', [
                'user' => $user,
                'vdep' => trajet::getDepart(),
                'varr' => trajet::getArrivee(),
                'types' => typevehicule::find()->all(),
                'marques' => marquevehicule::find()->all(),
                'form' => $form,
                'notif' => $notif,
                'errors' => $errors,
                'embedded' => false,
            ]);
        }

        public function actionReservations()
        {
            if (Yii::$app->user->isGuest) {
                return $this->redirect(['site/login']);
            }

            $user = Yii::$app->user->identity;
            $request = Yii::$app->request;
            if ($request->get('embed')) {
                return $this->renderPartial('_reservations', [
                    'user' => $user,
                    'embedded' => true,
                ]);
            }

            return $this->render('reservations', [
                'user' => $user,
                'embedded' => false,
            ]);
        }

        public function actionMesVoyages()
        {
            if (Yii::$app->user->isGuest) {
                return $this->redirect(['site/login']);
            }

            $user = Yii::$app->user->identity;
            $request = Yii::$app->request;
            if ($request->get('embed')) {
                return $this->renderPartial('_mes_voyages', [
                    'user' => $user,
                    'embedded' => true,
                ]);
            }

            return $this->render('mes-voyages', [
                'user' => $user,
                'embedded' => false,
            ]);
        }

































        //part 5


    public function actionReserver()
    {
        $request = Yii::$app->request;

        //  Utilisateur non connecté → login
        if (Yii::$app->user->isGuest) {
            return $this->asJson([
                'status' => 'login',
                'redirect' => Yii::$app->urlManager->createUrl(['site/login'])
            ]);
        }

        //  Données envoyées
        $idsParam = $request->post('voyage_ids', '');
        $nb = (int)$request->post('nb', 1);

        if (empty($idsParam) || $nb <= 0) {
            return $this->asJson([
                'status' => 'error',
                'message' => 'Données invalides.'
            ]);
        }

        $ids = array_values(array_filter(array_map('intval', explode(',', $idsParam))));
        if (empty($ids)) {
            return $this->asJson([
                'status' => 'error',
                'message' => 'Aucun voyage sélectionné.'
            ]);
        }

        $userId = Yii::$app->user->id;
        $transaction = Yii::$app->db->beginTransaction();

        try {

            foreach ($ids as $voyageId) {

                $voyage = voyage::findOne($voyageId);
                if (!$voyage) {
                    throw new \Exception("Voyage introuvable.");
                }

                if ($voyage->getPlacesRestantes() < $nb) {
                    throw new \Exception("Plus assez de places disponibles.");
                }

                //  Création réservation
                $reservation = new \app\models\reservation();
                $reservation->voyage = $voyageId;
                $reservation->voyageur = $userId;
                $reservation->nbplaceresa = $nb;

                if (!$reservation->save()) {
                    throw new \Exception("Erreur lors de la réservation.");
                }

            }

            $transaction->commit();

            $placesRestantes = [];
            foreach ($ids as $voyageId) {
                $voyage = voyage::findOne($voyageId);
                if ($voyage) {
                    $placesRestantes[$voyageId] = $voyage->getPlacesRestantes();
                }
            }

            return $this->asJson([
                'status' => 'success',
                'message' => 'Réservation confirmée.',
                'places' => $placesRestantes,
                'nb' => $nb,
                'voyage_ids' => $ids
            ]);

        } catch (\Throwable $e) {
            $transaction->rollBack();

            return $this->asJson([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }


}

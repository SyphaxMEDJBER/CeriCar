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
         * Page d’accueil avec les données de base pour la recherche (listes départ/arrivée).
         *
         * @return string HTML rendu.
         */
        public function actionIndex()
        {
                // Prépare les données utilisées par la recherche sur la page d’accueil.
                $vdep= trajet::getDepart();
                $varr= trajet::getArrivee();

                return $this->render('index', [
                        'vdep'  => $vdep,
                        'varr' => $varr,
                    ]);
                        
        }

        /**
         * Action de connexion.
         *
         * - AJAX : retourne du JSON pour auth.js
         * - Non-AJAX : affiche le formulaire de connexion ou redirige en cas de succès
         *
         * @return Response|string JSON ou HTML rendu.
         */

        public function actionLogin()
        {
            // Si l’utilisateur est déjà connecté
            if (!Yii::$app->user->isGuest) {
                return $this->goHome();
            }

            // Mémorise l’URL de retour si fournie (ex: après réservation).
            $returnUrl = Yii::$app->request->get('returnUrl');
            if (!empty($returnUrl)) {
                Yii::$app->user->setReturnUrl($returnUrl);
            }

            // Crée le formulaire de connexion
            $model = new LoginForm();

            // Si le formulaire est envoyé et les identifiants sont corrects
            if ($model->load(Yii::$app->request->post())) {
                // Mode AJAX : on renvoie un JSON pour auth.js.
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

                // Mode classique : on redirige vers la page précédente.
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
         * Action de déconnexion.
         *
         * @return Response Redirection vers l’accueil après déconnexion.
         */
        public function actionLogout()
        {
            // Déconnecte l’utilisateur
            Yii::$app->user->logout();

            // Retour à l’accueil
            return $this->goHome();
        }










        /**
         * Page contact : valide le formulaire et envoie l’email.
         *
         * @return Response|string HTML rendu ou rafraîchissement en cas de succès.
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
         * Page À propos statique.
         *
         * @return string
         */
        public function actionAbout()
        {
            return $this->render('about');
        }




        /**
         * Page de test utilisée pour des vérifications manuelles en développement.
         *
         * @param string|null $pseudo Pseudo optionnel.
         * @return string HTML rendu.
         */
        public function actionTest($pseudo =null){
            $pseudo="Loup";

            $user=internaute::getUserByIdentifiant($pseudo);


            return $this->render('test',['user'=>$user]);   // charge la vue test.php, injecte la variable $user et construit la page html 
        }

        /**
         * Action d’inscription.
         *
         * - AJAX : retourne du JSON pour auth.js
         * - Non-AJAX : affiche le formulaire d’inscription ou redirige vers la connexion en cas de succès
         *
         * @return Response|string JSON ou HTML rendu.
         */
        public function actionSignup()
        {
            $model = new \app\models\SignupForm();

            if (Yii::$app->request->isPost) {
                // Mode AJAX : le JS gère l’affichage des erreurs/succès.
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

                // Mode classique : création puis redirection vers la connexion.
                if ($model->signup()) {
                    Yii::$app->session->setFlash('success', 'Compte créé avec succès.');
                    return $this->redirect(['site/login']);
                }
            }

            return $this->render('signup', ['model' => $model]);
        }




                    
    
        /**
         * Page de recherche avec résultats.
         *
         * - AJAX : retourne du JSON (HTML partiel + notif)
         * - Non-AJAX : affiche la page complète
         *
         * @return string|Response JSON ou HTML rendu.
         */
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

            // On accepte l’entrée en POST (formulaire) ou en GET (navigation interne).
            $departInput = $request->post('depart', $request->get('depart'));
            $arriveeInput = $request->post('arrivee', $request->get('arrivee'));
            $nbInput = $request->post('voyageurs', $request->get('voyageurs'));
            $coresInput = $request->post('correspondance', $request->get('correspondance', null));

            // Calcule les résultats uniquement quand des entrées sont présentes.
            if ($request->isPost || ($request->isGet && ($departInput !== null || $arriveeInput !== null || $nbInput !== null || $coresInput !== null))) {//on ne calcule la recherche que si on  a soumis le formulaire
                
                $depart = $departInput;//recupere la ville de dep envoyee par le form
                $arrivee = $arriveeInput;//arr
                $nb = (int)$nbInput;//nbr de voyageurs
                $cores = !empty($coresInput);//si on veut des correspondances
                
                // Recherche du trajet exact (départ/arrivée).
                $trajet = trajet::getTrajet($depart, $arrivee);//on ramene l'objet trajet correspondant 
                
                if ($trajet) {//si le trajet existe 
                    $voyages = voyage::getVoyagesByTrajetId($trajet->id);// Récupère tous les voyages proposés pour ce trajet
                    
                    
                    foreach ($voyages as $v) {//on parcourt chaque voyage correspondant au trajet 
                        // Filtre si pas assez de places.
                        if($nb<=$v->nbplacedispo){
                            
                            
                            $placesRestantes = $v->getPlacesRestantes();//
                            $complet = ($placesRestantes < $nb);//true si complet
                            
                            // Prix total pour tous les voyageurs.
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
                                'distance'    => $trajet->distance,
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
                    
                


                    
                // Construit une carte de correspondance à partir de plusieurs segments.
                $buildCorrespondance = function (array $segments) use ($nb) {
                    // Agrège les infos de chaque segment pour une carte globale.
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
                        // On conserve le minimum des places/bagages pour refléter la contrainte la plus forte.
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
                        // Si on n’a pas de places, on considère le trajet complet.
                        'complet' => $placesMin !== null ? $placesMin < $nb : true,
                        'prix' => $prixTotal * $nb,
                        'heure' => implode(' → ', $heures),
                        'marque' => implode(' / ', $marques),
                        'typev' => implode(' / ', $types),
                        'bagages' => $bagagesMin ?? 0,
                        'contraintes' => trim(implode(' ', $contraintes))
                    ];
                };

                // Calcule les minutes de départ/arrivée pour comparer les correspondances.
                $getDepartMinutes = function ($voyage) {
                    return (int)$voyage->heuredepart * 60; // Heure départ (h) -> minutes.
                };
                $getArriveeMinutes = function ($voyage, $trajet) {
                    $departMin = (int)$voyage->heuredepart * 60; // Heure départ (h) -> minutes.
                    $dureeMin = (int)round($trajet->distance); // 1 km = 1 minute.
                    return $departMin + $dureeMin; // Heure d'arrivée en minutes.
                };


                /* ==============================
                2️⃣ CORRESPONDANCES A → B → C
                ============================== */
                if ($cores) {

                    foreach (trajet::getTrajetsDepuis($depart) as $t1) {

                        $villeB = $t1->arrivee;
                        // Évite les boucles et le trajet direct déjà traité.
                        if ($villeB === $arrivee || $villeB === $depart) {
                            continue;
                        }

                        $trajetBC = trajet::getTrajet($villeB, $arrivee);
                        if ($trajetBC) {
                            foreach (voyage::getVoyagesByTrajetId($t1->id) as $v1) {
                                foreach (voyage::getVoyagesByTrajetId($trajetBC->id) as $v2) {

                                    // Vérifie places et cohérence temporelle entre segments.
                                    if (
                                        $nb <= $v1->getPlacesRestantes() &&
                                        $nb <= $v2->getPlacesRestantes() &&
                                        $getArriveeMinutes($v1, $t1) < $getDepartMinutes($v2)
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
                            // Évite les cycles et les doublons.
                            if ($villeC === $arrivee || $villeC === $depart || $villeC === $villeB) {
                                continue;
                            }

                            $trajetCD = trajet::getTrajet($villeC, $arrivee);
                            if (!$trajetCD) {
                                continue;
                            }

                            foreach (voyage::getVoyagesByTrajetId($t1->id) as $v1) {
                                foreach (voyage::getVoyagesByTrajetId($t2->id) as $v2) {
                                    // Respecte l’ordre des segments et les places.
                                    if (
                                        $nb > $v1->getPlacesRestantes() ||
                                        $nb > $v2->getPlacesRestantes() ||
                                        $getArriveeMinutes($v1, $t1) >= $getDepartMinutes($v2)
                                    ) {
                                        continue;
                                    }

                                    foreach (voyage::getVoyagesByTrajetId($trajetCD->id) as $v3) {
                                        // Le dernier segment doit partir après le précédent.
                                        if (
                                            $nb <= $v3->getPlacesRestantes() &&
                                            $getArriveeMinutes($v2, $t2) < $getDepartMinutes($v3)
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

                    // Construit la notification globale pour l’interface.
                    if ($directCount === 0 && $corrCount === 0) {
                        if ($trajet) {
                            $notif = ['type'=>'warning','message'=>'Aucun voyage disponible pour ce trajet.'];
                        } else {
                            $notif = ['type'=>'danger','message'=>'Trajet introuvable.'];
                        }
                    } else {
                        // Compose un résumé du nombre de trajets trouvés.
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
                        // Réponse JSON utilisée par recherche.js.
                        return $this->asJson([   //reponse json pas de layout
                            'html' => $this->renderPartial('_resultats', [//html partiel : que les cartes resultats
                                'resultats' => $resultats,//donnees pour la vue partielle
                                'depart'    => $depart,//affichage du trajet sur les cartes
                                'arrivee'   => $arrivee
                            ]),
                            'notif' => $notif ?? null// le massage pour le bondeau
                        ]);
                    }
                    
                    // Mode classique : rendu complet de la page.
                    return $this->render('recherche', compact('vdep','varr','resultats','depart','arrivee','nb'));//appel normal sans ajax , variables injectées dans recherche.php
                
            }

            return $this->render('recherche', compact('vdep','varr','resultats','depart','arrivee','nb'));

        
    }

        /**
         * AJAX : détails des cartes de correspondance.
         *
         * @return string HTML partiel rendu.
         */
        public function actionCorrespondanceDetails()
        {
            $request = Yii::$app->request;
            $idsParam = $request->get('ids', '');
            $nb = (int)$request->get('nb', 1);

            // Convertit la liste d’IDs en tableau d’entiers.
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
                    // Prix par segment = distance * tarif * nb voyageurs.
                    $prix = $trajet->distance * $voyage->tarif * max($nb, 1);
                    $total += $prix;
                }

                $segments[] = [
                    'depart' => $trajet ? $trajet->depart : null,
                    'arrivee' => $trajet ? $trajet->arrivee : null,
                    'heure' => $voyage->heuredepart,
                    'distance' => $trajet ? $trajet->distance : null,
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



        /**
         * Page profil pour les utilisateurs connectés.
         *
         * @return Response|string Redirige vers la connexion si invité, sinon affiche le profil.
         */
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

        /**
         * Proposer un trajet (conducteurs avec permis uniquement).
         *
         * - GET : affiche le formulaire (optionnellement embed=1 pour le partiel)
         * - POST : valide et enregistre, retourne du JSON pour l’AJAX
         *
         * @return Response|string JSON ou HTML rendu.
         */
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
            // embed=1 est utilisé quand ce formulaire est chargé dans la page profil.
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
                // Récupération et normalisation des champs.
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
                    // On vérifie que le trajet existe en base.
                    $trajet = trajet::getTrajet($form['depart'], $form['arrivee']);
                    if (!$trajet) {
                        $errors[] = 'Trajet introuvable.';
                    }
                }

                if (!empty($errors)) {
                    // Erreurs de validation : on affiche une notif d’échec.
                    $notif = ['type' => 'danger', 'message' => 'Veuillez corriger les erreurs.'];
                } else {
                    // Construction de l’entité voyage.
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
                        // Reset des champs après succès.
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
                        // Format JSON attendu par profil.js.
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
                // Version partielle pour affichage dans le profil.
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

            // Version page complète.
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

        /**
         * Liste des réservations.
         *
         * @return Response|string Partiel (embed) ou page complète.
         */
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

        /**
         * Liste des voyages pour l’utilisateur courant.
         *
         * @return Response|string Partiel (embed) ou page complète.
         */
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

































        //partie 5


    /**
     * Réserver un ou plusieurs voyages.
     *
     * @return Response Statut JSON pour l’AJAX.
     */
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

        // Transaction pour garantir l’atomicité des réservations multiples.
        $userId = Yii::$app->user->id;
        $transaction = Yii::$app->db->beginTransaction();

        try {

            foreach ($ids as $voyageId) {

                // Vérifie que chaque voyage existe et a assez de places.
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

            // Tout est OK → commit.
            $transaction->commit();

            // Recalcule les places restantes après réservation.
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
            // En cas d’erreur, on annule toutes les réservations.
            $transaction->rollBack();

            return $this->asJson([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }


}

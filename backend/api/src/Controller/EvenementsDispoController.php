<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Entity\Cours;
use App\Entity\Evenement;
use App\Entity\Promotion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping\Entity;
use Exception;

// retourne les pourcentages de disponibilité
class EvenementsDispoController extends AbstractController
{
    #[Route('/evenements/dispo', name: 'organisation_evenement_date')]
    public function index(Request $request): Response
    {
        $ApiResponse = (object)array();
        $status = 200;
        // prevent request errors
        try {
            // recover id user
            $idEtudiant = intval($request->query->get('idEtudiant'));
            $token = $this->get('security.token_storage')->getToken();

            // check if user info given
            if ($idEtudiant === 0 && !isset($token)) {
                throw new Exception('Bad request. Unknow user');
            }

            // priority given to token
            if (isset($token)) {
                $user = $token->getUser();
                $idEtudiant = intval($user->getId());
            }
        }
        // bad request
        catch (Exception $err) {
            $status = 403;
            $response = [
                "code" => 403,
                "message" => $err->getMessage()
            ];
            return ($this->json($response, $status));
        }


        //récupération de donne grace a un patch
        $parameters = json_decode($request->getContent(), true);

        $dateDebutEvent = (new \DateTime($parameters['dateDebutEvent']));
        $dateFinEvent = (new \DateTime($parameters['dateFinEvent']));
        $typeEvent = ($parameters['typeEvent']);
        $idPromotions = $parameters['promotionEvent'];

        //Initilisaton d'un tableau de tableau 
        $listeJoursMoi = [];
        for ($i = 0; $i < 31; $i++) {
            $listeJoursMoi[$i][0] = '';
        }


        // En fonction du type de l'evenement le traitement ne sera pas le même
        switch ($typeEvent) {
            case "Soiree":
                $ensembleDesCours = $this->getDoctrine()->getRepository(Cours::class)->findAll();
                foreach ($ensembleDesCours as $cours) {

                    $dateDebutCours = $cours->getDateDebut();

                    if ($cours->getExamen() == True) { //est un examen on continue else on passe au cours suivant
                        $moisEvent = date_format($dateDebutEvent, 'm');
                        $anneEvent = date_format($dateDebutEvent, 'y');
                        $moisSuivant = strval(fmod((intval($moisEvent + 1)), 12)); //Modulo 12 permet de passer de décembre à janvier


                        if ((date_format($dateDebutEvent, 'y m') == date_format($dateDebutCours, 'y m')) || date_format($dateDebutCours, 'y m') == $anneEvent . " " . $moisSuivant . "01") {
                            // si année et mois de l'event et du cours sont les memes et que le cours ou si jours cours et le premier du mois suivant else on passe au cours suivant
                            if (date_format($dateDebutCours, 'm d') != $moisEvent . " 01") {
                                $etudiantCours = $cours->getEtudiants(); //liste des etudiants du cours
                                $index = intval(date_format($dateDebutCours, 'd')) - 2;
                                //Decallage -1 car liste commence à 0 puis -2 car pour le 1 er du mois on regarde les cours du 2 du mois
                                foreach ($etudiantCours as $participantCours) { // Pour chaque etudiant on met son id les jours ou il n'est pas dispo pour l'event
                                    if (in_array($participantCours->getPromotion()->getId(), $idPromotions)) { // l'etudiant est bien concerne par l'evenement
                                        $idParticipantCours = $participantCours->getId();
                                        if (in_array($idParticipantCours, $listeJoursMoi[$index]) == false) { // test si l'id a deja été entré 
                                            array_push($listeJoursMoi[$index], $idParticipantCours);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                break;


            case "Journee":


                $ensembleDesCours = $this->getDoctrine()->getRepository(Cours::class)->findAll();


                foreach ($ensembleDesCours as $cours) {

                    $dateDebutCours = $cours->getDateDebut();
                    if (date_format($dateDebutEvent, 'y m ') == date_format($dateDebutCours, 'y m ')) {
                        $dateFinCours = $cours->getDateFin();
                        if ((intval(date_format($dateDebutEvent, 'G')) >= intval(date_format($dateDebutCours, 'G')) && intval(date_format($dateDebutEvent, 'G')) <= intval(date_format(($dateFinCours), 'G'))) ||
                            (intval(date_format($dateDebutCours, 'G')) >= intval(date_format($dateDebutEvent, 'G')) && intval(date_format($dateDebutCours, 'G')) <= intval(date_format($dateFinEvent, 'G')))
                        ) {

                            $etudiantCours = $cours->getEtudiants(); //liste des etudiants du cours
                            $index = intval(date_format($dateDebutCours, 'd')) - 1;
                            //Decallage -1 car liste commence à 0 
                            try {
                                foreach ($etudiantCours as $participantCours) { // Pour chaque etudiant on met son id les jours ou il n'est pas dispo pour l'event     
                                    if (!$participantCours->getPromotion()) {
                                        throw new Exception("Le cours n'a pas de promotion");
                                    }
                                    if (in_array($participantCours->getPromotion()->getId(), $idPromotions)) {
                                        $idParticipantCours = $participantCours->getId();

                                        if (in_array($idParticipantCours, $listeJoursMoi[$index]) == false) { // test si l'id a deja été entré 
                                            array_push($listeJoursMoi[$index], $idParticipantCours);
                                        }
                                    }
                                }
                            } catch (Exception $err) {


                                break;
                            }
                        }
                    }
                }

                break;


            default:
                //Pas de prevision de personne disponible
                $disponibilteParJour = [];
                for ($i = 0; $i < 31; $i++) {
                    $disponibilteParJour[$i] = '';
                }

                $ApiResponse->disponibilite = $disponibilteParJour;
                return ($this->json($ApiResponse));
                break;
        }

        // On compte le nombre de personne concerne par l'evenement
        $promotions = $this->getDoctrine()
            ->getRepository(Promotion::class)
            ->findAll();
        $nbTotalEtudiantConcerne = 0;

        foreach ($promotions as $promotion) {

            try {


                if (!$promotion->getId()) {
                    new Exception("La promotion n'existe pas");
                }
                if (!$promotion->getEtudiants()) {
                    new Exception("Il n'y a pas d'étudiants dans la promotion");
                }

                if (in_array($promotion->getId(), $idPromotions)) { // si cette promo est invite alors on compte ses membres
                    $nbTotalEtudiantConcerne += count($promotion->getEtudiants());
                }
            } catch (Exception) { //permet de sauter une promotion si elle n'existe pas ou si elle n'a pas d'etudiants
            }
        }

        //% disponnibilité tout les jours du mois la disponibiliteParJour[1] correspond au 2 eme jour du mois
        $disponibilteParJour = [];
        for ($i = 0; $i < 31; $i++) {

            $nbElementArray = count($listeJoursMoi[$i]);
            if ($nbElementArray > 1) {
                $result = (((count($listeJoursMoi[$i]) - 1) / $nbTotalEtudiantConcerne) * 100); // -1 car le tableau contient la liste d'id ainsi que la première case ""
            } else { // l'element du tableau ne contient que ""
                $result = 0;
            }
            $valeurPourcentage = strval(100 - $result);
            if (strlen($valeurPourcentage) > 4) { // Empeche d'avoir plus de 1 chiffre apres la virgule (evite les problemen en front)
                $valeurPourcentage = substr($valeurPourcentage, 0, (4 - strlen($valeurPourcentage)));
            }
            array_push($disponibilteParJour, $valeurPourcentage . ' %'); //pourcentage de disponibilité par jour
        }

        $ApiResponse->disponibilite = $disponibilteParJour;
        return ($this->json($ApiResponse));
    }
}

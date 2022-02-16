<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Entity\Evenement;
use App\Entity\MediaObject;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

// ce controlleur permet de générer un fil d'actualité pour chaque étudiant
class EtudiantFeedController extends AbstractController
{
    #[Route('/etudiants/feed', name: 'feed_event')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
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

        // get etudiant
        $etudiant = $this->getDoctrine()
            ->getRepository(Etudiant::class)
            ->find($idEtudiant);

        // get promotion
        $promotionEtudiant = $etudiant->getPromotion()->getId();

        // query in bdd
        $query = $em->createQuery(
            'Select e
            FROM App:Evenement e
            WHERE e.dateDebut > :dateCourante
            ORDER BY e.dateDebut'
        )->setParameter('dateCourante', new DateTime('now'));

        $feedEvenement = $query->getResult();
        $tableauAPI = [];

        // fill the feed with events
        foreach ($feedEvenement as $caseEvenement) {
            $ListeInfoUnEvent = (object)array();
            $idEvent = $caseEvenement->getId();
            $evenement = $this->getDoctrine()
                ->getRepository(Evenement::class)
                ->find($idEvent);

            foreach ($evenement->getPromos() as $promotion) {
                if ($promotion->getId() == $promotionEtudiant) {
                    $ListeInfoUnEvent->idEvent = $evenement->getId();
                    $ListeInfoUnEvent->nomAssociation = $evenement->getAssociation()->getNom();
                    $ListeInfoUnEvent->nomEvent = $evenement->getNom();
                    $ListeInfoUnEvent->nbParticipant = count($evenement->getEtudiants());
                    $idImage = $evenement->getImage();
                    if (isset($idImage)) {
                        $image = $this->getDoctrine()
                            ->getRepository(MediaObject::class)
                            ->find($idImage);
                    }
                    if (isset($idImage) && isset($image)) {
                        $ListeInfoUnEvent->imageEvent = $request->getScheme() . '://' . $request->getHttpHost() . '/media/' . $image->filePath;
                    } else {
                        $ListeInfoUnEvent->imageEvent = $request->getScheme() . '://' . $request->getHttpHost() . '/media/evenements/default.png';
                    };
                    $date = $evenement->getDateDebut();
                    $ListeInfoUnEvent->heure = date_format($date, 'G:i');
                    $ListeInfoUnEvent->jour = date_format($date, 'j');
                    $ListeInfoUnEvent->mois = date_format($date, 'F');
                    $ListeInfoUnEvent->presence = False; //par défaut on met que l'etudiant ne participe pas
                    $CollectionParticipants = $evenement->getEtudiants(); //recupère id des etudiants qui participens
                    foreach ($CollectionParticipants as $etudiant) { //On regarde si il participe deja à l'evenement et on change son statut si c le cas
                        if ($etudiant->getId() == $idEtudiant) {;
                            $ListeInfoUnEvent->presence = True;
                            break;
                        }
                    }

                    array_push($tableauAPI, $ListeInfoUnEvent);
                    break;
                }
            }
        }
        $PageFeed = (object)array();

        $PageFeed->dataOneGrid = $tableauAPI;
        return ($this->json($PageFeed));
    }
}

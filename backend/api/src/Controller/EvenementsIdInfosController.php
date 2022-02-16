<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Entity\Evenement;
use App\Entity\MediaObject;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

// donne des infos sur un évènement en particulier
class EvenementsIdInfosController extends AbstractController
{
    #[Route('/evenements/{id}/infos', name: 'event')]
    public function index(Request $request, string $id): Response
    {
        $status = 200;
        // prevent request errors
        try {
            // recover id event
            if (isset($id)) {
                $idEvent = intval($id);
            } else {
                $idEvent = intval($request->query->get('idEvent'));
            }
            if ($idEvent === 0)
                throw new Exception('Bad request. Unknow event');

            // recover evenement from the database
            $evenement = $this->getDoctrine()
                ->getRepository(Evenement::class)
                ->find(intval($idEvent));

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

        $response = (object)array();

        // l'utilisateur est un administrateur de l'association
        $association = $evenement->getAssociation();
        $administrateurs = $association->getAdministrateurs();
        $response->administre = in_array($idEtudiant, $administrateurs);

        $response->presence = false;

        //recupère id des etudiants qui participens
        $participants = $evenement->getEtudiants();


        $listeParticipants = [];


        foreach ($participants as $etudiant) {

            $infoEtudiant = (object)array();
            $infoEtudiant->identite = $etudiant->getPrenom() . " " . $etudiant->getNom();

            $idPhoto = $etudiant->getPhoto();
            if (isset($idPhoto)) {
                $photo = $this->getDoctrine()
                    ->getRepository(MediaObject::class)
                    ->find($idPhoto);
            }
            if (isset($idPhoto) && isset($photo)) {
                $infoEtudiant->photoEtudiant = $request->getScheme() . '://' . $request->getHttpHost() . '/media/' . $photo->filePath;
            } else {
                $infoEtudiant->photoEtudiant = $request->getScheme() . '://' . $request->getHttpHost() . '/media/etudiants/default.png';
            };

            array_push($listeParticipants, $infoEtudiant);

            $response->presence = $etudiant->getId() == $idEtudiant;
        }

        $etudiantConnecte = $this->getDoctrine()
            ->getRepository(Etudiant::class)
            ->find($idEtudiant);

        $ListeEventEtudiant = [];
        foreach ($etudiantConnecte->getEvenements() as $event) {
            $x = '/evenements/' . strval($event->getId());
            array_push($ListeEventEtudiant, $x);
        }

        $response->nomEvenement = $evenement->getNom();
        $response->descriptionEvenement = $evenement->getDescription();

        $idImage = $evenement->getImage();
        if (isset($idImage)) {
            $image = $this->getDoctrine()
                ->getRepository(MediaObject::class)
                ->find($idImage);
        }
        if (isset($idImage) && isset($image)) {
            $response->imageEvent = $request->getScheme() . '://' . $request->getHttpHost() . '/media/' . $image->filePath;
        } else {
            $response->imageEvent = $request->getScheme() . '://' . $request->getHttpHost() . '/media/evenements/default.png';
        };


        try {
            if (!$evenement->getLieu()) {
                throw new Exception('Le cours n\'a pas de promotion');
            }
            $response->lieuEvent = $evenement->getLieu()->getNom();
        } catch (Exception $err) {
            $response->lieuEvent = "false";
        }

        $response->dateEvenement = date_format($evenement->getDateDebut(), 'l jS F Y à G:i');
        $response->nomAssociation = $evenement->getAssociation()->getNom();
        $response->nbEtudiant = count($evenement->getEtudiants());
        $response->listeParticipant = $listeParticipants;
        $response->listeEventEtudiant = $ListeEventEtudiant;

        return ($this->json($response, $status));
    }
}

<?php

namespace App\Controller;

use App\Entity\Association;
use App\Entity\MediaObject;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Webmozart\Assert\Assert;
use Symfony\Component\HttpFoundation\Request;

// Retourne : le nom, le logo et la description d'une asso. La liste des events organisé par l'asso avec pour chaque event le nom event et la date. Ainsi que le la liste des membres de l'asso.
class AssociationsIdInfosController extends AbstractController
{
    #[Route('/associations/{id}/infos', name: 'page_info_association')]
    public function index(Request $request, string $id): Response
    {
        $idAsso = intval($request->query->get('idAssociation'));
        $idEtudiant = intval($request->query->get('idEtudiant'));

        // prevent request errors
        try {
            // recover id association
            if (isset($id)) {
                $idAsso = intval($id);
            } else {
                $idAsso = intval($request->query->get('idAssociation'));
            }
            if ($idAsso === 0)
                throw new Exception('Bad request. Unknow association');

            // recover evenement from the database
            $assocation = $this->getDoctrine()
                ->getRepository(Association::class)
                ->find($idAsso);

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

        $PageAssociation = (object)array();

        $CollectionMembre = $assocation->getEtudiants();
        $ListeMembre = [];
        foreach ($CollectionMembre as $etudiant) {//récupération liste des membres de l'asso et leurs infos

            $InfoEtudiant = (object)array();
            $identite = $etudiant->getPrenom() . " " . $etudiant->getNom();
            $InfoEtudiant->identite = $identite;

            $idPhoto = $etudiant->getPhoto();
            if (isset($idPhoto)) {
                $photo = $this->getDoctrine()
                    ->getRepository(MediaObject::class)
                    ->find($idPhoto);
            }
            if (isset($idPhoto) && isset($photo)) {
                $InfoEtudiant->photoEtudiant = $request->getScheme() . '://' . $request->getHttpHost() . '/media/' . $photo->filePath;
            } else {
                $InfoEtudiant->photoEtudiant = $request->getScheme() . '://' . $request->getHttpHost() . '/media/etudiants/default.png';
            };
            array_push($ListeMembre, $InfoEtudiant);
        }

        $CollectionEvent = $assocation->getEvenements();
        $ListeEvent = [];
        foreach ($CollectionEvent as $event) {//Recuperation liste des evenements organisés par l'association et leurs infos
            $InfoEvent = (object)array();
            $InfoEvent->id = $event->getId();
            $InfoEvent->nomEvenement = $event->getNom();
            $InfoEvent->dateEvenement = date_format($event->getDateDebut(), 'l jS F Y à G:i');

            $idImage = $event->getImage();
            if (isset($idImage)) {
                $image = $this->getDoctrine()
                    ->getRepository(MediaObject::class)
                    ->find($idImage);
            }
            if (isset($idImage) && isset($image)) {
                $InfoEvent->imageEvent = $request->getScheme() . '://' . $request->getHttpHost() . '/media/' . $image->filePath;
            } else {
                $InfoEvent->imageEvent = $request->getScheme() . '://' . $request->getHttpHost() . '/media/evenements/default.png';
            };
            array_unshift($ListeEvent, $InfoEvent); // Permet de mettre l'element en premier dans le tableau
            //ce qui permet dans l'app d'afficher les plus récents en premier.
        }

        $administrateurs = $assocation->getAdministrateurs();
        // dd($administrateurs, $idEtudiant, in_array($idEtudiant, $administrateurs));

        $PageAssociation->administre = in_array($idEtudiant, $administrateurs);
        $PageAssociation->id = '/associations/' . $assocation->getId();
        $PageAssociation->nomAssociation = $assocation->getNom();
        $idImage = $assocation->getLogo();
        if (isset($idImage)) {
            $image = $this->getDoctrine()
                ->getRepository(MediaObject::class)
                ->find($idImage);
        }
        if (isset($idImage) && isset($image)) {
            $PageAssociation->logoAssociation = $request->getScheme() . '://' . $request->getHttpHost() . '/media/' . $image->filePath;
        } else {
            $PageAssociation->logoAssociation = $request->getScheme() . '://' . $request->getHttpHost() . '/media/associations/default.png';
        };
        $PageAssociation->descriptionAssociation = $assocation->getDescription();
        $PageAssociation->nbParticipants = count($assocation->getEtudiants());
        $PageAssociation->listeEvenements = $ListeEvent;
        $PageAssociation->ListeMembres = $ListeMembre;

        return ($this->json($PageAssociation));
    }
}

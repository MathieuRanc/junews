<?php

namespace App\Controller;

use App\Entity\Association;
use App\Entity\MediaObject;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// send association informations
class AssociationsInfosController extends AbstractController
{
    #[Route('/associations/infos', name: 'liste_asso')]
    public function index(Request $request): Response
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

        $associations = $this->getDoctrine() //Obtenir la liste des assos dans l'ordre alphabétique
            ->getRepository(Association::class)
            ->findBy([], ['nom' => 'ASC']);

        // initialisation de la réponse
        $reponse = (object)array();
        $ListeAssociations = [];
        foreach ($associations as $association) { //Pour chaque asso on récupères les élements que l'on veut afficher dans la liste
            $InfosAsso = (object)array();
            $InfosAsso->nomAssociation = $association->getNom();
            $InfosAsso->idAssociation = $association->getId();
            $InfosAsso->nombreMembre = count($association->getEtudiants());
            //récupération Logo
            $idLogo = $association->getLogo();
            if (isset($idLogo)) {
                $logo = $this->getDoctrine()
                    ->getRepository(MediaObject::class)
                    ->find($idLogo);
            }
            if (isset($idLogo) && isset($logo)) {
                $InfosAsso->logo = $request->getScheme() . '://' . $request->getHttpHost() . '/media/' . $logo->filePath;
            } else {
                $InfosAsso->logo = $request->getScheme() . '://' . $request->getHttpHost() . '/media/associations/default.png';
            };

            array_push($ListeAssociations, $InfosAsso);
        }

        $reponse->associations = $ListeAssociations;
        $reponse->nbAssociations = count($associations);
        return ($this->json($reponse));
    }
}

<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Entity\Evenement;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// change l'état de la participation d'un étudiant pour un évènement en particulier en fonction de son id
class EvenementsIdParticipeController extends AbstractController
{
    #[Route('/evenements/{id}/participe', name: 'participe')]
    public function index(Request $request, string $id): Response
    {
        $status = 200;
        // prevent request errors
        try {
            // recover id user
            $token = $this->get('security.token_storage')->getToken();

            // check if user info given
            if (!isset($token)) {
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

        $etudiant = $this->getDoctrine()
            ->getRepository(Etudiant::class)
            ->find($idEtudiant);

        $evenement = $this->getDoctrine()
            ->getRepository(Evenement::class)
            ->find(intval($id));

        $parameters = json_decode($request->getContent(), true);
        $presence = $parameters['participe'];

        if ($presence) {
            $etudiant->addEvenement($evenement);
            $response = [
                'participe' => true,
                'message' => $etudiant->getNom() . ' ' . $etudiant->getPrenom() . ' participe maintenant à l\'évènement ' . $evenement->getNom()
            ];
        } else {
            $etudiant->removeEvenement($evenement);
            $response = [
                'participe' => false,
                'message' => $etudiant->getNom() . ' ' . $etudiant->getPrenom() . ' ne participe pas à l\'évènement ' . $evenement->getNom()
            ];
        }
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        return ($this->json($response, $status));
    }
}

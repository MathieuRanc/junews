<?php

namespace App\Controller;

use App\Entity\Etudiant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Exception;

// si une personne est administrateur d'une association elle a accès à un menu en plus dans la navbar
class EtudiantsNavbarController extends AbstractController
{
    #[Route('/etudiants/navbar', name: 'navbar')]
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
        $response = (object)array();

        $etudiant = $this->getDoctrine()
            ->getRepository(Etudiant::class)
            ->find($idEtudiant);

        if ($etudiant->getAdministre() !== null) {
            $response->administre = $etudiant->getAdministre();
        } else {
            $response->administre = 0;
        }

        return ($this->json($response));
    }
}

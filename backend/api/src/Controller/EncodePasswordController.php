<?php

namespace App\Controller;

use App\Entity\Etudiant;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

// ce controlleur prend en argument un mot de passe et l'encode
class EncodePasswordController extends AbstractController
{
    #[Route('/encode-password', name: 'encode_password')]
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $status = 200;
        // prevent request errors
        try {
            // recover id user
            $parameters = json_decode($request->getContent(), true);
            $token = $this->get('security.token_storage')->getToken();
            if (!isset($parameters['password'])) {
                throw new Exception('Bad request. No password sended');
            }

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

        // find student by token authentification
        $etudiant = $this->getDoctrine()
            ->getRepository(Etudiant::class)
            ->find($idEtudiant);

        // encode password
        $response['password'] = $encoder->encodePassword($etudiant, $parameters['password']);

        return ($this->json($response));
    }
}

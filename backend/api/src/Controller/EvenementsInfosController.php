<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Promotion;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Exception;

// donne des infos sur l'ensemble des évènements
class EvenementsInfosController extends AbstractController
{
    #[Route('/evenements/infos', name: 'promotions_event')]
    public function index(Request $request): Response
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

        $promotions = $this->getDoctrine()
            ->getRepository(Promotion::class)
            ->findAll();

        $response->promotions = [];

        foreach ($promotions as $promotion) {
            $entity['@id'] = '/promotions/' . $promotion->getId();
            $entity['id'] = $promotion->getId();
            $entity['nom'] = $promotion->getNom();
            array_push($response->promotions, $entity);
        }

        $lieux = $this->getDoctrine()
            ->getRepository(Lieu::class)
            ->findAll();

        $response->lieux = [];

        foreach ($lieux as $lieu) {
            $entity['@id'] = '/lieus/' . $lieu->getId();
            $entity['id'] = $lieu->getId();
            $entity['nom'] = $lieu->getNom();
            array_push($response->lieux, $entity);
        }

        return ($this->json($response));
    }
}

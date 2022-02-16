<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\Etudiant;
use App\Entity\Lieu;
use App\Entity\Promotion;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// récupère un fichier csv de cours en replis la bdd avec
class EDTController extends AbstractController
{
    #[Route('/edt', name: 'edt')]
    public function index(EntityManagerInterface $em): Response
    {

        $response = 'Controller';
        // gestion des erreurs
        try {
            // on regarde si il y a un fichier de données
            if (file_exists('./assets/new/cours.csv')) {
                // l'opération a échouée
                $status = 403;
                throw new Exception('Les données ont été ajoutées à la base de donnée');
            }
            // récupération des données du fichier CSV
            $fp = fopen('./assets/new/cours.csv', 'r');
            $key = fgetcsv($fp, "1024", ";");
            $json = array();
            while ($row = fgetcsv($fp, "1024", ";")) {
                $json[] = array_combine($key, $row);
            }
            foreach ($json as $cours) {
                // pour chaque cours on crée un nouveau cours dans la bdd
                $newCours = new Cours;
                $lieu = $this->getDoctrine()
                    ->getRepository(Lieu::class)
                    ->find(intval($cours['lieu']));

                $newCours
                    ->setNom($cours['nom'])
                    ->setDateDebut(new \DateTime('@' . strtotime($cours['dateDebut'])))
                    ->setDateFin(new \DateTime('@' . strtotime($cours['dateFin'])))
                    ->setDescription($cours['description'])
                    ->setLieu($lieu)
                    ->setExamen($cours['examen'] == 'true');

                $promotion = $this->getDoctrine()
                    ->getRepository(Promotion::class)
                    ->find(intval($cours['promo']));

                foreach ($promotion->getEtudiants() as $etudiant) {
                    $etudiant->addCour($newCours);
                }
                // on écrit la ligne d'ajout de l'entitée dans la bdd
                $em->persist($newCours);
            }
            // on effectue la migration
            $em->flush();
            fclose($fp);
            rename('./assets/new/cours.csv', './assets/actual/cours.csv');

            // l'opération a réussi
            $status = 201;
            $response = [
                'code' => $status,
                'message' => 'Les données ont été ajoutées à la base de donnée'
            ];
        } catch (Exception $err) {
            $response = [
                'code' => $status,
                'message' => $err->getMessage()
            ];
        }

        return ($this->json($response, $status));
    }
}

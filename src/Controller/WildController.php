<?php
// src/Controller/WildController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WildController extends AbstractController
{
    /**
     * @Route("/wild", name="wild_index")
     */
    public function index() :Response
    {
        return $this->render('wild/index.html.twig', [
            'website' => 'Wild Séries',
        ]);
    }

    /**
     * @Route("/wild/show/{slug}",
     *     defaults={"slug"="Aucune série sélectionnée, veuillez choisir une série"},
     *     name="wild_show"
     * )
     */
    public function show(string $slug) :Response
    {
        if (strpos($slug,"_") || strtolower($slug) != $slug) {
            return $this->render('wild/index.html.twig', [
                'website' => "Erreur 404",
            ]);
        } else {
            $slug = ucwords(implode(" ", explode("-", $slug)));

            return $this->render('wild/show.html.twig', [
                'slug' => $slug,
            ]);
        }
    }
}
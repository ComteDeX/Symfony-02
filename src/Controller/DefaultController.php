<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route ("/fuch", name="fuch")
     */
    public function index() : Response
    {
        return $this->render('home.html.twig', [
            'website' => 'Wild Séries',
        ]);
    }
}

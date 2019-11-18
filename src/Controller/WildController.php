<?php
// src/Controller/WildController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class WildController extends AbstractController
{
    public function index() :Response
    {
        return new Response(
            '<html><body>Wild Series Index</body></html>'
        );
    }
}
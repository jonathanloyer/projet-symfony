<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class AcceuilController extends AbstractController
{
    #[Route('/', name: 'app_acceuil')]
function index ()
{
    return $this->render('pages/home/index.html.twig');
}
}
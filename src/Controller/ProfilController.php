<?php

namespace App\Controller;

use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ProfilController extends AbstractController
{
    // je vérifie si l'utilisateur est connecté sinon je le retourne sur la page d'authentification
    #[Route('/profil', name: 'app_profil')]
    function profile(Request $req, UtilisateurRepository $repo)
    {
        if (!$this->isGranted("IS_AUTHENTICATED_FULLY")){
            return $this->redirectToRoute('app_inscription');
        }
        return $this->render('pages/profile/index.html.twig');
    }
}
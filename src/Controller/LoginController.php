<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // je test les erreurs s'il y en a une 
        $error = $authenticationUtils->getLastAuthenticationError();
        //pour facilité la connexion de l'utilisateur je fais en sorte de garder en mémoire ces donnée afin que lorsqu'il clique sur le champ son pseudo ou email apparait 
        $lastUsername = $authenticationUtils->getLastUsername();

        // je crée une nouvelle instance de l'utilisateur et j'y affecte le dernier email utilisé
        // $utilisateur = new Utilisateur();
        // $utilisateur->setEmail($lastUsername)


        // je crée  le formulaire pour la  connexion
        $form = $this->createForm(LoginType::class, [
            'email' => $lastUsername
        ]);
        
        return $this->render('pages/login/index.html.twig', [
            'form' => $form->createView(),
            'error' => $error
            ]);
            if($error) {
                $this->addFlash('error', 'veuillez rentrer le bon mot de passe' );
                return $this->redirectToRoute('app_login');
            }
            
    }

    
}

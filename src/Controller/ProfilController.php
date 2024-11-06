<?php

namespace App\Controller;

use App\Form\ProfileType; // Assurez-vous d'importer votre formulaire de profil
use Doctrine\ORM\EntityManagerInterface; // Importer l'EntityManagerInterface
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request; // Importer Request pour traiter les données du formulaire
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProfilController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager; // Initialiser l'EntityManager
    }

    #[Route('/profile', name: 'app_profile')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')] // Protection de la page de profil
    public function index(): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        return $this->render('pages/profile/index.html.twig', [
            'user' => $user, // Passer l'utilisateur à la vue
        ]);
    }

    #[Route('/profile/update', name: 'app_modifier_informations')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')] // Protection de la page de modification
    public function edit(
        Request $request,
        UtilisateurRepository $repo,
        SluggerInterface $slugger,
        #[Autowire('%kernel.project_dir%/public/uploads/avatars')] string $brochuresDirectory
    ): Response {
        // Récupérer l'utilisateur connecté (dans la base de données grâce aux données de la session)
        $user = $repo->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);

        // Créer un formulaire pour modifier les informations de l'utilisateur
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $avatarFile */
            $avatarFile = $form->get('avatar')->getData();
            var_dump($avatarFile);

            if ($avatarFile) {
                // Générer un nouveau nom de fichier
                $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                // Ceci est nécessaire pour inclure en toute sécurité le nom du fichier dans l'URL
                $safeFilename = $slugger->slug($originalFilename);
                // Générer un nouveau nom de fichier pour l'avatar téléchargé
                // 1. Le nom d'origine du fichier est récupéré et sécurisé avec un slug (pour éviter les caractères spéciaux) grâce au slugger.
                // 2. Un identifiant unique est ajouté au nom du fichier via la fonction uniqid(), afin d'éviter les conflits de noms de fichiers sur le serveur.
                // 3. L'extension du fichier (comme .jpg, .png) est récupérée en utilisant la méthode guessExtension() pour conserver le type du fichier téléchargé.
                // Cela génère un nom unique pour le fichier avatar en combinant le nom sécurisé, un identifiant unique et l'extension du fichier.
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $avatarFile->guessExtension();

                try {
                    $avatarFile->move($brochuresDirectory, $newFilename);
                } catch (FileException $e) {
                    // Gérer l'exception si quelque chose se passe pendant le téléchargement du fichier
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'avatar.');
                }

                // Mettre à jour l'avatar de l'utilisateur
                $user->setAvatar($newFilename);
            }

            // Persist the changes in the database
            $this->entityManager->flush(); // Utiliser l'EntityManager injecté

            // Ajouter un message flash pour indiquer le succès
            $this->addFlash('success', 'Vos informations ont été mises à jour avec succès.');

            // Rediriger vers la page de profil
            return $this->redirectToRoute('app_profile');
        }

        // Afficher le formulaire d'édition
        return $this->render('pages/profile/edit_profile.html.twig', [
            'form' => $form->createView(), // Passer le formulaire à la vue
            'user' => $user, // Passer l'utilisateur à la vue (facultatif)
        ]);
    }
}

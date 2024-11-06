<?php

namespace App\Form;

use App\Entity\Uilisateur; // Assurez-vous d'importer l'entité correcte
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le pseudo est obligatoire.']),
                    new Length([
                        'min' => 6,
                        'max' => 50,
                        'minMessage' => 'Le pseudo doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le pseudo ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 500,
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('email', TextType::class, [
                'label' => 'Email',
                'required' => true,
            ])
            ->add('emploi', TextType::class, [
                'label' => 'Emploi',
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'max' => 50,
                        'minMessage' => 'L\'emploi doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'L\'emploi ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
            ])
            ->add('siteURL', TextType::class, [
                'label' => 'URL du site',
                'required' => false,
            ])
            ->add('avatar', FileType::class, [
                'label' => 'Avatar',
                'required' => false,
                'mapped' => false, // Si vous gérez le téléchargement d'images séparément
                'constraints' => [
                    new File([
                        'extensions' => ['jpg', 'png','JPEG'],
                        'mimeTypesMessage' => 'Le format doit être jpg ou png uniquement.',
                    ]),
                ],
            ]);
    }

    // public function configureOptions(OptionsResolver $resolver): void
    // {
    //     $resolver->setDefaults([
    //         'data_class' => Utilisateur::class, // Assurez-vous d'utiliser l'entité correcte
    //     ]);
    // }
}
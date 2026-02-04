<?php

namespace App\Form;

use App\Entity\Playlist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Formulaire de création / modification d'une Playlist.
 * Gère les champs, les contraintes et les relations (playlist, catégories).
 */
class PlaylistType extends AbstractType
{
    /**
     * Construction du formulaire
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Nom obligatoire
            ->add('name', TextType::class, [
                'label' => 'Nom :',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est obligatoire'])
                ]
            ])
                
            // Description facultative
            ->add('description', TextareaType::class, [
                'label' => 'Description :',
                'attr' => ['rows' => 5],
                'required' => false
            ])
                
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
        ;
    }
 
    /**
     * Configure les options par défaut du formulaire (entité associée, etc.).
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Playlist::class,
        ]);
    }
}

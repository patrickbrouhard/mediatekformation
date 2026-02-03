<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Formation;
use App\Entity\Playlist;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;


/**
 * Formulaire de création / modification d'une Formation.
 * Gère les champs, les contraintes et les relations (playlist, catégories).
 */
class FormationType extends AbstractType
{
    /**
     * Construction du formulaire
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Titre obligatoire
            ->add('title', TextType::class, [
                'label' => 'Titre :',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le titre est obligatoire'])
                ]
            ])
                
            // videoID obligatoire
            ->add('videoId', TextType::class, [
                'label' => 'Video ID :',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'ID vidéo obligatoire'])
                ]
            ])

            // Description facultative
            ->add('description', TextareaType::class, [
                'label' => 'Description :',
                'attr' => ['rows' => 5],
                'required' => false
            ])

            // Date sélectionnée, non future
            ->add('publishedAt', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date :',
                'html5' => true,
                'required' => true,
                'data' => isset($options['data']) && $options['data']->getPublishedAt() != null
                    ? $options['data']->getPublishedAt()
                    : new DateTime('now'),
                'constraints' => [
                    new LessThanOrEqual([
                        'value' => 'today',
                        'message' => 'La date ne peut pas être dans le futur'
                    ])
                ]
            ])

            // Sélection d'une seule playlist (obligatoire)
            ->add('playlist', EntityType::class, [
                'class' => Playlist::class,
                'choice_label' => 'name',
                'label' => 'Playlist :',
                'placeholder' => 'Choisir une playlist',
                'multiple' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'La playlist est obligatoire'])
                ]
            ])

            // Sélection multiple de catégories (facultatif)
            ->add('categories', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'name',
                'label' => 'Catégories (sélection multiple possible) :',
                'multiple' => true,
                'expanded' => true, // test
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
            'data_class' => Formation::class,
        ]);
    }
}

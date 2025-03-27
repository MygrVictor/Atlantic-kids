<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;  // Ajout de TextType
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'label' => 'Titre',
                'required' => true
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'required' => true
            ])
            ->add('image', FileType::class, [
                'label' => 'Image (JPG, PNG)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPG, PNG).',
                    ])
                ],
            ])
            // Champ pour saisir des tags manuellement
            ->add('tags', TextType::class, [
                'label' => 'Tags (séparés par des virgules)',
                'required' => false,
                'attr' => ['placeholder' => 'Tag1, Tag2, Tag3'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Créer la publication',
                'attr' => ['class' => 'btn btn-primary']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
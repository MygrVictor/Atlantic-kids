<?php
namespace App\Form;

use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\Length;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 255]),
                ],
                'label' => 'Titre de la vidéo'
            ])
            ->add('url', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Url(),
                ],
                'label' => 'URL de la vidéo'
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Length(['max' => 1000]),
                ],
                'label' => 'Description de la vidéo'
            ])
            ->add('user', HiddenType::class, [
                'data' => $options['user'], // L'utilisateur est récupéré ici à partir des options du formulaire
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
            'user' => null, // Ajouter l'option 'user' pour pouvoir la passer au formulaire
        ]);
    }
}

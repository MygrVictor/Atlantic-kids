<?php

// src/Form/VideoType.php

namespace App\Form;

use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;  // Utilisation du type TextareaType pour la description
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // On ajoute l'option 'user' pour l'utiliser dans le formulaire
        $user = $options['user'] ?? null;

        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la vidéo',
            ])
            ->add('url', TextType::class, [
                'label' => 'URL de la vidéo YouTube',
            ])
            ->add('description', TextareaType::class, [  // Ajout de la description avec TextareaType
                'label' => 'Description',
                'required' => false,  // Tu peux changer ce champ pour le rendre obligatoire si nécessaire
            ]);

        // Si l'utilisateur est passé dans les options, on peut lier la vidéo à cet utilisateur
        if ($user) {
            // Exemple : ajouter une logique ici si tu veux lier l'utilisateur à la vidéo
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
            'user' => null,  // Ajouter l'option 'user' ici pour la rendre disponible dans le formulaire
        ]);
    }
}

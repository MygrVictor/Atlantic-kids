<?php
namespace App\Form;

use App\Entity\Video;
use Symfony\Component\Form\FormEvent;

class VideoFormListener
{
    public function __invoke(FormEvent $event)
    {
        $form = $event->getForm();
        $video = $event->getData(); // L'objet Video soumis

        if ($video instanceof Video) {
            $user = $form->getConfig()->getOption('user');
            $video->setUser($user); // Lier l'utilisateur à la vidéo
        } else {
            throw new \LogicException('Le formulaire ne contient pas une instance de Video.');
        }
    }
}

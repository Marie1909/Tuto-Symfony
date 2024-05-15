<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Security\UsersAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new Users();//Création de l'utilisateur
        $form = $this->createForm(RegistrationFormType::class, $user);//Création du formulaire d'enregistrement
        $form->handleRequest($request);//Gestion du formulaire

        if ($form->isSubmitted() && $form->isValid()) { //Si formulaire est bon 
            // encode the plain password
            $user->setPassword(
                    $userPasswordHasher->hashPassword(// Mot de passe haché
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);//on persiste
            $entityManager->flush();// et on flush (entré dans la BDD)

            // do anything else you need here, like send an email

            return $security->login($user, UsersAuthenticator::class, 'main');//Authentification de l'utilisateur
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}

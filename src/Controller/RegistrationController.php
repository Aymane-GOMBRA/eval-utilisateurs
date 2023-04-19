<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profilePictureFile = $form['profilePicture']->getData();

        if ($profilePictureFile) {
            // Générer un nom de fichier unique
            $newFilename = uniqid().'.'.$profilePictureFile->guessExtension();

            // Déplacer le fichier vers le répertoire public/uploads/profile_pictures
            try {
                $profilePictureFile->move(
                    $this->getParameter('profile_picture_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                // Gérer les exceptions liées au déplacement du fichier
            }

            // Mettre à jour le champ de la photo de profil de l'utilisateur
            $user->setPdp($newFilename);
        }
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $user->setRoles(["ROLE_USER"]);
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
            $session = $request->getSession();
            $session->set('notification', 'User créer avec succès');
            $session->set('type_notif', 'alert-success');

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}

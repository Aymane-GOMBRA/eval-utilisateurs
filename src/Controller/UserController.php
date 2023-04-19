<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use App\Entity\User;
use App\Form\EditUserFormType;
use App\Form\RegistrationFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// ...

class UserController extends AbstractController
{
    #[Route('/', name:'list_users')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $users = $em->getRepository(User::class)->findAll();
        $session = $request->getSession();
        $notification = $session->get('notification');
        $type_notif = $session->get('type_notif');
        return $this->render('users/index.html.twig', [
            "users"=>$users,
            'notification'=>$notification,
            'type_notif'=>$type_notif,
        ]);
    }

    #[Route('/user/{id_user}', name:'view_user')]
    public function viewUser($id_user,Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $user = $em->getRepository(User::class)->find($id_user);
        if($user === null){
            return $this->redirectToRoute('list_article');
        }
        return $this->render('users/view_user.html.twig', [
            "user"=>$user
        ]);
    }

    #[Route('/user/edit/{id_user}', name:'edit_user')]
    public function editUser($id_user,Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $user = $em->getRepository(User::class)->find($id_user);
        if($user === null){
            return $this->redirectToRoute('list_article');
        }
        $form = $this->createForm(EditUserFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $em->flush();
            $session = $request->getSession();
            $session->set('notification', 'User modifié avec succès');
            $session->set('type_notif', 'alert-success');
            return $this->redirectToRoute('list_users');
        }
        return $this->render('users/edit_user.html.twig', [
            "user"=>$user,
            "form"=>$form->createView()
        ]);
    }
}
<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function adminPanel
    (
        UserRepository $userRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');


        return $this->render('admin/index.html.twig', [
            'users' => $userRepository->findBy(array(), array('id' => 'DESC')),
        ]);
    }


    #[Route('/admin/delete/figure/{id}', name: 'admin_delete_user')]
    public function deleteUser(UserRepository $userRepository, EntityManagerInterface $entityManager,int $id, Request $request): Response
    {
        // Verify if user is an admin and throw AccesDeniedException if he's not
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = $userRepository->findOneBy(array('id' => $id));

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin');
    }
}

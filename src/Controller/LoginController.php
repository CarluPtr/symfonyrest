<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(): Response
    {
        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
        ]);
    }

    #[Route("/logout", name:"app_logout")]
    public function logout( Request $request, EventDispatcherInterface $eventDispatcher, TokenStorageInterface $tokenStorage)
    {
        $logoutEvent = new LogoutEvent($request, $tokenStorage->getToken());
        $eventDispatcher->dispatch($logoutEvent);
        $tokenStorage->setToken(null);
        return $this->redirect($this->generateUrl('home'));
    }
}
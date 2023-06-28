<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function profilePage(): Response {
        $user = $this->getUser();

        return $this->render('account/index.html.twig', [
            'user' => $user,
        ]);
    }
}

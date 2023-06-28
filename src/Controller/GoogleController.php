<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;

class GoogleController extends AbstractController
{
    #[Route('/login/google', name: 'login_google')]
    public function connectAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect();
    }


    #[Route('/login/google/check', name: 'login_google_check')]
    public function connectCheckAction(Request $request)
    {
        if (!$this->getUser()){
            return new JsonResponse(array('status' => false, 'message' => "User not Found!"));
        }
        else{
            return $this->redirectToRoute('api');
        }
    }
}

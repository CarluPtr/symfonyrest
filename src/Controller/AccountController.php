<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function profilePage(): Response {
        $user = $this->getUser();

        return $this->render('account/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/api/users', name: 'users', methods: ['GET'])]
    public function getProductList(UserRepository $userRepository, SerializerInterface $serializer, Request $request): JsonResponse
    {
        $page = $request->get('page',1);
        $limit = $request->get('limit', 5);

        $usersList = $userRepository->findAllWithPagination($page, $limit);
        $jsonUsersList = $serializer->serialize($usersList, 'json');
        return new JsonResponse($jsonUsersList, Response::HTTP_OK, [], true);

    }
}

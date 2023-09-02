<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    #[Route('/api/users/{id}', name: 'deleteUser', methods: ['DELETE'])]
    public function deleteProduct(User $user, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em->remove($user);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}

<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Annotations as OA;
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

    /**
     * Cette méthode permet de récupérer l'ensemble des users reliés au client de l'utilisateur de session.
     *
     * @OA\Response(
     *     response=200,
     *     description="Retourne la liste des users",
     * )

     * @OA\Tag(name="Users")
     */
    #[Route('/api/users', name: 'users', methods: ['GET'])]
    public function getUsersList(UserRepository $userRepository, SerializerInterface $serializer, Request $request): JsonResponse
    {
        $user = $this->getUser();

        $usersList = $userRepository->findBy(array("client"=> $user->getClient()));
        $jsonUsersList = $serializer->serialize($usersList, 'json', ['groups' => ['user']]);
        return new JsonResponse($jsonUsersList, Response::HTTP_OK, [], true);

    }

    /**
     * Récupère les détails d'un user spécifique.
     *
     * @OA\Response(
     *     response=200,
     *     description="Retourne les détails d'un user",
     * )
     * @OA\Tag(name="Users")
     */
    #[Route('/api/users/{id}', name: 'userInfo', methods: ['GET'])]
    public function getUserInfo(User $user, SerializerInterface $serializer, Request $request): JsonResponse
    {
        $sessionUser = $this->getUser();
        if($user->getClient() == $sessionUser->getClient()){
            $jsonUser = $serializer->serialize($user, 'json', ['groups' => ['user']]);
            return new JsonResponse($jsonUser, Response::HTTP_OK, ['accept' => 'json'], true);
        }
        else{
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

    }

    /**
     * Supprime un user spécifique.
     *
     * @OA\Response(
     *     response=204,
     *     description="Supprime un user (besoin d'être connecté à un compte administrateur pour effectuer la requête.)",
     * )
     * @OA\Tag(name="Users")
     */
    #[Route('/api/users/{id}', name: 'deleteUser', methods: ['DELETE'])]
    public function deleteProduct(User $user, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em->remove($user);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}

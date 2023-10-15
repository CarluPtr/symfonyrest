<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class ProductController extends AbstractController
{

    #[Route('/api', name: 'api', methods: ['GET'])]
    public function api()
    {
        $data =
            [
                'Welcome' => "Vous avez maintenant accès à l'API",
                "Vous pouvez également consulter votre profil ici" => $this->generateUrl('app_account'),
                "Consulter la liste des produits de BileMo ici" => $this->generateUrl('product'),
                "Consulter la liste des utilisateurs de BileMo ici" => $this->generateUrl('users')
        ];
        return new JsonResponse(json_encode($data), Response::HTTP_OK, [], true);

    }

    /**
     * Cette méthode permet de récupérer l'ensemble des produits.
     *
     * @OA\Response(
     *     response=200,
     *     description="Retourne la liste des produits",
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="La page que l'on veut récupérer",
     *     @OA\Schema(type="int")
     * )
     *
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Le nombre d'éléments que l'on veut récupérer",
     *     @OA\Schema(type="int")
     * )
     * @OA\Tag(name="Products")
     */
    #[Route('/api/products', name: 'product', methods: ['GET'])]
    public function getProductList(ProductRepository $productRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cache): JsonResponse
    {
        $page = $request->get('page',1);
        $limit = $request->get('limit', 5);

        $idCache = "getProductList-" . $page . "-" . $limit;

        $productList = $cache->get($idCache, function (ItemInterface $item) use ($productRepository, $page, $limit){
            $item->tag("productsCache");
            return $productRepository->findAllWithPagination($page, $limit);
        });

        $jsonProductList = $serializer->serialize($productList, 'json');
        return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);

    }
    /**
     * Récupère les détails d'un produit spécifique.
     *
     * @OA\Response(
     *     response=200,
     *     description="Retourne les détails d'un produit",
     * )
     * @OA\Tag(name="Products")
     */
    #[Route('/api/products/{id}', name: 'detailProduct', methods: ['GET'])]
    public function getDetailProduct(Product $product, SerializerInterface $serializer): JsonResponse {

        $jsonProduct = $serializer->serialize($product, 'json');
        return new JsonResponse($jsonProduct, Response::HTTP_OK, ['accept' => 'json'], true);
    }
    /**
     * Supprime d'un produit spécifique.
     *
     * @OA\Response(
     *     response=204,
     *     description="Supprime un produit (besoin d'être connecté à un compte administrateur pour effectuer la requête.)",
     * )
     * @OA\Tag(name="Products")
     */
    #[Route('/api/products/{id}', name: 'deleteProduct', methods: ['DELETE'])]
    public function deleteProduct(Product $product, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em->remove($product);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
    /**
     * Créer un produit.
     *
     *
     * @OA\RequestBody(
     *     description="Add user",
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *                 property="title",
     *                 description="titre du produit",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="content",
     *                 description="Contenu du produit",
     *                 type="string"
     *             )
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=201,
     *          *     description="Créer un produit (besoin d'être connecté à un compte administrateur pour effectuer la requête.)",
     * )
     * @OA\Tag(name="Products")
     */
    #[Route('/api/create-product', name:"createProduct", methods: ['POST'])]
    public function createProduct(
        Request $request, SerializerInterface $serializer, EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator
    ): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $product = $serializer->deserialize($request->getContent(), Product::class, 'json');

        $errors = $validator->validate($product);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $em->persist($product);
        $em->flush();

        $jsonProduct = $serializer->serialize($product, 'json');

        $location = $urlGenerator->generate('detailProduct', ['id' => $product->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonProduct, Response::HTTP_CREATED, ["Location" => $location], true);
    }
}

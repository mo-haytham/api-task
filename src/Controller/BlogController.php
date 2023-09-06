<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Form\BlogType;
use App\Repository\BlogPostRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", methods={"GET"})
     */
    public function index(BlogPostRepository $repo): JsonResponse
    {
        return $this->json($repo->getAllInArray());
    }

    /**
     * @Route("/blog/store", methods={"POST"})
     */
    public function store(Request $request, EntityManagerInterface $em): JsonResponse
    {

        $blog = new BlogPost();

        $form = $this->createForm(BlogType::class, $blog);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($blog);
            $em->flush();

            return $this->json([
                "message" => "Successfully submitted",
            ], Response::HTTP_CREATED);
        }

        return $this->json([
            "message" => "Invalid"
        ], Response::HTTP_CREATED);
    }
}

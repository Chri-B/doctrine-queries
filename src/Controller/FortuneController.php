<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\FortuneCookieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class FortuneController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(Request $request, CategoryRepository $categoryRepository): Response
    {
        $searchTerms = $request->query->get('q');

        if($searchTerms) {
            $categories = $categoryRepository->search($searchTerms);
        } else {
            $categories = $categoryRepository->findaAllOrdered();
        }

        return $this->render('fortune/homepage.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/category/{id}', name: 'app_category_show')]
    public function showCategory(int $id, CategoryRepository $categoryRepository, FortuneCookieRepository $fortuneCookieRepository): Response
    {
        $category = $categoryRepository->findWithFortunesJoin($id);

        if(!$category) {
            throw $this->createNotFoundException("Category not found!");
        }

        $stats = $fortuneCookieRepository->countNumberPrintedForCategory($category);

        return $this->render('fortune/showCategory.html.twig', [
            'category' => $category,
            'fortunesPrinted' => $stats->fortunesPrinted,
            'fortunesAverage' => $stats->fortunesAvereage,
            'categoryName' => $stats->categoryName
        ]);
    }
}

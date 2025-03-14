<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PersonController extends AbstractController
{
    #[Route('/people', name: 'people_index')]
    public function index(): Response
    {
        return $this->render('people/index.html.twig', [
            'controller_name' => 'PersonController',
        ]);
    }

    #[Route('/people/add', name: 'people_add')]
    public function add(): Response
    {
        return $this->render('people/form.html.twig', []);
    }
}

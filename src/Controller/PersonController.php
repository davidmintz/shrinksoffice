<?php

namespace App\Controller;

use App\Entity\Person;
use App\Form\PersonType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PersonController extends AbstractController
{
    #[Route('/people', name: 'people_list')]
    public function index(): Response
    {
        return $this->render('person/index.html.twig', [
            'controller_name' => 'PersonController',
        ]);
    }
    #[Route('/people/add', name: 'people_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {

        $person = new Person();
        $form = $this->createForm(PersonType::class, $person);

        $form->handleRequest($request);

        // Handle successful form submission
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($person);
            $entityManager->flush();

            // Return JSON if it's an AJAX request
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse([
                    'success' => true,
                    'message' => 'Person added successfully!',
                ]);
            }

            // Otherwise, fallback to a normal redirect
            $this->addFlash('success', 'Person added successfully!');
            return $this->redirectToRoute('people_list');
        }

        // If validation fails and it's an AJAX request, return the form HTML
        if ($request->isXmlHttpRequest()) {
            return new Response(
                $this->renderView('person/_form.html.twig', ['form' => $form->createView()]),
                Response::HTTP_BAD_REQUEST
            );
        }

        // For normal GET requests, render the full page
        return $this->render('person/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

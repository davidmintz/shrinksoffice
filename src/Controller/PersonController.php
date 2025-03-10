<?php

namespace App\Controller;

use App\Entity\Person;
use App\Form\PersonType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Psr\Log\LoggerInterface;

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
    #[Route('/api/search-payers', name: 'search_payers', methods: ['GET'])]
    public function searchPayers(Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger): JsonResponse
    {
//        $query = $request->query->get('q', '');
//
//        $payers = $entityManager->getRepository(Person::class)->createQueryBuilder('p')
//            ->where('p.type = :type')
//            ->andWhere('p.firstname LIKE :query OR p.lastname LIKE :query')
//            ->setParameter('type', 'payer')
//            ->setParameter('query', '%' . $query . '%')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult();
//
//        $data = array_map(fn(Person $payer) => [
//            'id' => $payer->getId(),
//            'firstname' => $payer->getFirstname(),
//            'lastname' => $payer->getLastname(),
//        ], $payers);
//
//        return new JsonResponse($data);
        $query = $request->query->get('q');

        if (!$query) {
            return new JsonResponse([], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Build the QueryBuilder
        $qb = $entityManager->createQueryBuilder();
        $qb->select('p.id, p.firstname, p.lastname')
            ->from(Person::class, 'p')
            ->where('p.firstname LIKE :query OR p.lastname LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->setMaxResults(10);

        // Convert QueryBuilder to Query
        $doctrineQuery = $qb->getQuery();

        // Log the DQL (Doctrine Query Language)
        $logger->info("DQL Query: " . $doctrineQuery->getDQL());

        // Log the SQL query generated from DQL
        $sql = $doctrineQuery->getSQL();
        $logger->info("SQL Query: " . $sql);

        // Log query parameters
        $parameters = $doctrineQuery->getParameters();
        foreach ($parameters as $parameter) {
            $logger->info("Parameter: " . $parameter->getName() . " => " . $parameter->getValue());
        }

        // Execute the query and return JSON response
        $result = $doctrineQuery->getResult();

        return new JsonResponse($result);
    }

    #[Route('/autocomplete/person', name: 'autocomplete_person', methods: ['GET'])]
    public function autocomplete(Request $request, EntityManagerInterface $em)
    {
        $query = $request->query->get('q', '');
        $results = $em->getRepository(Person::class)->findActiveByName($query);

        return new JsonResponse(array_map(fn($p) => [
            'id' => $p->getId(),
            'text' => $p->getFirstname() . ' ' . $p->getLastname(),
        ], $results));
    }
}

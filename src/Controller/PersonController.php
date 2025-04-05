<?php

namespace App\Controller;

use App\Entity\Person;
use App\Form\PersonType;
use App\Repository\PersonRepository;
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
    public function add(Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger): Response
    {
        $entityType = 'person';
        $verb = 'created';

        $person = new Person();
        $form = $this->createForm(PersonType::class, $person);

        $form->handleRequest($request);

        // Handle successful form submission
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($person);
            $entityManager->flush();
            $logger->debug("we did a flush()");
            // Return JSON if it's an AJAX request
            if ($request->isXmlHttpRequest()) {
                $logger->debug("returning JSON");
                return new JsonResponse([
                    'success' => true,
                    'message' => 'Person added successfully!',
                    'id' => $person->getId(),
                ]);
            }

            // Otherwise, fallback to a normal redirect
            $this->addFlash('success', 'A new person was successfully added to the database.');
            return $this->redirectToRoute('person_list');
        }

        // If validation fails and it's an AJAX request, return the form HTML
        $logger->debug("shit is at line ".__LINE__);
        if ($request->isXmlHttpRequest()) {
            $logger->debug("failed validation, re-rendering form");
            return new Response(
                $this->renderView('person/_form.html.twig',
                    ['form' => $form->createView(),
                        'entity_type' => $entityType,
                        'verb' => $verb,
                    ]),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        $logger->debug("initial GET, gonna render full template");

        // normal GET request, render the full page
        return $this->render('person/form.html.twig', [
            'form' => $form->createView(),
            'entity_type' => $entityType,
            'verb' => $verb,
        ]);
    }

    #[Route('/person/test', name: 'person_test', methods: ['GET'])]
    public function test(Request $request, PersonRepository $repo) : JsonResponse
    {
        $query = $request->query->get('q', '');
        $dql_query = $repo->createQueryBuilder('p')
            ->select('p.lastname, p.firstname, p.id')
            ->andWhere('p.firstname LIKE :query OR p.lastname LIKE :query')
            ->andWhere('p.active = 1')
            ->setParameter('query', "%$query%")
            ->setMaxResults(10)
            ->getQuery();
        //$dql = $dql_query->getDQL();
        //$result = $repo->findByName($query);
        $result = $dql_query->getResult();
        return new JsonResponse(['result'=>$result]);
    }
}

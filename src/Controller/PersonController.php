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
use App\Controller\Traits\HandlesFormCrud;

use Psr\Log\LoggerInterface;

class PersonController extends AbstractController
{
    use HandlesFormCrud;

    public function __construct(private readonly EntityManagerInterface $entityManager
    ) {}

    #[Route('/people', name: 'people_list')]
    public function index(): Response
    {
        return $this->render('person/index.html.twig', [
            'controller_name' => 'PersonController',
        ]);
    }

    #[Route('/people/add', name: 'person_create')]
    public function create(Request $request): Response
    {
        $person = new Person();
        $form = $this->createForm(PersonType::class, $person);

        return $this->handleForm($request, $person, $form);
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


    #[Route('/people/update/{id}', name: 'person_update')]
    public function update(Request $request, Person $person): Response
    {
        $form = $this->createForm(PersonType::class, $person);

        return $this->handleForm($request, $person, $form);
    }

    protected function getSuccessMessage(object $entity): string
    {
        /** @var Person $entity */
        $who  = '<strong>'.
                htmlspecialchars("{$entity->getFirstname()} {$entity->getLastname()}")
                .'</strong>';
        return $entity->getId() === null
            ? sprintf('%s has been added to the database.', $who)
            : sprintf('%s has been updated.', $who);
    }
    protected function getFormTemplate(): string
    {
        return 'person/_form.html.twig';
    }
    protected function getFormViewParameters(object $entity): array
    {
        return ['person' => $entity];
    }


}

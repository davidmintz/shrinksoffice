<?php
namespace App\Form\DataTransformer;

use App\Entity\Person;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class PersonToIdTransformer implements DataTransformerInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // Transforms a Person object to an ID for the form field
    public function transform($person): ?int
    {
        return $person ? $person->getId() : null;
    }

    // Transforms an ID back to a Person object when submitted
    public function reverseTransform($personId): ?Person
    {
        if (!$personId) {
            return null;
        }

        $person = $this->entityManager->getRepository(Person::class)->find($personId);

        if (!$person) {
            throw new TransformationFailedException(sprintf(
                'A payer with ID "%s" does not exist.',
                $personId
            ));
        }

        return $person;
    }
}

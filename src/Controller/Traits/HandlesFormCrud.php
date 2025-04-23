<?php

namespace App\Controller\Traits;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Trait HandlesFormCrud
 *
 * Used in controllers that extend AbstractController
 * and define $this->entityManager via constructor injection.
 * Uses getFormTemplate() to return the partial (_form.html.twig)
 * and derives the full-page form automatically by stripping the underscore.
 *
 * @mixin AbstractController
 * @property EntityManagerInterface $entityManager
 */
trait HandlesFormCrud
{
    protected function handleForm(Request $request, object $entity, FormInterface $form): Response
    {
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->entityManager->persist($entity);
                $this->entityManager->flush();

                //$isNew = $entity->getId() === null;
                $message = $this->getSuccessMessage($entity);

                return new JsonResponse([
                    'success' => true,
                    'message' => $message,
                    'id' => $entity->getId(),
                ]);
            }

            return new Response(
                $this->renderView($this->getFormTemplate(), [
                    'form' => $form->createView(),
                ]),
                422
            );

        }

        // initial GET request or unsubmitted form
        $fullPageTemplate = str_replace('/_', '/', $this->getFormTemplate());

        return $this->render($fullPageTemplate, array_merge(
            ['form' => $form->createView()],
            $this->getFormViewParameters($entity)
        ));
    }

    abstract protected function getFormTemplate(): string;

    abstract protected function getSuccessMessage(object $entity): string;

    /**
     * Returns additional view variables to pass when rendering the form page.
     *
     * This allows controllers using the HandlesFormCrud trait to inject the entity
     * with a semantic variable name (e.g., 'person', 'invoice') so that the shared
     * Twig form partial can reference it as needed (e.g., for setting the form action).
     *
     * @param object $entity The entity bound to the form
     * @return array Associative array of template variables to merge into the view context
     */
    abstract protected function getFormViewParameters(object $entity): array;
}

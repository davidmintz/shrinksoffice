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
    protected function handleForm(Request $request, object $entity, FormInterface $form, bool $isNew): Response
    {
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->entityManager->persist($entity);
                $this->entityManager->flush();

                $message = $this->getSuccessMessage($entity, $isNew);

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

        return $this->render($fullPageTemplate, [
            'form' => $form->createView(),
        ]);
    }

    abstract protected function getFormTemplate(): string;

    abstract protected function getSuccessMessage(object $entity, bool $isNew): string;
}

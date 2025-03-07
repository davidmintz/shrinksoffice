<?php

namespace App\Controller;

use App\Service\PdfGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvoiceController extends AbstractController
{
    #[Route('/invoice/{id}', name: 'invoice_pdf')]
    public function generateInvoice(PdfGenerator $pdfGenerator, int $id): Response
    {
        $invoiceData = [
            'sender_name' => 'Your Practice Name',
            'sender_address' => '123 Main St',
            'sender_city' => 'YourCity',
            'sender_state' => 'ST',
            'sender_zip' => '12345',
            'recipient_name' => 'John Doe',
            'recipient_address' => '456 Elm St',
            'recipient_city' => 'AnotherCity',
            'recipient_state' => 'NY',
            'recipient_zip' => '67890',
            'invoice_id' => $id,
            'invoice_date' => (new \DateTime())->format('Y-m-d'),
            'services' => [
                ['date' => new \DateTime('2025-02-03'), 'duration' => '45 minutes', 'fee' => 250, 'diagnostic_code' => 451],
                ['date' => new \DateTime('2025-02-10'), 'duration' => '45 minutes', 'fee' => 250, 'diagnostic_code' => 451],
                ['date' => new \DateTime('2025-02-17'), 'duration' => '45 minutes', 'fee' => 250, 'diagnostic_code' => 451],
                ['date' => new \DateTime('2025-02-24'), 'duration' => '45 minutes', 'fee' => 250, 'diagnostic_code' => 451],
            ],
            'total_due' => 1000,
        ];

        $html = $this->renderView('invoice/invoice.html.twig', $invoiceData);

        return $pdfGenerator->generateInvoicePdf($html);
    }
}


<?php
namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Response;

class PdfGenerator
{
    public function generateInvoicePdf(string $html): Response
    {
        // Set up Dompdf options
        $options = new Options();
        $options->set('defaultFont', 'Arial');  // Set default font
        $options->set('isHtml5ParserEnabled', true);  // Enable better HTML support
        $options->set('isRemoteEnabled', true);  // Allow external resources (e.g., images, stylesheets)

        // Create Dompdf instance
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);  // Load HTML content
        $dompdf->setPaper('Letter', 'portrait');  // Set paper size and orientation
        $dompdf->render();  // Render the PDF

        // Return PDF response
        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="invoice.pdf"',
            ]
        );
    }
}

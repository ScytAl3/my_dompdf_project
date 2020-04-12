<?php

namespace App\Service\Facture;

// Include Dompdf required namespaces
use Dompdf\Dompdf;
use Dompdf\Options;

use App\Repository\FactureRepository;
use Twig\Environment;

/**
 * service utiliser par FactureController pour enregistrer une facture au format PDF et l afficher dans un
 * nouvel onglet
 */
class FactureService
{
    protected $factureRepository;
    protected $twig;
    protected $pdfDirectory;

    /**
     * methode d ajout de dependances a la methode __construct aka injection de dependance
     * 
     * @param FactureRepository le repository pour pouvoir appeller la methode find()
     * @param Environment   pour travailler avec un template twig
     * @param String    le chemin du dossier pour enregistrer les factures generees define in config/services.yaml
     */
    public function __construct(FactureRepository $factureRepository, Environment $twig, string $pdfDirectory)
    {
        $this->factureRepository = $factureRepository;
        $this->twig = $twig;
        $this->pdfDirectory = $pdfDirectory;
    }

    /**
     * sauvegarde le PDF genere et l affiche dans un nouvel onglet
     * 
     * @param Integer   numero identifiant de la facture selectionnee
     */
    public function getPDF(int $id)
    {
        $facture = $this->factureRepository->find($id);
        //
        //dd($facture);
        //
        // configure Dompdf according to the needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsHtml5ParserEnabled(true);
        $pdfOptions->setDpi(150);

        // instantiate Dompdf with the options
        $dompdf = new Dompdf($pdfOptions);

        // retrieve the HTML generated in the twig file
        $html = $this->twig->render('facture/pdf.html.twig', [
            'title' => "Facture au format PDF",
            'facture' => $facture
        ]);

        // load HTML to Dompdf
        $dompdf->loadHtml($html);
        //
        //dd($dompdf);
        //
        // (Optional) setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // render the HTML as PDF
        $dompdf->render();

        // store PDF Binary Data
        $output = $dompdf->output();

        //  write the file in the public directory set in config/services.yaml
        $publicDirectory = $this->pdfDirectory;
        // concatenate the name with the facture id
        $pdfFilepath =  $publicDirectory . '/facture_' . $facture->getId() . '.pdf';

        // write file to the desired path
        file_put_contents($pdfFilepath, $output);

        // output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
    }
}

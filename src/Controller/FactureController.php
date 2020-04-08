<?php

namespace App\Controller;

// Include Dompdf required namespaces
use Dompdf\Dompdf;
use Dompdf\Options;

use App\Entity\Facture;
use App\Form\FactureType;
use App\Repository\FactureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class FactureController extends AbstractController
{
    private $entityManager;
    private $factureRepository;

    public function __construct(EntityManagerInterface $entityManager, FactureRepository $factureRepository)
    {
        // initialisation de l entityManager
        $this->entityManager = $entityManager;
        // initialisation du Repository Facture
        $this->factureRepository = $factureRepository;
    }

    /**
     * @Route("/", name="app_facture", methods={"GET"})
     */
    public function index(): Response
    {
        // appelle de la methode du Repository qui retourne toutes les Factures
        $factureList = $this->factureRepository->findAll();
        return $this->render('facture/index.html.twig', [
            'title' => 'Liste des factures',
            'factureList' => $factureList,
        ]);
    }

    /**
     * appelle de la page de creation d une facture
     * 
     * @Route("/facture/create", name="app_create_facture", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function createFacture(Request $request)
    {
        // instanciation d un nouvel objet Facture
        $facture = new Facture;
        // recuperation du builder du formulaire associe a l entity Facture
        $factureForm = $this->createForm(FactureType::class, $facture);
        $factureForm->handleRequest($request);
        // formulaire soumis et valide
        if ($factureForm->isSubmitted() && $factureForm->isValid()) {
            // persistence des donnees
            $this->entityManager->persist($facture);
            $this->entityManager->flush();
            // redirige vers la page des factures
            return $this->redirectToRoute('app_facture');
        }
        // sinon on affiche le formulaire
        return $this->render('facture/create.html.twig', [
            'title' => 'Création de la facture',
            'form' => $factureForm->createView()
        ]);
    }

    /**
     * @Route("/facture_pdf/{id}", name="app_facture_pdf", methods={"GET"})
     */
    public function generatePdf(Facture $facture): Response
    {
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
        $html = '<link rel="stylesheet" href="{{ asset(\'build/app.css\') }}">' . $this->renderView('facture/pdf.html.twig', [
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
        $publicDirectory = $this->getParameter('factures_directory');
        // concatenate the name with the facture id
        $pdfFilepath =  $publicDirectory . '/facture_' . $facture->getId() . '.pdf';

        // write file to the desired path
        file_put_contents($pdfFilepath, $output);

        // output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
        exit;
    }
}

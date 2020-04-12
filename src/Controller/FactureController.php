<?php

namespace App\Controller;


use App\Entity\Facture;
use App\Form\FactureType;
use App\Repository\FactureRepository;
use App\Service\Facture\FactureService;
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
    public function createFacture(Request $request): Response
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
     * 
     * @param FactureService    le service associe a la gestion des factures
     * @param Integer   le numero identifiant de la facture selectionnee
     */
    public function generatePdf(FactureService $factureService, int $id)
    {
        $factureService->getPDF($id);
        exit;
    }
}

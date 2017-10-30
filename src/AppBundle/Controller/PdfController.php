<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Vehicule;
use AppBundle\Service\OffreService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Service\OffreServiice;
use AppBundle\Entity\Reservation;


class PdfController extends Controller
{
    /**
     * @Route("/pdf/{id}", name="genererPDF")
     *  @Security("has_role('ROLE_USER')")
     */
    public function pdfAction($id, OffreService $offreService)
    {
        /*$reservation = $offreService->getReservationById($id);
        dump($reservation->getVehicule()->getMarque());die;*/

        $repo = $this->getDoctrine()->getRepository("AppBundle:Vehicule");

        $repoResa = $this->getDoctrine()->getRepository("AppBundle:Reservation");



        $reservation = $repoResa->find($id);

       // dump($offreService->infoReservation($id));die;

        $vehicule = $repo->find($reservation->getVehicule()->getId());
        $marque = $vehicule->getMarque();
        $immat = $vehicule->getplaqueImmatriculation();
        $modele = $vehicule->getModele();
        $couleur = $vehicule->getCouleur();
        $datedebut = $reservation->getDateDebut()->format('d/m/Y');
        $datefin = $reservation->getDateFin()->format('d/m/Y');
        $nom = $this->getUser()->getNom();
        $prenom = $this->getUser()->getPrenom();
        $prixTotal = $offreService->getReservationById($id)->getPrixTotal();
       // dump($prixTotal);die;
        $prix = $offreService->infoReservation($id)['offre']->getprixJournalier();
        $nbjours = $offreService->infoReservation($id)["days"];






//        $pdf->AddPage();
//
//        $pdf->SetFont('Arial','B',16);
//
//        $pdf->SetTextColor(0, 0, 0);
//        //Multicell ordre : Largeur,Hauteur,Message,Bordure(booleen) + savoir que mettre en bordure , alignement du texte,fond coloré ou non
//
//        $pdf->Multicell(190, 10, "facture", 1, 'C', false);
//        $pdf->Multicell(100, 5, $marque, 1, 'R', false);
//        $pdf->Multicell(190, 5, $modele, 1, 'R', false);
//        $pdf->Multicell(190, 5, $couleur, 1, 'R', false);
//        $pdf->Multicell(190, 5, $datedebut, 1, 'R', false);
//        $pdf->Multicell(190, 5, $datefin, 1, 'R', false);

        $pdf = new \FPDF();

        // Création de la page

        $pdf->AddPage();

        //$pdf->Image('logo2.png');


        // On rajoute une police au document
        //$pdf->AddFont();
        $pdf->SetFont('Arial','',14);


        // Saut à la ligne

        $pdf->Ln(20);

        // Récupère la position actuel

        $x = $pdf->GetX();

        $y = $pdf->GetY();

        // Changement de police

        $pdf->SetFont('Arial','BUI', 12);



        // Tableau de facture

        $col1="facture\n";

        $pdf->MultiCell(90, 10, $col1, 'LTB', 1);
        $pdf->SetXY($x + 90, $y);

        $pdf->SetFont('Arial','B', 10);

        $txt = utf8_decode( mb_strtoupper("réservation numéro : ".$id));

        $pdf->MultiCell(99, 10,$txt, 'TRB', 'L');

        $pdf->SetFont('Arial','', 12);

        $pdf->MultiCell(189 , 10,"Nom : ".$nom , 1);

        $pdf->MultiCell(189 , 10,"Prenom : ".$prenom, 1);

        $pdf->ln(15);

        // Tableau récap + info

        $pdf->SetFont('Arial','', 8);

        $pdf->setFillColor(200);

        // traitement pour le code d'évènement

        $x = $pdf->GetX();

        $y = $pdf->GetY();


        $pdf->Cell(30, 10, utf8_decode( mb_strtoupper("Immatriculation\n")), 'LTRB',"",'C',1);

        $x = $pdf->GetX();

        $y = $pdf->GetY();

        $pdf->ln();

        $a = $pdf->GetX();

        $z = $pdf->GetY();

        $pdf->Cell(20, 10,$immat,'BL',"",'C',0);

        // traitement pour le description


        $pdf->SetXY($x , $y);

        $pdf->Cell(50, 10, utf8_decode( mb_strtoupper("Détails\n")), 'TRB',0,'C',1);

        $x = $pdf->GetX();

        $y = $pdf->GetY();

        $pdf->ln();

        $a = $pdf->GetX();

        $z = $pdf->GetY();

       $pdf->Cell(30,10,"","B","0","");


        $pdf->SetXY($a + 30 , $z);


        $pdf->Cell(50, 10,utf8_decode($marque." ".$modele." ".$couleur) , 'B',"","C", '0','');

        // traitement pour PJHT




        $pdf->SetXY($x , $y);

        $pdf->Cell(15, 10, "P.J.HT\n",'TRB',0,'C',1);

        $x = $pdf->GetX();

        $y = $pdf->GetY();

        $pdf->ln();

        $a = $pdf->GetX();

        $z = $pdf->GetY();

        $pdf->SetXY($a +80 , $z);

        $pdf->Cell(15, 10,$prix.' euros','B',"","C", '0','');

        // traitement pour montant

        $pdf->SetXY($x+15 , $y);

        $pdf->Cell(20, 10, "Montant\n",'TRB',0,'C',1);


        $a = $pdf->GetX();

        $z = $pdf->GetY();

        $pdf->SetXY($a + 155 , $z);

        $pdf->Cell(20, 10,',00', 'B', '0','');

        // traitement pour tva

        $pdf->SetXY($x , $y);

        $pdf->Cell(15, 10, strtoupper("Nb jour"), 'TRB', 'C','',1);



        $pdf->ln();

        $a = $pdf->GetX();

        $z = $pdf->GetY();

        $pdf->SetXY($a + 95 , $z);

        $pdf->Cell(15, 10, $nbjours, 'B',"","C",'0');

        $x = $pdf->GetX();

        $y = $pdf->GetY();

        $pdf->SetXY($x,$y);
        $pdf->setFillColor(200);

        $pdf->Cell(20,10, $prixTotal." euros","BR","","C","0");

        // traitement msg retard

        $pdf->ln(20);

        $pdf->SetFont('Arial','B', 7);

        $pdf->SetTextColor(0);

        $pdf->SetDrawColor(130);

        $msgInfo = utf8_decode('Si le vehicule n\'est pas rendu à temps, il vous sera prelevez 150 euros supplementaire par jours de retard .');

        $pdf->MultiCell(0, 10, $msgInfo, 'T', 'C');

        $pdf->ln(20);

        // traitement tableau de fin

        $x = $pdf->GetX();

        $y = $pdf->GetY();

        $pdf->SetXY($x + 120 , $y);

        $pdf->SetFont('Arial','', 12);

        $pdf->SetFillColor(200);

        $pdf->Cell(40, 10, 'Total HT', 'TL', 'C', '',1); //border TL

        $pdf->Cell(30, 10,$prixTotal." euros", 'TLR', '0','R');

        $pdf->ln();

        $x = $pdf->GetX();

        $y = $pdf->GetY();

        $TVA = 19.6;

        $TotalTVA = $prixTotal*($TVA/100);

        $prixTTC = $prixTotal+($TotalTVA);



        $pdf->SetXY($x + 120 , $y);

        $pdf->Cell(40, 10, 'Total TVA', 'L', 'C','',1);

        $pdf->Cell(30, 10,$TotalTVA." euros", 'LR', '0','R');

        $pdf->ln();

        $x = $pdf->GetX();

        $y = $pdf->GetY();

        $pdf->SetXY($x + 120 , $y);

        $pdf->Cell(40, 10, 'Total TTC', 'L', 'C','',1);

        $pdf->Cell(30, 10,$prixTTC." euros", 'LR', '0','R');

        $pdf->ln();

        $x = $pdf->GetX();

        $y = $pdf->GetY();

        $pdf->SetXY($x + 120 , $y);

        $retard = 0;

        $pdf->Cell(40, 10, 'Retard', 'L', 'C','',1);

        $pdf->Cell(30, 10, $retard.' euros ', 'LR', '0','R');

        $pdf->ln();

        $x = $pdf->GetX();

        $y = $pdf->GetY();

        $prixTotal= $prixTTC + $retard;

        $pdf->SetXY($x + 120 , $y);

        $pdf->Cell(40, 10, '', 'LB', 'C','',1);

        $pdf->Cell(30, 10, $prixTotal." euros ", '1', '0','R');

        $pdf->ln(35);

        //traitement siret

        $pdf->SetFont('Arial','B', 7);

        $pdf->SetFillColor(0);

        $siret = utf8_decode( mb_strtoupper("Emotion"));

        $pdf->MultiCell(0, 4, $siret, 'T', 'R');

        $nomfact = '../web/uploads/facture/'.$nom."_".$prenom."_"."facture".$id.".pdf";


        $pdf->Output('F',$nomfact);



        return new Response($pdf->Output(), 200, array('Content-Type' => 'application/pdf'));
    }
}
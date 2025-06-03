<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TCPDF;

class ReportController extends AbstractController
{
    #[Route('/report/pdf', name: 'generate_pdf')]
    public function generatePdf(Connection $connection): Response
    {
        // Consulta SQL para obtener los estudiantes
        $sql = "SELECT codigo, primer_apellido, segundo_apellido, primer_nombre, segundo_nombre, 
                       genero, fecha_nacimiento, email, celular, tipo_documento, no_documento 
                FROM estudiante 
                ORDER BY primer_apellido, primer_nombre";
        $estudiantes = $connection->executeQuery($sql)->fetchAllAssociative();

        // Crear nueva instancia de TCPDF
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        
        // Establecer información del documento
        $pdf->SetCreator('Sistema Académico');
        $pdf->SetAuthor('Administrador');
        $pdf->SetTitle('Reporte de Estudiantes ');
        
        // Establecer márgenes
        $pdf->SetMargins(15, 15, 15);
        
        // Agregar una página
        $pdf->AddPage();
        
        // Establecer fuente
        $pdf->SetFont('helvetica', 'B', 14);
        
        // Agregar título
        $pdf->Cell(0, 10, 'Estudiantes desde xampp', 0, 1, 'C');
        $pdf->Ln(5);
        
        // Configurar la tabla
        $pdf->SetFont('helvetica', 'B', 9);
        
        // Encabezados de la tabla
        $pdf->Cell(25, 7, 'Código', 1, 0, 'C');
        $pdf->Cell(35, 7, 'Apellidos', 1, 0, 'C');
        $pdf->Cell(35, 7, 'Nombres', 1, 0, 'C');
        $pdf->Cell(15, 7, 'Género', 1, 0, 'C');
        $pdf->Cell(25, 7, 'F. Nacimiento', 1, 0, 'C');
        $pdf->Cell(50, 7, 'Email', 1, 0, 'C');
        $pdf->Cell(25, 7, 'Celular', 1, 0, 'C');
        $pdf->Cell(25, 7, 'Documento', 1, 1, 'C');
        
        // Datos de los estudiantes
        $pdf->SetFont('helvetica', '', 8);
        
        foreach ($estudiantes as $estudiante) {
            $pdf->Cell(25, 6, $estudiante['codigo'], 1, 0, 'C');
            $pdf->Cell(35, 6, $estudiante['primer_apellido'] . ' ' . $estudiante['segundo_apellido'], 1, 0, 'L');
            $pdf->Cell(35, 6, $estudiante['primer_nombre'] . ' ' . $estudiante['segundo_nombre'], 1, 0, 'L');
            $pdf->Cell(15, 6, $estudiante['genero'], 1, 0, 'C');
            $pdf->Cell(25, 6, date('d/m/Y', strtotime($estudiante['fecha_nacimiento'])), 1, 0, 'C');
            $pdf->Cell(50, 6, $estudiante['email'], 1, 0, 'L');
            $pdf->Cell(25, 6, $estudiante['celular'], 1, 0, 'C');
            $pdf->Cell(25, 6, $estudiante['no_documento'], 1, 1, 'C');
        }
        
        // Generar el PDF
        return new Response(
            $pdf->Output('reporte_estudiantes.pdf', 'I'),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="reporte_estudiantes.pdf"'
            ]
        );
    }
} 
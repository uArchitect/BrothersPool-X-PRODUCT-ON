<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use Dompdf\Dompdf;
use Dompdf\Options;

class ExportController extends Controller
{


  
    
    public function expensesPDF(Request $request, $ids) {
        $expenseInformation = DB::table('expenses')
            ->select('expenses.*')
            ->whereIn('expenses.id', explode(',', $ids))
            ->get();
            
        
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans'); // Türkçe karakterler için uygun font seçimi
        $pdf = new Dompdf($options);
        
        $pdf->loadHtml(view('pdf.expenses', compact('expenseInformation'))->render(), 'UTF-8');
        $pdf->setPaper('A4', 'landscape');
        $pdf->render();
        
        return $pdf->stream('gider_raporu.pdf');
    }
    
    
}

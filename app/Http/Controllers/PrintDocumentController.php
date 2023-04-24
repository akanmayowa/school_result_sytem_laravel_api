<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PrintDocumentService;
use App\Http\Requests\PrintDocumentRequest;
use App\Http\Requests\PrintOralMarkSheetDocumentRequest;
use App\Http\Requests\PrintZonalMarkSheetDocumentRequest;
use App\Http\Requests\PrintResearchProjectDocumentRequest;
use App\Http\Requests\PrintCourseRegStatisticsDocumentRequest;

class PrintDocumentController extends Controller
{

    public function __construct(public PrintDocumentService $printDocumentService){
        $this->printDocumentService = $printDocumentService;
    }

    public function candidateIndexing(PrintDocumentRequest $request)
    {
        return $this->printDocumentService->candidateIndexing($request->validated());
    }

    public function candidateIndexing_II()
    {
        return $this->printDocumentService->candidateIndexing_II();
    }

    public function oralMarkSheet()
    {
        return $this->printDocumentService->oralMarkSheet();
    }

    public function researchProject()
    {
        return $this->printDocumentService->researchProject();
    }

    public function researchProjecAA2()
    {
        return $this->printDocumentService->researchProjecAA2();
    }

    public function zonalMarksheet()
    {
        return $this->printDocumentService->zonalMarksheet();
    }

    public function courseRegistrationStatistics()
    {
        return $this->printDocumentService->courseRegistrationStatistics();
    }
}

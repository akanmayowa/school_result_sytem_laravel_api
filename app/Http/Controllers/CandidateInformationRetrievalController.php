<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CandidateInformationRetrievalServices;


class CandidateInformationRetrievalController extends Controller
{
    public ?CandidateInformationRetrievalServices $candidate_information_retrieval_services = null;

    public function __construct(CandidateInformationRetrievalServices $candidate_information_retrieval_services)
    {
//        ini_set('memory_limit', '700M');
        $this->candidate_information_retrieval_services = $candidate_information_retrieval_services;
    }

    public function retrievingFresherCandidateInformation()
    {
        return $this->candidate_information_retrieval_services->retrievalOfACandidateInformationAsAFresher();
    }

    public function retrievingFresherCandidateInformation_II()
    {
        return $this->candidate_information_retrieval_services->retrievalOfACandidateInformationAsAFresher_II();
    }

    public function retrievingIndexedCandidateInformation()
    {
        return $this->candidate_information_retrieval_services->retrievalOfAnIndexedCandidateInformation();
    }

    public function retrievingIndexedCandidateStatisticsInformation()
    {
        return $this->candidate_information_retrieval_services->retrievalOfAnIndexedCandidateStatisticsInformation();
    }

    public function retrievingIndexedCandidateInformation_II()
    {
        return $this->candidate_information_retrieval_services->retrievalOfAnIndexedCandidateInformation_II();
    }

    public function retrievingResistingCandidateInformation()
    {
        return $this->candidate_information_retrieval_services->retrievalOfAResitingCandidateInformation();
    }

    public function retrievingResistingCandidateInformation_II()
    {
        return $this->candidate_information_retrieval_services->retrievalOfAResitingCandidateInformation_II();
    }

    public function retrievingCandidateProjectOfStudy()
    {
        return $this->candidate_information_retrieval_services->retrievalOfACandidateProjectStudiedInformation();
    }

    public function retrievingCandidateSchoolPerfomance()
    {
        return $this->candidate_information_retrieval_services->retrievealOfACandidateSchoolPerformanceInformation();
    }


    public function retrievingCandidateResultAnalysis()
    {
        set_time_limit(0);

        return $this->candidate_information_retrieval_services->candidateResultAnalysis();
    }


    public function retrievingCandidateResultAnalysis_II()
    {
        return $this->candidate_information_retrieval_services->candidateResultAnalysis_II();
    }



    public function retrievealOfAllSchoolExamPerformance()
    {
        return $this->candidate_information_retrieval_services->retrievealOfAllSchoolExamPerformanceInformation();
    }

    public function retrievalOfCourseIndexStatistics()
    {
        return $this->candidate_information_retrieval_services->retrievalOfCourseIndexStatisticsInformation();
    }

    public function retrievalOfPracticalMarksheet()
    {
        return $this->candidate_information_retrieval_services->retrievalOfPracticalMarksheetInformation();
    }

    public function retrievalOfOralMarksheet()
    {
        return $this->candidate_information_retrieval_services->retrievalOfOralMarksheetInformation();
    }

    public function retrievalOfScoresResultBasedOnPassAndfailStatistic()
    {
    return $this->candidate_information_retrieval_services->RetrievalOfScoresResultBasedOnPassAndfailStatisticInformation();
    }



    public function retrievealOfApplicationForIndexing()
    {
        return $this->candidate_information_retrieval_services->retrievealOfApplicationForIndexingInformation();
    }



}

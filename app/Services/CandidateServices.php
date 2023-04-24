<?php

namespace App\Services;

use App\Helpers\GeneralLogs;
use App\Models\TrainingSchool;
use Carbon\Carbon;
use App\Traits\ResponsesTrait;
use App\Repositories\CandidateRepository;
use App\Models\ScoreMarkerTwo;
use App\Models\CourseHeader;
use App\Models\CandidateIndexing;
use App\Models\Candidate;
use App\Http\Resources\CandidateResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class CandidateServices
{

    use ResponsesTrait;

    public ?CandidateRepository $candidateRepository = null;

    public function __construct(CandidateRepository $candidateRepository)
    {
        $this->candidateRepository = $candidateRepository;
        $this->candidate = new Candidate();
        $this->courseHeader = new CourseHeader();
        $this->candidateIndexing = new CandidateIndexing();
    }

    public function createCandidate(): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'course_header' => 'required|string',
            'exam_id' => 'required|integer',
            'candidate_index' => 'required|exists:candidate_indexings|string',
        ]);
        if($validator->fails()){ return $this->errorResponse( $validator->errors()); }
        $exam_id_array = str_split(request()->exam_id, 2);
        $query = $this->candidateIndexing->where('candidate_index', request()->candidate_index)
                                         ->where('course_header', request()->course_header);

        if (!$query->exists()) {
            $registered_candidate_query = $this->candidate->where('candidate_index',request()->candidate_index);
            if (request()->course_header === 'AA2' && !$registered_candidate_query->exists() || $registered_candidate_query->count() === 1 && $registered_candidate_query->first()->reg_status === 'pending') {
                $this->candidateIndexing->where('candidate_index', request()->candidate_index)->update(['course_header' => 'AA2']);
            } else {
                return response()->json(['error' => 'Candidate Does not exist for selected course header']);
            }
        }

        $verified = $this->candidateIndexing->where('candidate_index', request()->candidate_index)
            ->where('course_header', request()->course_header)->first();

        if ($verified->unverified == 1)
        {
                return response()->json(['error' => 'This Candidate Hasnt Been Verified']);
        }

        $candidate = $this->candidateIndexing->where('candidate_index', request()->candidate_index)->first();
        $candidate->exam_id = request()->exam_id;
        $candidate->exam_date = '20' . $exam_id_array[1] . '-' . $exam_id_array[0] . '-01';
        $candidate->month_yr_reg = request()->exam_id;
        $candidate->registered_at = date('Y-m-d H:i:s');
        $candidate->validate = 'yes';
        $candidate->save();

        $registered_query = $this->candidate->where('candidate_index', request()->candidate_index);
        if ($registered_query->exists()) {
            $registered_query->first()->update([
                'school_code' => $candidate->school_code,
                'candidate_index' => request()->candidate_index,
                'course_header' => request()->course_header,
                'exam_id' => request()->exam_id,
                'reg_status' => 'approved'
            ]);
        } else {
            $this->candidate->create([
                'school_code' => $candidate->school_code,
                'candidate_index' => $candidate->candidate_index,
                'course_header' => $candidate->course_header,
                'exam_id' => request()->exam_id,
                'reg_status' => 'approved'
            ]);
        }

        $data = [
            'registered_candidate' => $registered_query->get(),
            'candidate_index' => $candidate
        ];

        return $this->successResponse($data,'Candidate Registered Successfully');
    }

    public function updateCandidate($candidateId, $data): \Illuminate\Http\JsonResponse
    {
        if (!$indexed_candidate = $this->candidateIndexing->where('id', $candidateId)->first()) {
            return $this->errorResponse('Candidate index not found', 404);
        }
        if (!$course_month = $this->get_course_exam_month($data['course_header'])) {
            return $this->errorResponse('Course header not found', 404);
        }
        $date = Carbon::parse($data['exam_year']);
        $examYearMonth = $date->format('y') . $date->format('m'); // 0222
        $data['exam_id'] = $examYearMonth;
        $data['exam_date'] = $date->toDateTimeString();
        $candidate_index = $indexed_candidate->candidate_index;
        $candidateIndexing = $this->candidateIndexing->whereCandidateIndex($candidate_index)->first();

        if (!$candidateIndexing) {
            return $this->errorResponse('Candidate index not found');
        }
        if ($candidateIndexing->course_header === 'B5') {
            $data['indexed'] = 1;
            $data['visible'] = 1;
            $data['unverified'] = 0;
        }
        if (isset($data['photo']) && !empty($data['photo'])) {
            $data['photo'] = uploadImage($data['photo'], CandidateIndexing::photo, $candidate_index);
        }
        if (isset($data['birth_certificate_upload']) && !empty($data['birth_certificate_upload'])) {
            $data['birth_certificate_upload'] = uploadImage($data['birth_certificate_upload'], CandidateIndexing::birth_certificate, $candidate_index);
        }
        if (isset($data['marriage_certificate_upload']) && !empty($data['marriage_certificate_upload'])) {
            $data['marriage_certificate_upload'] = uploadImage($data['marriage_certificate_upload'], CandidateIndexing::marriage_certificate, $candidate_index);
        }
        $newUpdate = $candidateIndexing->update($data);
        $this->candidate->where('candidate_index', $data['candidate_index'])->update(['candidate_index' => $data['candidate_index']]);
         //log should be here
        if ($indexed_candidate->course_header !== 'B5') {
            self::verify($candidateIndexing);
        }
        return $this->successResponse((new CandidateIndexing())->first(), 'Candidate information updated');

    }

    public static function verify($candidate)
    {
        // $candidates = $this->candidateIndexing->whereCandidateIndex($candidate_index)->get();
        $valid = false;
        // foreach ($candidates as $candidate) {
        $english = $candidate->english;
        $biology = $candidate->biology;
        $chemistry = $candidate->chemistry;
        $health_science = $candidate->health_science;
        $integrated_science = $candidate->integrated_science;

        $mathematics = $candidate->mathematics;
        $geography = $candidate->geography;
        $agric = $candidate->agric;
        $economics = $candidate->economics;
        $physics = $candidate->physics;
        $accounting = $candidate->accounting;
        $commerce = $candidate->commerce;
        $government = $candidate->government;
        $literature = $candidate->literature;
        $yoruba = $candidate->yoruba;
        $technical_drawing = $candidate->technical_drawing;
        $general_science = $candidate->general_science;
        $food_and_nutrition = $candidate->food_and_nutrition;
        $igbo = $candidate->igbo;
        $hausa = $candidate->hausa;
        $history = $candidate->history;
        $religious_knowledge = $candidate->religious_knowledge;

        $valid = false;
        $reason = '';

        switch ($candidate->courseHeader['cadre']) {
            case 'PHN':
                $has_english = false;
                $has_maths = false;
                $has_biology = false;
                $has_physics = false;
                $has_chemistry = false;

                if (str_contains($english, 'A') || str_contains($english, 'B') || str_contains($english, 'C')) {
                    $has_english = true;
                }

                if (str_contains($mathematics, 'A') || str_contains($mathematics, 'B') || str_contains($mathematics, 'C')) {
                    $has_maths = true;
                }

                if (str_contains($biology, 'A') || str_contains($biology, 'B') || str_contains($biology, 'C')) {
                    $has_biology = true;
                }

                if (str_contains($chemistry, 'A') || str_contains($chemistry, 'B') || str_contains($chemistry, 'C')) {
                    $has_chemistry = true;
                }

                if (str_contains($physics, 'A') || str_contains($physics, 'B') || str_contains($physics, 'C')) {
                    $has_physics = true;
                }

                $valid = $has_english && $has_maths && $has_biology && $has_chemistry && $has_physics;

                $reason = [];
                $reason_items = [];
                if (!$valid) {
                    $reason_items[] = !$has_english ? 'english' : '';
                    $reason_items[] = !$has_maths ? 'mathematics' : '';
                    $reason_items[] = !$has_biology ? 'biology' : '';
                    $reason_items[] = !$has_chemistry ? 'chemistry' : '';
                    $reason_items[] = !$has_physics ? 'physics' : '';
                }

                foreach ($reason_items as $reasons) {
                    if (!empty($reasons)) {
                        $reason[] = $reasons;
                    }
                }

                $reason = join(', ', $reason) ? 'No credit in ' . join(', ', $reason) : '';
                break;

            case 'ND':
                $has_english = false;
                $has_maths = false;
                $has_biology_or_health_science = false;
                $has_chemistry = false;
                $has_others = false;

                if (str_contains($english, 'A') || str_contains($english, 'B') || str_contains($english, 'C')) {
                    $has_english = true;
                }

                if (str_contains($mathematics, 'A') || str_contains($mathematics, 'B') || str_contains($mathematics, 'C')) {
                    $has_maths = true;
                }

                if (str_contains($biology, 'A') || str_contains($biology, 'B') || str_contains($biology, 'C') || str_contains($health_science, 'A') || str_contains($health_science, 'B') || str_contains($health_science, 'C')) {
                    $has_biology_or_health_science = true;
                }

                if (str_contains($chemistry, 'A') || str_contains($chemistry, 'B') || str_contains($chemistry, 'C')) {
                    $has_chemistry = true;
                }

                if (str_contains($geography, 'A') || str_contains($geography, 'B') || str_contains($geography, 'C') || str_contains($economics, 'A') || str_contains($economics, 'B') || str_contains($economics, 'C') || str_contains($food_and_nutrition, 'A') || str_contains($food_and_nutrition, 'B') || str_contains($food_and_nutrition, 'C') || str_contains($physics, 'A') || str_contains($physics, 'B') || str_contains($physics, 'C') || str_contains($technical_drawing, 'A') || str_contains($technical_drawing, 'B') || str_contains($technical_drawing, 'C')) {
                    $has_others = true;
                }
                $valid = $has_english && $has_maths && $has_biology_or_health_science && $has_chemistry && $has_others;

                $reason = [];
                $reason_items = [];
                if (!$valid) {
                    $reason_items[] = !$has_english ? 'english' : '';
                    $reason_items[] = !$has_maths ? 'mathematics' : '';
                    $reason_items[] = !$has_biology_or_health_science ? '1 credit in  biology or health science' : '';
                    $reason_items[] = !$has_chemistry ? 'chemistry' : '';
                    $reason_items[] = !$has_others ? '1 credit in geography, economics, food and nutrition, physics or technical drawing' : '';
                }

                foreach ($reason_items as $reasons) {
                    if (!empty($reasons)) {
                        $reason[] = $reasons;
                    }
                }

                $reason = join(', ', $reason) ? 'Does not have ' . join(', ', $reason) : '';

                break;

            case 'EHA': //  A3
                $req_array = [$english ?? '0', $biology ?? '0', $health_science ?? '0', $chemistry ?? '0', $food_and_nutrition ?? '0', $physics ?? '0', $general_science ?? '0', $integrated_science ?? '0'];
                $req_array_count = array_count_values($req_array);

                $a1_count = $req_array_count['A1'] ?? 0;
                $a2_count = $req_array_count['A2'] ?? 0;
                $a3_count = $req_array_count['A3'] ?? 0;
                $b2_count = $req_array_count['B2'] ?? 0;
                $b3_count = $req_array_count['B3'] ?? 0;
                $b4_count = $req_array_count['B4'] ?? 0;
                $b5_count = $req_array_count['B5'] ?? 0;
                $b6_count = $req_array_count['B6'] ?? 0;
                $c4_count = $req_array_count['C4'] ?? 0;
                $c5_count = $req_array_count['C5'] ?? 0;
                $c6_count = $req_array_count['C6'] ?? 0;

                $d7_count = $req_array_count['D7'] ?? 0;
                $e8_count = $req_array_count['E8'] ?? 0;

                $total_credits = $a1_count + $a2_count + $a3_count + $b2_count + $b3_count + $b4_count + $b5_count + $b6_count + $c4_count + $c5_count + $c6_count;
                $total_passes = $total_credits + $d7_count + $e8_count;


                $others_array = array($mathematics ?? '0', $geography ?? '0', $agric ?? '0', $economics ?? '0', $accounting ?? '0', $commerce ?? '0', $government ?? '0', $literature ?? '0', $yoruba ?? '0', $technical_drawing ?? '0', $igbo ?? '0', $hausa ?? '0', $history ?? '0', $religious_knowledge ?? '0');

                $count = array_count_values($others_array);
                $a1_count = $count['A1'] ?? 0;
                $a2_count = $count['A2'] ?? 0;
                $a3_count = $count['A3'] ?? 0;
                $b2_count = $count['B2'] ?? 0;
                $b3_count = $count['B3'] ?? 0;
                $b4_count = $count['B4'] ?? 0;
                $b5_count = $count['B5'] ?? 0;
                $b6_count = $count['B6'] ?? 0;
                $c4_count = $count['C4'] ?? 0;
                $c5_count = $count['C5'] ?? 0;
                $c6_count = $count['C6'] ?? 0;
                $d7_count = $count['D7'] ?? 0;
                $e8_count = $count['E8'] ?? 0;

                $total_pass_count = $a1_count + $a2_count + $a3_count + $b2_count + $b3_count + $b4_count + $b5_count + $b6_count + $c4_count + $c5_count + $c6_count + $d7_count + $e8_count;

                $has_others = false;

                $has_four_passes = false;
                $has_one_requried_pass = false;


                if ($total_passes > 3) {
                    $has_four_passes = true;
                    $has_one_requried_pass = true;
                } else if ($total_passes > 2) {
                    $has_one_requried_pass = true;
                    if ($total_pass_count > 0) {
                        $has_four_passes = true;
                    }
                } else if ($total_passes > 1) {
                    $has_one_requried_pass = true;
                    if ($total_pass_count > 1) {
                        $has_four_passes = true;
                    }
                } else if ($total_passes > 0) {
                    $has_one_requried_pass = true;
                    if ($total_pass_count > 2) {
                        $has_four_passes = true;
                    }
                } else if ($total_passes == 0) {
                    $has_one_requried_pass = false;
                    if ($total_pass_count > 2) {
                        $has_four_passes = true;
                    }
                }

                $valid = $has_one_requried_pass && $has_four_passes;
                // $valid = $has_one_requried_credit && $has_others;

                $reason = [];
                $reason_items = [];
                if (!$valid && $has_four_passes) {
                    $reason_items[] = !$has_one_requried_pass ? 'a pass in one science subject' : '';
                } else {
                    $reason_items[] = !$has_one_requried_pass ? 'a pass in one science subject' : '';
                    $reason_items[] = !$has_others ? '3 passes in other subjects' : '';
                }
                // if (!$valid) {
                //     $reason_items[] = !$has_one_requried_credit ? 'a credit in science subjects' : '';
                //     $reason_items[] = !$has_others ? '3 passes in other subjects' : '';
                // }

                foreach ($reason_items as $reasons) {
                    if (!empty($reasons)) {
                        $reason[] = $reasons;
                    }
                }

                $reason = join(', ', $reason) ? 'Does not have ' . join(' and ', $reason) : '';
                break;

            case 'HEP': //  A7
                $others_array = [$geography ?? '0', $agric ?? '0', $economics ?? '0', $physics ?? '0', $accounting ?? '0', $commerce ?? '0', $government ?? '0', $literature ?? '0', $yoruba ?? '0', $technical_drawing ?? '0', $general_science ?? '0', $food_and_nutrition ?? '0', $igbo ?? '0', $hausa ?? '0', $history ?? '0', $religious_knowledge ?? '0', $chemistry ?? '0'];

                $count = array_count_values($others_array);
                $a1_count = $count['A1'] ?? 0;
                $a2_count = $count['A2'] ?? 0;
                $a3_count = $count['A3'] ?? 0;
                $b2_count = $count['B2'] ?? 0;
                $b3_count = $count['B3'] ?? 0;
                $b4_count = $count['B4'] ?? 0;
                $b5_count = $count['B5'] ?? 0;
                $b6_count = $count['B6'] ?? 0;
                $c4_count = $count['C4'] ?? 0;
                $c5_count = $count['C5'] ?? 0;
                $c6_count = $count['C6'] ?? 0;

                $total_pass_count = 0;

                if ($a1_count > 0)
                    $total_pass_count += 1;
                if ($a2_count > 0)
                    $total_pass_count += 1;
                if ($a3_count > 0)
                    $total_pass_count += 1;
                if ($b2_count > 0)
                    $total_pass_count += 1;
                if ($b3_count > 0)
                    $total_pass_count += 1;
                if ($b4_count > 0)
                    $total_pass_count += 1;
                if ($b5_count > 0)
                    $total_pass_count += 1;
                if ($b6_count > 0)
                    $total_pass_count += 1;
                if ($c4_count > 0)
                    $total_pass_count += 1;
                if ($c5_count > 0)
                    $total_pass_count += 1;
                if ($c6_count > 0)
                    $total_pass_count += 1;
                $has_english = false;
                $has_maths = false;
                $has_biology_health_science = false;
                $has_others = $total_pass_count > 0;

                if (str_contains($english, 'A') || str_contains($english, 'B') || str_contains($english, 'C')) {
                    $has_english = true;
                }
                if (str_contains($mathematics, 'A') || str_contains($mathematics, 'B') || str_contains($mathematics, 'C')) {
                    $has_maths = true;
                }

                if (str_contains($biology, 'A') || str_contains($biology, 'B') || str_contains($biology, 'C') || str_contains($health_science, 'A') || str_contains($health_science, 'B') || str_contains($health_science, 'C')) {
                    $has_biology_health_science = true;
                }

                if ($has_english && $has_maths && ((str_contains($biology, 'A') || str_contains($biology, 'B') || str_contains($biology, 'C')) && (str_contains($health_science, 'A') || str_contains($health_science, 'B') || str_contains($health_science, 'C')))) {
                    $valid = true;
                } else {
                    $valid = $has_english && $has_maths && $has_biology_health_science && $has_others;
                }

                $reason = [];
                $reason_items = [];
                if (!$valid) {
                    $reason_items[] = !$has_english ? 'English' : '';
                    $reason_items[] = !$has_maths ? 'Mathematics' : '';
                    $reason_items[] = !$has_biology_health_science ? 'Biology or Health science' : '';
                    $reason_items[] = !$has_others ? 'other subject' : '';
                }

                foreach ($reason_items as $reasons) {
                    if (!empty($reasons)) {
                        $reason[] = $reasons;
                    }
                }

                $reason = join(', ', $reason) ? 'Does not have credit in ' . join(', ', $reason) : '';

                break;

            case 'EVT': //  A2 2 credits from Eng, biology/health science, 2 credits from any of Geography, Chemistry, Physics, Agric, Mathematics, 1 pass from others

//                    $valid = $has_three_requried_credits && $has_two_passes_in_others;
                $others_array = array($mathematics ?? '0', $geography ?? '0', $physics ?? '0', $chemistry ?? '0', $agric ?? '0');
                $other_subjects = array($mathematics ?? '0', $geography ?? '0', $physics ?? '0', $chemistry ?? '0', $agric ?? '0', $economics ?? '0', $accounting ?? '0', $commerce ?? '0', $government ?? '0', $literature ?? '0', $yoruba ?? '0', $technical_drawing ?? '0', $general_science ?? '0', $food_and_nutrition ?? '0', $igbo ?? '0', $hausa ?? '0', $history ?? '0', $religious_knowledge ?? '0');
                $count = array_count_values($others_array);
                $a1_count = $count['A1'] ?? 0;
                $a2_count = $count['A2'] ?? 0;
                $a3_count = $count['A3'] ?? 0;
                $b2_count = $count['B2'] ?? 0;
                $b3_count = $count['B3'] ?? 0;
                $b4_count = $count['B4'] ?? 0;
                $b5_count = $count['B5'] ?? 0;
                $b6_count = $count['B6'] ?? 0;
                $c4_count = $count['C4'] ?? 0;
                $c5_count = $count['C5'] ?? 0;
                $c6_count = $count['C6'] ?? 0;

                $total_pass_count = 0;

                if ($a1_count > 0)
                    $total_pass_count += 1;
                if ($a2_count > 0)
                    $total_pass_count += 1;
                if ($a3_count > 0)
                    $total_pass_count += 1;
                if ($b2_count > 0)
                    $total_pass_count += 1;
                if ($b3_count > 0)
                    $total_pass_count += 1;
                if ($b4_count > 0)
                    $total_pass_count += 1;
                if ($b5_count > 0)
                    $total_pass_count += 1;
                if ($b6_count > 0)
                    $total_pass_count += 1;
                if ($c4_count > 0)
                    $total_pass_count += 1;
                if ($c5_count > 0)
                    $total_pass_count += 1;
                if ($c6_count > 0)
                    $total_pass_count += 1;

                if ($a1_count > 1)
                    $total_pass_count += 1;
                if ($a2_count > 1)
                    $total_pass_count += 1;
                if ($a3_count > 1)
                    $total_pass_count += 1;
                if ($b2_count > 1)
                    $total_pass_count += 1;
                if ($b3_count > 1)
                    $total_pass_count += 1;
                if ($b4_count > 1)
                    $total_pass_count += 1;
                if ($b5_count > 1)
                    $total_pass_count += 1;
                if ($b6_count > 1)
                    $total_pass_count += 1;
                if ($c4_count > 1)
                    $total_pass_count += 1;
                if ($c5_count > 1)
                    $total_pass_count += 1;
                if ($c6_count > 1)
                    $total_pass_count += 1;

                $count_2 = array_count_values($other_subjects);
                $a1_count_2 = $count_2['A1'] ?? 0;
                $a2_count_2 = $count_2['A2'] ?? 0;
                $a3_count_2 = $count_2['A3'] ?? 0;
                $b2_count_2 = $count_2['B2'] ?? 0;
                $b3_count_2 = $count_2['B3'] ?? 0;
                $b4_count_2 = $count_2['B4'] ?? 0;
                $b5_count_2 = $count_2['B5'] ?? 0;
                $b6_count_2 = $count_2['B6'] ?? 0;
                $c4_count_2 = $count_2['C4'] ?? 0;
                $c5_count_2 = $count_2['C5'] ?? 0;
                $c6_count_2 = $count_2['C6'] ?? 0;
                $d7_count_2 = $count_2['D7'] ?? 0;
                $e8_count_2 = $count_2['E8'] ?? 0;

                $total_pass_count_2 = 0;

                if ($a1_count_2 > 0)
                    $total_pass_count_2 += 1;
                if ($a2_count_2 > 0)
                    $total_pass_count_2 += 1;
                if ($a3_count_2 > 0)
                    $total_pass_count_2 += 1;
                if ($b2_count_2 > 0)
                    $total_pass_count_2 += 1;
                if ($b3_count_2 > 0)
                    $total_pass_count_2 += 1;
                if ($b4_count_2 > 0)
                    $total_pass_count_2 += 1;
                if ($b5_count_2 > 0)
                    $total_pass_count_2 += 1;
                if ($b6_count_2 > 0)
                    $total_pass_count_2 += 1;
                if ($c4_count_2 > 0)
                    $total_pass_count_2 += 1;
                if ($c5_count_2 > 0)
                    $total_pass_count_2 += 1;
                if ($c6_count_2 > 0)
                    $total_pass_count_2 += 1;
                if ($d7_count_2 > 0)
                    $total_pass_count_2 += 1;
                if ($e8_count_2 > 0)
                    $total_pass_count_2 += 1;

                $has_english = false;
                $has_biology_health_science = false;
                $has_two_science_subjects = $total_pass_count > 1;
                $has_others_subject = $total_pass_count_2 > 0;

                if (str_contains($english, 'A') || str_contains($english, 'B') || str_contains($english, 'C')) {
                    $has_english = true;
                }

                if (str_contains($biology, 'A') || str_contains($biology, 'B') || str_contains($biology, 'C') || str_contains($health_science, 'A') || str_contains($health_science, 'B') || str_contains($health_science, 'C')) {
                    $has_biology_health_science = true;
                }

                $valid = $has_english && $has_biology_health_science && $has_two_science_subjects && $has_others_subject;

                $reason = [];
                $reason_items = [];

                if (!$valid) {
                    $reason_items[] = !$has_english || !$has_biology_health_science ? '2 required credits in English and Biology/Health Science' : '';
                    $reason_items[] = !$has_two_science_subjects ? '2 required credits in Geography, Chemistry, Physics, Agric, Technical Drawing, Mathematics' : '';
                    $reason_items[] = !$has_others_subject ? '1 pass in Economics, Integrated Science, Accounting, Commerce, Government, Literature, Yoruba, General science, Food and Nutrition, Igbo, Hausa, History or Religious Knowledge' : '';
                }

                foreach ($reason_items as $reasons) {
                    if (!empty($reasons)) {
                        $reason[] = $reasons;
                    }
                }

                $reason = join(', ', $reason) ? 'Does not have ' . join(' and ', $reason) : '';
                break;
        }
        if ($valid) {
            $candidate->update([
                'indexed' => 1,
                'visible' => 1,
                'unverified' => 0,
                'reason' => $reason
            ]);
        } else {
            $candidate->update([
                'unverified' => 1,
                'visible' => 1,
                'reason' => $reason
            ]);
        }
        // }
        return $valid ? 'valid' : 'invalid';

    }

    public function get_course_exam_month($course_header)
    {
        if (!$courseHeader = $this->courseHeader->where('header_key', $course_header)->first()) {
            return null;
        }
        return $courseHeader->month;
    }

    public function fetchAllCandidate()
    {
        if(request()->school_code AND request()->course_header AND request()->exam_year AND !empty(request()->school_code) AND  !empty(request()->exam_year) AND !empty(request()->course_header))
        {
            $exam_year = str_split(request()->exam_year)[2] . str_split(request()->exam_year)[3];
            $candidate = (new Candidate())::with('candidateIndexForCandidate:id,first_name,last_name,middle_name,school_code,candidate_index')
                ->where('exam_id','LIKE', '%' . $exam_year)
                    ->where('course_header', request()->course_header)
                    ->where('school_code', request()->school_code)
                ->groupBy('candidate_index')
                ->orderBy('candidate_index','asc')
                ->get();
            return $this->successResponse($candidate, "Retrieving Candidate Information");
        }
        //working
        if(request()->exam_year AND request()->school_code AND !empty(request()->exam_year) AND !empty(request()->school_code))
        {
            $exam_year = str_split(request()->exam_year)[2] . str_split(request()->exam_year)[3];
            $candidate =   (new Candidate())::with('candidateIndexForCandidate:id,first_name,last_name,middle_name,school_code,candidate_index')
                ->where('exam_id','LIKE', '%' . $exam_year . '%')
                    ->where('school_code', request()->school_code)
                ->orderByDesc('id')
                ->groupBy('candidate_index')
                ->orderBy('candidate_index','asc')
                ->get();
            return $this->successResponse($candidate, "Retrieving Candidate information");
        }
        /// working fine
        if(request()->exam_year AND request()->course_header AND !empty(request()->exam_year) AND !empty(request()->course_header))
        {
            $exam_year = str_split(request()->exam_year)[2] . str_split(request()->exam_year)[3];
            $candidate = (new Candidate())::with('candidateIndexForCandidate:id,first_name,last_name,middle_name,school_code,candidate_index')
                ->where('exam_id','LIKE', '%' . $exam_year)
                    ->where('course_header', request()->course_header)
                ->orderByDesc('id')
                ->groupBy('candidate_index')
                ->orderBy('candidate_index','asc')
                ->get();
            return $this->successResponse($candidate, "Retrieving Candidate information");
        }
        ///working
        if(request()->school_code && request()->course_header)
        {
            $candidate = (new Candidate())::with('candidateIndexForCandidate:id,first_name,last_name,middle_name,school_code,candidate_index')
                                    ->where('school_code',  request()->school_code)
                                    ->where('course_header',  request()->course_header)
                ->orderBy('candidate_index','asc')
                ->groupBy('candidate_index')
                ->get();
               return $this->successResponse($candidate, "Retrieving Candidate information");
        }
        // working
        if (request()->school_code) {
            $candidate = (new Candidate())::where('school_code', 'LIKE', '%' . request()->school_code . '%')
                ->with('candidateIndexForCandidate:id,first_name,last_name,middle_name,school_code,candidate_index')
                ->orderBy('candidate_index','asc')
                ->groupBy('candidate_index')
                ->get();
            return $this->dataOutput($candidate);
        }
        // working
        if (request()->course_header) {
            $candidate = (new Candidate())::where('course_header', 'LIKE', '%' . request()->course_header . '%')
                ->with('candidateIndexForCandidate:id,first_name,last_name,middle_name,school_code,candidate_index')
                ->orderBy('candidate_index','asc')
                ->groupBy('candidate_index')
                ->get();
            return $this->dataOutput($candidate);
        }
        // working
        if (request()->exam_year) {
            $exam_year = str_split(request()->input('exam_year'))[2] . str_split(request()->input('exam_year'))[3];
            $candidate = (new Candidate())->where('exam_id', 'LIKE', '%' . $exam_year )
                ->with('candidateIndexForCandidate:id,first_name,last_name,middle_name,school_code,candidate_index')
                ->orderBy('candidate_index','asc')
                ->groupBy('candidate_index')
                ->get();
            return $this->dataOutput($candidate);
        }
        //working
        if (request()->candidate_index) {
            $candidate = (new Candidate())::where('candidate_index', 'LIKE', '%' . request()->candidate_index . '%')
                ->with('candidateIndexForCandidate:id,first_name,last_name,middle_name,school_code,candidate_index')
                ->orderBy('candidate_index','asc')
                ->groupBy('candidate_index')
                ->get();
            return $this->dataOutput($candidate);
        }

    }

    public function fetchAllCandidateVersion2(): \Illuminate\Http\JsonResponse
    {
        if( request()->course_header AND request()->exam_year AND  !empty(request()->exam_year) AND !empty(request()->course_header))
        {
            $exam_year = str_split(request()->exam_year)[2] . str_split(request()->exam_year)[3];
            $candidate =  (new Candidate())::with('candidateIndexForCandidate:id,first_name,last_name,middle_name,school_code,candidate_index')
                ->where('exam_id','LIKE', '%' . $exam_year)
                ->where('course_header', request()->course_header)
                ->where('school_code', auth()->user()->operator_id)
                ->orderBy('candidate_index','asc')
                ->groupBy('candidate_index')
                ->get();
            return $this->successResponse($candidate, "Retrieving Candidate Information");
        }
        /// working fine
        if(request()->exam_year AND request()->course_header AND !empty(request()->exam_year) AND !empty(request()->course_header))
        {
            $exam_year = str_split(request()->exam_year)[2] . str_split(request()->exam_year)[3];
            $candidate = DB::table('candidates')
                ->where('exam_id','LIKE', '%' . $exam_year)
                ->where('course_header', request()->course_header)
                ->where('school_code', auth()->user()->operator_id)
                ->orderBy('candidate_index','asc')
                ->groupBy('candidate_index')
                ->get();
            return $this->successResponse($candidate, "Retrieving Candidate information");
        }
        // working
        if (request()->course_header) {
            $candidate = (new Candidate())::with('candidateIndexForCandidate:id,first_name,last_name,middle_name,school_code,candidate_index')
                                        ->where('course_header', 'LIKE', '%' . request()->course_header . '%')
                                        ->where('school_code', auth()->user()->operator_id)
                ->orderBy('candidate_index','asc')
                ->groupBy('candidate_index')
                ->get();
            return $this->dataOutput($candidate);
        }
        // working
        if (request()->exam_year) {
            $exam_year = str_split(request()->input('exam_year'))[2] . str_split(request()->input('exam_year'))[3];
            $candidate = (new Candidate())::with('candidateIndexForCandidate:id,first_name,last_name,middle_name,school_code,candidate_index')
                                        ->where('exam_id', 'LIKE', '%' . $exam_year . '%')
                                        ->where('school_code', auth()->user()->operator_id)
                                        ->orderBy('candidate_index','asc')
                                        ->groupBy('candidate_index')
                                        ->get();
            return $this->dataOutput($candidate);
        }
        //working
        if (request()->candidate_index) {
            $candidate = (new Candidate())::where('candidate_index', 'LIKE', '%' . request()->candidate_index . '%')
                ->with('candidateIndexForCandidate:id,first_name,last_name,middle_name,school_code,candidate_index')
                ->where('school_code', auth()->user()->operator_id)
                ->orderBy('candidate_index','asc')
                ->groupBy('candidate_index')
                ->get();
            return $this->dataOutput($candidate);
        }
    }

    public function dataOutput($candidate){
        $data = [
            'candidate' => $candidate,
        ];
        return $this->successResponse($data, "Registered Candidate Information Retrieved Successfully");
    }

    public function trainingSchoolAndCandidateRelationship($school_code): \Illuminate\Http\JsonResponse
    {
        $candidate_with_training_school_info = $this->candidate::where('school_code', $school_code)->with('trainingSchoolCandidate')->paginate(20);
        return $this->successResponse($candidate_with_training_school_info,"Candidate-Training School Information Retrieved");
    }

    public function fetchAllCandidateThatAreRegisteredAndIndexed($school_code): JsonResponse
    {
        $registered_candidate = $this->candidate::where('school_code', $school_code)->with('candidateIndexing')->paginate(10);
        return $this->successResponse($registered_candidate,"Registered Candidate And Indexed Candidate Information Retrieved");
    }

    public function showSingleCandidateDetail($id)
    {
        $single_candidate_index_details = $this->candidate->where('id', $id)->get();
        if(!$single_candidate_index_details)
        {
            return $this->errorResponse( 'Candidate Not Available');
        }
        return $this->successResponse($single_candidate_index_details, "Single Candidate Index Details Retrieved Successfully");
    }

    public function createCandidateForTrainingSchool(): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'course_header' => 'required|string',
            'exam_id' => 'required|integer',
            'candidate_index' => 'required|exists:candidate_indexings|string',
        ]);

        if($validator->fails()){ return $this->errorResponse( $validator->errors()); }

            $school_can_register = TrainingSchool::where('school_code', auth()->user()->operator_id)->first();

            if($school_can_register->can_register == 0)
            {
                return $this->errorResponse("School Cannot Register A Candidate Please Contact Waheb Admin", 401);
            }

            $candidate_index = CandidateIndexing::where('candidate_index', request()->candidate_index)->first();

        if ($candidate_index->unverified == 1)
        {
            return $this->errorResponse("This Candidate Hasnt Been Verified", 401);
        }


        $exam_id_array = str_split(request()->exam_id, 2);

        $school_code = auth()->user()->operator_id;

        $query = $this->candidateIndexing->where('candidate_index', request()->candidate_index)
                                         ->where('course_header', request()->course_header)
                                        ->where('school_code', $school_code);

        $school_check = TrainingSchool::where('school_code', $school_code)->first();

            if($school_check->can_register == 0)
            {
                return $this->errorResponse('School Cant Register A Candidate');
            }

        if (!$query->exists()) {
            $registered_candidate_query = $this->candidate->where('candidate_index',request()->candidate_index)
                                                            ->where('school_code', auth()->user()->operator_id);

            if (request()->course_header === 'AA2' && !$registered_candidate_query->exists() || $registered_candidate_query->count() === 1 && $registered_candidate_query->first()->reg_status === 'pending') {
                $this->candidateIndexing->where('candidate_index', request()->candidate_index)->update(['course_header' => 'AA2']);
            } else {
                return response()->json(['error' => 'Candidate Does not exist for selected course header']);
            }
        }

        $candidate = $this->candidateIndexing->where('candidate_index', request()->candidate_index)->first();
        $candidate->exam_id = request()->exam_id;
        $candidate->exam_date = '20' . $exam_id_array[1] . '-' . $exam_id_array[0] . '-01';
        $candidate->month_yr_reg = request()->exam_id;
        $candidate->registered_at = date('Y-m-d H:i:s');
        $candidate->validate = 'yes';
        $candidate->save();

        $registered_query = $this->candidate->where('candidate_index', request()->candidate_index);
        if ($registered_query->exists()) {
            $registered_query->first()->update([
                'school_code' => $candidate->school_code,
                'candidate_index' => request()->candidate_index,
                'course_header' => request()->course_header,
                'exam_id' => request()->exam_id,
                'reg_status' => 'approved'
            ]);
        } else {
            $this->candidate->create([
                'school_code' => $candidate->school_code,
                'candidate_index' => $candidate->candidate_index,
                'course_header' => $candidate->course_header,
                'exam_id' => request()->exam_id,
                'reg_status' => 'approved'
            ]);
        }

        $data = [
            'registered_candidate' => $registered_query->get(),
            'candidate_index' => $candidate
        ];

        return $this->successResponse($data,'Candidate Registered Successfully');
    }

}

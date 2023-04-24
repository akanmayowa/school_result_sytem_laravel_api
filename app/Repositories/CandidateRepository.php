<?php /** @noinspection ALL */

namespace App\Repositories;

use App\Models\Candidate;
use Exception;
use Illuminate\Support\Facades\Schema;

class CandidateRepository extends BaseRepository{


    public function __construct(Candidate $model) {

        parent::__construct($model);
    }

    /**
     * @throws Exception
     */
    public function checkModelColumnsExist($columns)
    {
        $table = $this->model->getTable();
        $tableColumns = Schema::getColumnListing($table);

        foreach($columns as $column){
            if(!in_array($column, $tableColumns)){
                throw new Exception("{$column} column not found in {$table} table");
            }
        }
    }

    public function all()
    {
        return $this->model->all();
    }

    public function where($array){

        $this->checkModelColumnsExist(array_keys($array));

        return $this->model->where($array);
    }

    public function query(){
        return $this->model->query();
    }

     public function whereFirst($array){

        $this->checkModelColumnsExist(array_keys($array));

        return $this->model->where($array)->first();
    }


    public function create(array $data){
        return $this->model->create($data);
    }

    public function update(array $data, $id)
    {
        return $this->model->findOrFail($id)->update();
    }

    public function show($id){
        return $this->model->find($id);
    }

    public function selectAllFromCandidateAndCandidateIndexing($splitted_year)
    {
        return $this->model->join('candidate_indexings', 'candidate_indexings.candidate_index', 'candidates.candidate_index')
            ->where('candidates.school_code', request()->school_code)
            ->where('candidates.course_header', request()->course_header)
            ->where('candidates.exam_id', 'like', '%' . $splitted_year[1])
            ->groupBy('candidates.candidate_index')
             ->get('candidates.candidate_index', 'candidate_indexings.last_name', 'candidate_indexings.first_name', 'candidate_indexings.middle_name', 'candidates.*', 'candidate_indexings.validate');
    }

    public function selectSchoolCodeAndExamId()
    {
        return $this->model->with('trainingSchoolCandidate')
            ->where('visible',1)
            ->where('exam_id', '!=', FALSE)
            ->get();
    }
    public function selectRegisteredCandidate()
    {
        return $this->model->with(['trainingSchoolCandidate:school_code,school_name,state_code', 'training_school.state'])
            ->join('candidate_indexings', 'candidate_indexings.candidate_index', 'candidates.candidate_index')
            ->where('candidates.course_header', request()->course_header)
            ->where('candidates.exam_id', 'like', '%' . $splitted_exam_year)
            ->select('whb_registered_candidates.*', 'candidate_indexings.validate', 'candidate_indexings.visible')
            ->groupBy('candidates.candidate_index')
            ->sortBy('trainingSchoolCandidate:state_id') //or state_id
            ->get();


    }

    public function filterRegisteredCandidate()
    {
        $filtering_candidate = $registered_candidates->filter(function ($query) {
            return (($query->registration_type == 'fresh' && $x->visible == 1) || $query->registration_type == 'resit' || $query->registration_type == 'resitall');
        });
       return $filtering_candidate->all();
    }



}

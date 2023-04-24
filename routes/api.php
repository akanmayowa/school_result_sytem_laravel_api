<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrainingSchoolController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\SchoolCategoryController;
use App\Http\Controllers\CourseModuleController;
use App\Http\Controllers\CourseHeaderController;
use App\Http\Controllers\AuthController;



// Authentication routes
Route::group(['prefix' => 'auth'], function() {
    Route::post('/verify', [App\Http\Controllers\TwoFactorAuthController::class, 'verifyTwoFactorCode']);
    Route::get('/reset', [App\Http\Controllers\TwoFactorAuthController::class, 'resendTwoFactorCode']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('forgot-password', [\App\Http\Controllers\PasswordController::class, 'forgetPassword']);
     Route::post('/training-school-verify', [App\Http\Controllers\TrainingSchoolAuthController::class, 'verifyTwoFactorCode']);
     Route::post('/training-school-login', [App\Http\Controllers\TrainingSchoolAuthController::class, 'login']);
    Route::post('reset-password', [\App\Http\Controllers\PasswordController::class, 'resetPassword'])->name('password.reset');

    // reset user two-factor authentication code
    Route::post('/resend-two-factor-code', [AuthController::class, 'resetTwoFactorAuthenticationCode']);
});

// Only authenticated users can access this route
Route::group(['middleware' => ['auth:api']], function() {
    // Only admin can access this route
    Route::group(['prefix' => 'admin', 'middleware' => ['isAdmin']
    ], function() {


        Route::get('auto-reminder', [\App\Http\Controllers\ExamOffenderController::class, 'reminder']);
        Route::get('count-unread-notification', [\App\Http\Controllers\TrainingSchoolNotificationController::class, 'countUnreadNotifications']);

        /// for dashboard counter
        Route::get('dashboard-counter-v2', [\App\Http\Controllers\TrainingSchool\DashBoardCounterController::class, 'indexV2']);

        /// updsate user profile
        Route::put('/user-profile-update', [\App\Http\Controllers\UserController::class, 'update'])->name('user.update');
        Route::put('/user-profile-image-update', [\App\Http\Controllers\UserController::class, 'updateProfileImage'])->name('user.profile.image.update');
        Route::get('/user-profile-information', [\App\Http\Controllers\UserController::class, 'authenticatedUserInformation']);

        //Change password for users
        Route::post('change-password', [\App\Http\Controllers\PasswordController::class, 'changePassword']);

        //state routes
        Route::get('state/index', [StateController::class, 'index']);
        Route::post('state/store', [StateController::class, 'store']);

        //school category routes
        Route::get('school-category/index', [SchoolCategoryController::class, 'index']);
        Route::post('school-category/store', [SchoolCategoryController::class, 'store']);

        //training school routes
        Route::resource('training-school', TrainingSchoolController::class);
        Route::post('training-school/change-status/{id}', [TrainingSchoolController::class, 'changeStatus']);
        Route::get('training-school-status-null', [\App\Http\Controllers\TrainingSchoolController::class, 'index_II']);
        Route::post('training-school-can-register-status', [\App\Http\Controllers\TrainingSchoolController::class, 'changeCanRegiterStatus']);
        Route::patch('training-school/{id}', [\App\Http\Controllers\TrainingSchool\TrainingSchoolController::class, 'update']);


        //course modules routes
        Route::get('course-module', [CourseModuleController::class, 'index']);
        Route::post('course-module', [CourseModuleController::class, 'store']);
        Route::put('course-module/{id}', [CourseModuleController::class, 'update']);
        Route::post('course-module-delete-status', [CourseModuleController::class, 'deleteStatus']);
        Route::post('course-module-practical-status', [CourseModuleController::class, 'practicalStatus']);

        //course header routes
        Route::get('course-header', [CourseHeaderController::class, 'index']);
        Route::
        get('course-header-total', [CourseHeaderController::class, 'totals']);
        Route::get('course-header-delete-status-no', [CourseHeaderController::class, 'index_II']);
        Route::post('course-header', [CourseHeaderController::class, 'store']);
        Route::put('course-header/{id}', [CourseHeaderController::class, 'update']);
        Route::post('course-header-delete-status', [CourseModuleController::class, 'deleteStatus']);
        Route::post('course-header-practical-status', [CourseModuleController::class, 'practicalStatus']);

        //Nationality routes
        Route::get('nationality', [\App\Http\Controllers\NationalityController::class, 'index']);
        Route::post('nationality', [\App\Http\Controllers\NationalityController::class, 'store']);

        //CertificateEvaluation
        Route::get('certificate-evaluation', [\App\Http\Controllers\CertificateEvaluationController::class, 'index']);
        Route::post('certificate-evaluation', [\App\Http\Controllers\CertificateEvaluationController::class, 'store']);

        //CandidateCategory
        Route::get('candidate-category', [\App\Http\Controllers\CandidateCategoryController::class, 'index']);
        Route::post('candidate-category', [\App\Http\Controllers\CandidateCategoryController::class, 'store']);

        ///Grade routes
        Route::get('grade', [\App\Http\Controllers\GradeController::class, 'index']);
        Route::post('grade', [\App\Http\Controllers\GradeController::class, 'store']);

        //Candidate Indexing
        Route::get('candidate-indexing', [\App\Http\Controllers\CandidateIndexingController::class, 'index']);
        Route::get('candidate-indexing/{id}', [\App\Http\Controllers\CandidateIndexingController::class, 'show']);
        Route::post('candidate-indexing', [\App\Http\Controllers\CandidateIndexingController::class, 'store']);
//        Route::put('candidate-indexing/{id}', [\App\Http\Controllers\CandidateIndexingController::class, 'update']);

        Route::post('candidate-indexing-update', [\App\Http\Controllers\CandidateIndexingController::class, 'update']);
        Route::get('candidate-indexing-all', [\App\Http\Controllers\CandidateIndexingController::class, 'index_II']);
        Route::post('candidate-indexing-search', [\App\Http\Controllers\CandidateIndexingController::class, 'index_III']);
        Route::delete('candidate-indexing-delete', [\App\Http\Controllers\CandidateIndexingController::class, 'delete']);

        //Candidate
        // Route::get('candidate', [\App\Http\Controllers\CandidateIndexingController::class, 'index']);
        Route::post('candidate', [\App\Http\Controllers\CandidateController::class, 'store']);
        Route::post('candidate/{id}/update', [\App\Http\Controllers\CandidateController::class, 'update']);
        Route::get('candidate', [\App\Http\Controllers\CandidateController::class, 'index']);
        Route::get('candidate/{id}', [\App\Http\Controllers\CandidateController::class, 'show']);
        Route::get('registered-and-indexed-candidate/{schoolCode}', [\App\Http\Controllers\CandidateController::class, 'indexVersionBeta']);


        // exam offence
        Route::post('exam-offence', [\App\Http\Controllers\ExamOffenceController::class, 'store']);
        Route::get('exam-offence', [\App\Http\Controllers\ExamOffenceController::class, 'index']);
        // Route::put('exam-offence/{examOffenceId}', [\App\Http\Controllers\ExamOffenceController::class, 'update']);
        Route::get('exam-offence/{examOffenceId}', [\App\Http\Controllers\ExamOffenceController::class, 'show']);

        // Exam offender
        Route::post('exam-offender', [\App\Http\Controllers\ExamOffenderController::class, 'store']);
        Route::get('exam-offender', [\App\Http\Controllers\ExamOffenderController::class, 'index']);
        Route::put('exam-offender/{id}', [\App\Http\Controllers\ExamOffenderController::class, 'update']);
        Route::get('exam-offender/{id}', [\App\Http\Controllers\ExamOffenderController::class, 'show']);
        Route::delete('exam-offender/{id}', [\App\Http\Controllers\ExamOffenderController::class, 'destroy']);

        // school resit
        Route::post('school-resit', [\App\Http\Controllers\SchoolResitController::class, 'store']);
        Route::post('school-resit-filter', [\App\Http\Controllers\SchoolResitController::class, 'index']);
        Route::get('school-resit-count', [\App\Http\Controllers\SchoolResitController::class, 'schoolResitcounter']);
        Route::get('school-resit-all', [\App\Http\Controllers\SchoolResitController::class, 'indexV2']);
        Route::get('school-resit-all', [\App\Http\Controllers\SchoolResitController::class, 'show']);
        Route::delete('school-resit/delete', [\App\Http\Controllers\SchoolResitController::class, 'delete']);

        // candidate score
        Route::post('candidate-score-entry', [\App\Http\Controllers\CandidateScoreController::class,'scoreEntry']);
        Route::post('candidate-score-entry/store', [\App\Http\Controllers\CandidateScoreController::class,'store']);
        Route::post('candidate-score-entry', [\App\Http\Controllers\CandidateScoreController::class,'update']);
        Route::get('candidate-total-score', [\App\Http\Controllers\CandidateScoreController::class,'candidateScore']);
        Route::get('candidate-score-course-header', [\App\Http\Controllers\CandidateScoreController::class,'indexForCourseHeaderNotDeleted']);
        Route::post('candidate-exam-score', [\App\Http\Controllers\CandidateScoreController::class,'indexForCandidateExamScore']);


        Route::post('candidate-score-for-score-marke-one', [\App\Http\Controllers\CandidateScoreController::class,'indexForScoreMarkerOne']);
        Route::post('candidate-final-score', [\App\Http\Controllers\CandidateScoreController::class,'indexForResult']);

        // Exam
        Route::get('exam', [\App\Http\Controllers\ExamController::class, 'index']);
        Route::post('exam', [\App\Http\Controllers\ExamController::class, 'store']);

        //Candidate inCourse
        Route::get('candidate-in-course', [\App\Http\Controllers\CandidateInCourseController::class, 'index']);
        Route::post('candidate-in-course', [\App\Http\Controllers\CandidateInCourseController::class, 'store']);

        //Training School Notifications to Super Admin and Admin Dashboard Notification Page
        Route::get('training-school-notification-unread', [\App\Http\Controllers\TrainingSchoolNotificationController::class, 'getAllUnReadNotification']);
        Route::get('training-school-notification-read', [\App\Http\Controllers\TrainingSchoolNotificationController::class, 'getAllReadNotification']);
        Route::post('training-school-notification-change-status', [\App\Http\Controllers\TrainingSchoolNotificationController::class, 'changeNotificationStatus']);
        Route::get('training-school-notification-all', [\App\Http\Controllers\TrainingSchoolNotificationController::class, 'fetchAllNotification']);

        Route::post('verify-candidate-indexing', [\App\Http\Controllers\CandidateIndexingController::class, 'verifyCandidateIndex']);

        // BroadCast Notification  routes for super admin and admin
        Route::post('broadcast-message-to-all-training-school', [\App\Http\Controllers\BroadCastMessageController::class, 'sentMessageToAllTrainingSchool']);
        Route::post('broadcast-message-to-selected-training-school', [\App\Http\Controllers\BroadCastMessageController::class, 'sendBroadCastToSelectedTrainingSchool']);
        Route::post('broadcast-message-to-training-school-based-on-selected-courses', [\App\Http\Controllers\BroadCastMessageController::class, 'sendBroadCastToTrainingSchoolBasedOnSelectedCourses']);
        Route::get('broadcast-message-for-admin', [\App\Http\Controllers\BroadCastMessageController::class, 'fetchAllBroadCastForAdmins']);
        Route::get('broadcast-message-for-status', [\App\Http\Controllers\BroadCastMessageController::class, 'changeBroadCastMessageStatus']);
        Route::get('broadcast-message-get-single', [\App\Http\Controllers\BroadCastMessageController::class, 'show']);
        Route::get('fetch-single-notification', [\App\Http\Controllers\BroadCastMessageController::class, 'show_II']);

        // Candidate Information Retrieval(Printing Document)
        Route::post('candidate-information-retrieval-fresher', [\App\Http\Controllers\CandidateInformationRetrievalController::class, 'retrievingFresherCandidateInformation']);
        Route::post('candidate-information-retrieval-indexing', [\App\Http\Controllers\CandidateInformationRetrievalController::class, 'retrievingIndexedCandidateInformation']);
        Route::post('candidate-information-retrieval-statistics', [\App\Http\Controllers\CandidateInformationRetrievalController::class, 'retrievingIndexedCandidateStatisticsInformation']);
        Route::post('candidate-information-retrieval-resitting', [\App\Http\Controllers\CandidateInformationRetrievalController::class, 'retrievingResistingCandidateInformation']);
        Route::post('candidate-information-retrieval-project-study', [\App\Http\Controllers\CandidateInformationRetrievalController::class, 'retrievingCandidateProjectOfStudy']);
        Route::post('candidate-information-retrieval-school-perfomance', [\App\Http\Controllers\CandidateInformationRetrievalController::class, 'retrievingCandidateSchoolPerfomance']);
        Route::post('candidate-information-retrieval-result-analysis', [\App\Http\Controllers\CandidateInformationRetrievalController::class, 'retrievingCandidateResultAnalysis']);
        Route::post('candidate-information-retrieval-all-school-perfomance', [\App\Http\Controllers\CandidateInformationRetrievalController::class, 'retrievealOfAllSchoolExamPerformance']);
        Route::post('candidate-information-retrieval-course-index-statistics', [\App\Http\Controllers\CandidateInformationRetrievalController::class, 'retrievalOfCourseIndexStatistics']);
        Route::post('candidate-information-retrieval-practical-mark-sheet', [\App\Http\Controllers\CandidateInformationRetrievalController::class, 'retrievalOfPracticalMarksheet']);
        Route::post('candidate-information-retrieval-oral-mark-sheet', [\App\Http\Controllers\CandidateInformationRetrievalController::class, 'retrievalOfOralMarksheet']);
        Route::post('candidate-information-retrieval-score-result', [\App\Http\Controllers\CandidateInformationRetrievalController::class, 'retrievalOfScoresResultBasedOnPassAndfailStatistic']);
        Route::post('candidate-information-retrieval-application-indexing', [\App\Http\Controllers\CandidateInformationRetrievalController::class, 'retrievealOfApplicationForIndexing']);




        // Candidate Index Search
        Route::post('candidate-search', [\App\Http\Controllers\CandidateIndexSearchController::class, 'search']);
        Route::get('fetch-candidate-exam-id', [\App\Http\Controllers\CandidateIndexSearchController::class, 'getExamId']);


        Route::get('dashboard-counter', [\App\Http\Controllers\TrainingSchool\DashBoardCounterController::class, 'index']);


        Route::group(['prefix' => 'print'], function() {
            Route::get('candidate-indexing', [\App\Http\Controllers\PrintDocumentController::class, 'candidateIndexing']);
            Route::get('oral-mark-sheet', [\App\Http\Controllers\PrintDocumentController::class, 'oralMarkSheet']);
            Route::get('research-project', [\App\Http\Controllers\PrintDocumentController::class, 'researchProject']);
            Route::get('research-project-aa2', [\App\Http\Controllers\PrintDocumentController::class, 'researchProjecAA2']);
            Route::get('zonal-mark-sheet', [\App\Http\Controllers\PrintDocumentController::class, 'zonalMarkSheet']);
            Route::get('course-registration-statistics', [\App\Http\Controllers\PrintDocumentController::class, 'courseRegistrationStatistics']);
        });


        Route::post('verification', [\App\Http\Controllers\CandidateIndexingController::class, 'verifyCandidateIndex']);

    });


    //  Only school-admin can access this route
    Route::group(['prefix' => 'school-admin', 'middleware' => ['isSchoolAdmin']], function() {
        // ...
    });

    //  Only student can access this route
    Route::group(['prefix' => 'student', 'middleware' => ['isStudent']], function() {
        // ...
    });

    //  Only TrainingSchool admin can access this routes here
    Route::group(['prefix' => 'training-school-admin', 'middleware' => ['isTrainingSchoolAdmin']], function() {

        Route::get('fetch-candidate-exam-id', [\App\Http\Controllers\CandidateIndexSearchController::class, 'getExamIdForSchoools']);
        Route::post('candidate-indexing', [\App\Http\Controllers\CandidateIndexingController::class, 'store_II']);
        Route::post('candidate-registration', [\App\Http\Controllers\CandidateController::class, 'store_II']);

        // BroadCast Message Module for TrainingSchool
        Route::post('broadcast-message-to-waheb-admin', [\App\Http\Controllers\BroadCastMessageController::class, 'sendBroadCastToWahebAdmin']);

        // Update User/Training School Proflie
        Route::post('update-user-training-school-profile', [\App\Http\Controllers\UserController::class, 'updateUserTrainingSchoolRelationship']);
        Route::post('update-user-training-school-profile-image', [\App\Http\Controllers\UserController::class, 'updateUserTrainingSchoolRelationshipImage']);

        Route::get('user-profile-information', [\App\Http\Controllers\UserController::class, 'authenticatedUserInformation']);
        Route::get('candidate-indexing/{id}', [\App\Http\Controllers\CandidateIndexingController::class, 'show']);
        Route::get('candidate-indexing', [\App\Http\Controllers\TrainingSchool\CandidateIndexingController::class, 'index']);
        Route::get('candidate', [\App\Http\Controllers\TrainingSchool\CandidateController::class, 'index']);
        Route::get('candidate-score', [\App\Http\Controllers\TrainingSchool\CandidateScoreController::class, 'index']);
        Route::get('course-header', [\App\Http\Controllers\TrainingSchool\CourseHeaderController::class, 'index']);
        Route::get('course-module', [\App\Http\Controllers\TrainingSchool\CourseModuleController::class, 'indexV2']);
        Route::get('exam-offenders', [\App\Http\Controllers\TrainingSchool\ExamOffendersController::class, 'index']);
        Route::get('school-resit', [\App\Http\Controllers\TrainingSchool\SchoolResitController::class, 'index']);
        Route::get('training-school', [\App\Http\Controllers\TrainingSchool\TrainingSchoolController::class, 'index']);
        Route::get('training-school-dashboard-counter', [\App\Http\Controllers\TrainingSchool\DashBoardCounterController::class, 'indexV2']);
        Route::get('candidate-category', [\App\Http\Controllers\TrainingSchool\CandidateCategoryController::class, 'index']);
        Route::get('nationality', [\App\Http\Controllers\TrainingSchool\NationalityController::class, 'index']);
        Route::patch('training-school/{id}', [\App\Http\Controllers\TrainingSchool\TrainingSchoolController::class, 'update']);
        Route::get('candidate-training-school/{schoolCode}', [\App\Http\Controllers\TrainingSchool\CandidateController::class, 'indexVersion2']);
        Route::get('registered-and-indexed-candidate/{schoolCode}', [\App\Http\Controllers\TrainingSchool\CandidateController::class, 'indexVersionBeta']);

        /// print document for schoools
        Route::post('candidate-information-retrieval-fresher_II', [\App\Http\Controllers\CandidateInformationRetrievalController::class, 'retrievingFresherCandidateInformation_II']);
        Route::post('candidate-information-retrieval-indexing_II', [\App\Http\Controllers\CandidateInformationRetrievalController::class, 'retrievingIndexedCandidateInformation_II']);
        Route::post('candidate-information-retrieval-resitting_II', [\App\Http\Controllers\CandidateInformationRetrievalController::class, 'retrievingResistingCandidateInformation_II']);
        Route::post('print-candidate-indexing_II', [\App\Http\Controllers\PrintDocumentController::class, 'candidateIndexing_II']);
        Route::post('candidate-information-retrieval-result-analysis_II', [\App\Http\Controllers\CandidateInformationRetrievalController::class, 'retrievingCandidateResultAnalysis_II']);
        Route::get('school-resit-all/{id}', [\App\Http\Controllers\SchoolResitController::class, 'show']);
        Route::get('broadcast-message-for-status', [\App\Http\Controllers\BroadCastMessageController::class, 'changeBroadCastMessageStatus']);
        Route::get('broadcast-message-for-admin', [\App\Http\Controllers\BroadCastMessageController::class, 'fetchAllBroadCastForSchoolAdmin']);
        Route::get('broadcast-message-get-single', [\App\Http\Controllers\BroadCastMessageController::class, 'show']);


        Route::post('verification', [\App\Http\Controllers\CandidateIndexingController::class, 'verifyCandidateIndex']);
        Route::get('course-header-delete-status-no', [CourseHeaderController::class, 'index_II']);
        Route::get('training-school-status-null', [\App\Http\Controllers\TrainingSchoolController::class, 'index_II']);
        Route::get('candidate-indexing-all', [\App\Http\Controllers\CandidateIndexingController::class, 'index_II']);
        Route::post('candidate-indexing-search-school', [\App\Http\Controllers\CandidateIndexingController::class, 'index_IIII']);

        Route::get('logged-in-school', [\App\Http\Controllers\TrainingSchoolController::class, 'loginInSchoolDetails']);
        Route::get('count-unread-notification', [\App\Http\Controllers\TrainingSchoolNotificationController::class, 'countUnreadNotifications']);

        Route::post('change-password', [\App\Http\Controllers\PasswordController::class, 'changePassword']);
        Route::post('candidate-indexing-update', [\App\Http\Controllers\CandidateIndexingController::class, 'update']);
    });





});

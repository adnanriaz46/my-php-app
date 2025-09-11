<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Services\EmailService;
use App\Helper\AssessmentHelper;
use App\Helper\LocationHelper;
use App\Models\AssessmentDripEmail;
use App\Models\AssessmentResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Inertia\Response;

class AssessmentController extends Controller
{
    /**
     * Display the assessment page
     */
    public function show(): Response
    {
        $user = Auth::user();
        $assessment = null;

        // If user is logged in, try to get existing assessment
        if ($user) {
            $assessment = AssessmentResponse::where('email', $user->email)->first();
        }

        // Get investment strategies
        $investmentStrategies = [
            "Fix & Flip",
            "Buy & Hold",
            "Realtor / Broker",
            "Wholesaling",
            "Land",
            "Being A Private Lender",
            "Development (New Construction)"
        ];

        // Get counties from LocationHelper
        $counties = LocationHelper::getComboboxCounties();

        return Inertia::render('guest-page/DoAssessmentPage', [
            'assessment' => $assessment,
            'investmentStrategies' => $investmentStrategies,
            'counties' => $counties,
            'isAuthenticated' => Auth::check(),
            'user' => $user ? [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
            ] : null,
            'nextStep' => session('nextStep') ? session('nextStep') : null,
        ]);
    }

    /**
     * Save assessment step data
     */
    public function saveStep(Request $request)
    {

        $step = $request->input('step');

        if (!$step) {
            return redirect()->route('guest.do_assessment')->withErrors(['step' => 'Step is required']);
        }

        // Validate based on step
        $validationRules = $this->getValidationRules((int) $step);
        $validated = $request->validate($validationRules);

        // Get or create assessment response
        $assessment = $this->getOrCreateAssessment($request);

        // Update assessment with step data
        $this->updateAssessmentStep($assessment, (int) $step, $validated);

        // Store next step in session for persistence
        $nextStep = (int) $step + 1;

        return redirect()->route('guest.do_assessment')->with('success', 'Step saved successfully')->with('nextStep', $nextStep);
    }

    /**
     * Get assessment data
     */
    public function getAssessment(Request $request)
    {
        $email = $request->input('email');

        if (!$email) {
            return redirect()->route('guest.do_assessment')->with('error', 'Email is required');
        }

        $assessment = AssessmentResponse::where('email', $email)->first();

        return redirect()->route('guest.do_assessment', ['email' => $email]);
    }

    private function assessmentAiEmailDripGeneration($assessment)
    {

        // Assemble the messages for the AI
        $messages = AssessmentHelper::getAiOverviewInitMessage($assessment);
        $response = $this->sendAiRequest($messages);
        $content = json_decode($response->content());

        $textResponse = $content?->choices[0]->message->content;

        if (!$textResponse) {
            return false;
        }
        $assessment = AssessmentResponse::where('email', $assessment->email)->first();
        // Store the response in the database
        $assessment->update([
            'assessment_overview_response' => $textResponse,
        ]);

        // Generate email drip
        $steps = $this->generateEmailDrip($assessment);

        // Send overview email to user
        $user = User::where('email', $assessment->email)->first();

        $emailService = app(EmailService::class);
        $config = config('user_email_config.categories_description.assessment_drip');
        if ($user) {
            $emailService->send($user, 'assessment_drip', $config['template_id'], [
                'subject' => 'REI Blueprint - Customized Strategy Plan For ' . $assessment->first_name . ' ' . $assessment->last_name,
                'body' => $textResponse,
                'unsubscribe' => url('unsubscribe/' . $user->uuid)
            ]);
        } else {
            $emailService->sendCustom($assessment->email, $assessment->first_name . ' ' . $assessment->last_name, 'assessment_drip', $config['template_id'], [
                'subject' => 'REI Blueprint - Customized Strategy Plan For ' . $assessment->first_name . ' ' . $assessment->last_name,
                'body' => $textResponse
            ]);
        }
        $assessment->update([
            'drip_mail_completed_step' => 0,
            'drip_mail_sent_at' => now(),
        ]);

        return $steps;
    }

    private function generateEmailDrip($assessment)
    {
        // Increase max execution time and memory limit for this potentially long-running AI request
        set_time_limit(900); // 15 minutes
        ini_set('memory_limit', '512M');

        $assessmentID = $assessment->id;
        $messages = AssessmentHelper::getAiEmailDripMessages($assessment);
        $response = $this->sendAiRequest($messages);
        $content = json_decode($response->content());
        $textResponse = $content?->choices[0]->message->content;

        $Emails = AssessmentHelper::getAiDEResponseEmailAndSubject($textResponse);

        /// next step
        $messages[] = [
            'role' => 'assistant',
            'content' => $textResponse,
        ];
        $messages = AssessmentHelper::getAiEmailDripWithPreviousMessages($messages);

        $response = $this->sendAiRequest($messages);
        $content = json_decode($response->content());
        $textResponse = $content?->choices[0]->message->content;

        $Emails = array_merge($Emails, AssessmentHelper::getAiDEResponseEmailAndSubject($textResponse));

        $steps = 1;
        foreach ($Emails as $Email) {
            AssessmentDripEmail::updateOrCreate(
                [
                    'assessment_response_id' => $assessmentID,
                    'step' => $steps,
                ],
                [
                    'subject' => $Email['subject'],
                    'body' => $Email['email'],
                    'status' => AssessmentDripEmail::STATUS_PENDING,
                    'sent_at' => null,
                ]
            );
            $steps++;
        }

        return $steps;
    }


    /**
     * Complete assessment
     */
    public function completeAssessment(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $assessment = AssessmentResponse::where('email', $request->email)->first();

        $succcessfullSteps = $this->assessmentAiEmailDripGeneration($assessment);

        if (!$assessment) {
            return redirect()->route('guest.do_assessment')->with('error', 'Assessment not found');
        }

        $assessment->update([
            'assessment_completed' => true,
        ]);

        // Clear the assessment step from session
        session()->forget('current_assessment_step');

        return redirect()->back()->with('success', 'Assessment completed successfully! ' . $succcessfullSteps . ' steps generated');
    }

    /**
     * Get validation rules for each step
     */
    private function getValidationRules(int $step): array
    {
        $rules = [];

        switch ($step) {
            case 1:
                $rules = [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'email' => 'required|email|max:255',
                    'phone' => 'required|string|max:20',
                ];
                break;

            case 2:
                $rules = [
                    'investment_strategy' => 'required|array|min:1',
                    'investment_strategy.*' => 'string|in:Fix & Flip,Buy & Hold,Realtor / Broker,Wholesaling,Land,Being A Private Lender,Development (New Construction)',
                ];
                break;

            case 3:
                $rules = [
                    'counties_invest' => 'required|array|min:1',
                    'counties_invest.*' => 'string',
                ];
                break;

            case 4:
                $rules = [
                    'how_long_in_rei' => 'required|string|in:Don\'t Have,<1 Year,1-2 Years,3-5 Years,5-10 Years,10+ Years',
                ];
                break;

            case 5:
                $rules = [
                    'how_many_agent_deals' => 'required|integer|min:0',
                    'how_many_flips' => 'required|integer|min:0',
                    'how_many_land' => 'required|integer|min:0',
                    'how_many_new_construction' => 'required|integer|min:0',
                    'how_many_private_lending' => 'required|integer|min:0',
                    'how_many_rentals' => 'required|integer|min:0',
                    'how_many_wholesale' => 'required|integer|min:0',
                ];
                break;

            case 6:
                $rules = [
                    'other_investments_yn' => 'required|boolean',
                    'other_investments_detail' => 'nullable|string|max:1000',
                ];
                break;

            case 7:
                $rules = [
                    'primary_goal' => 'required|string|max:2000',
                ];
                break;

            case 8:
                $rules = [
                    'investment_biz_plan' => 'required|string|max:2000',
                ];
                break;

            case 9:
                $rules = [
                    'smart_goals' => 'required|string|max:2000',
                ];
                break;

            case 10:
                $rules = [
                    'main_obstacle' => 'required|string|max:2000',
                ];
                break;

            case 11:
                $rules = [
                    'acq_strategy' => 'required|array|min:1',
                    'acq_strategy.*' => 'string',
                ];
                break;

            case 12:
                $rules = [
                    'time_per_week_actual' => 'required|string|in:<1 Hour,1-2 Hours,2-5 Hours,5-10 Hours,10+ Hours',
                ];
                break;

            case 13:
                $rules = [
                    'time_per_week_goal' => 'required|string|in:<1 Hour,1-2 Hours,2-5 Hours,5-10 Hours,10+ Hours,20+ hours,I need a team',
                ];
                break;

            case 14:
                $rules = [
                    'pillar_ranking_finance' => 'required|integer|min:0|max:10',
                    'pillar_ranking_leadership' => 'required|integer|min:0|max:10',
                    'pillar_ranking_marketing' => 'required|integer|min:0|max:10',
                    'pillar_ranking_operations' => 'required|integer|min:0|max:10',
                    'pillar_ranking_sales' => 'required|integer|min:0|max:10',
                ];
                break;

            case 15:
                $rules = [
                    'average_deal_profit' => 'required|numeric|min:0',
                ];
                break;

            case 16:
                $rules = [
                    'what_would_you_pay' => 'required|numeric|min:0',
                ];
                break;

            case 17:
                $rules = [
                    'want_auto_offers' => 'required|boolean',
                ];
                break;

            case 18:
                $rules = [
                    'additional_comments' => 'nullable|string|max:2000',
                ];
                break;
        }

        return $rules;
    }

    /**
     * Get or create assessment response
     */
    private function getOrCreateAssessment(Request $request): AssessmentResponse
    {
        $email = $request->input('email');

        if (!$email) {
            throw new \Exception('Email is required');
        }

        $assessment = AssessmentResponse::where('email', $email)->first();

        if (!$assessment) {
            $assessment = AssessmentResponse::create([
                'email' => $email,
                'first_name' => $request->input('first_name', ''),
                'last_name' => $request->input('last_name', ''),
                'phone' => $request->input('phone', ''),
                'user_identifier' => $email,
            ]);
        }

        return $assessment;
    }

    /**
     * Update assessment with step data
     */
    private function updateAssessmentStep(AssessmentResponse $assessment, int $step, array $data): void
    {
        $updateData = [];

        switch ($step) {
            case 1:
                $updateData = [
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'user_identifier' => $data['email'],
                ];
                break;

            case 2:
                $updateData = ['investment_strategy' => $data['investment_strategy']];
                break;

            case 3:
                $updateData = ['counties_invest' => $data['counties_invest']];
                break;

            case 4:
                $updateData = ['how_long_in_rei' => $data['how_long_in_rei']];
                break;

            case 5:
                $updateData = [
                    'how_many_agent_deals' => $data['how_many_agent_deals'],
                    'how_many_flips' => $data['how_many_flips'],
                    'how_many_land' => $data['how_many_land'],
                    'how_many_new_construction' => $data['how_many_new_construction'],
                    'how_many_private_lending' => $data['how_many_private_lending'],
                    'how_many_rentals' => $data['how_many_rentals'],
                    'how_many_wholesale' => $data['how_many_wholesale'],
                ];
                break;

            case 6:
                $updateData = [
                    'other_investments_yn' => $data['other_investments_yn'],
                    'other_investments_detail' => $data['other_investments_detail'] ?? null,
                ];
                break;

            case 7:
                $updateData = ['primary_goal' => $data['primary_goal']];
                break;

            case 8:
                $updateData = ['investment_biz_plan' => $data['investment_biz_plan']];
                break;

            case 9:
                $updateData = ['smart_goals' => $data['smart_goals']];
                break;

            case 10:
                $updateData = ['main_obstacle' => $data['main_obstacle']];
                break;

            case 11:
                $updateData = ['acq_strategy' => $data['acq_strategy']];
                break;

            case 12:
                $updateData = ['time_per_week_actual' => $data['time_per_week_actual']];
                break;

            case 13:
                $updateData = ['time_per_week_goal' => $data['time_per_week_goal']];
                break;

            case 14:
                $updateData = [
                    'pillar_ranking_finance' => $data['pillar_ranking_finance'],
                    'pillar_ranking_leadership' => $data['pillar_ranking_leadership'],
                    'pillar_ranking_marketing' => $data['pillar_ranking_marketing'],
                    'pillar_ranking_operations' => $data['pillar_ranking_operations'],
                    'pillar_ranking_sales' => $data['pillar_ranking_sales'],
                ];
                break;

            case 15:
                $updateData = ['average_deal_profit' => $data['average_deal_profit']];
                break;

            case 16:
                $updateData = ['what_would_you_pay' => $data['what_would_you_pay']];
                break;

            case 17:
                $updateData = ['want_auto_offers' => $data['want_auto_offers']];
                break;

            case 18:
                $updateData = ['additional_comments' => $data['additional_comments'] ?? null];
                break;
        }

        if (!empty($updateData)) {
            $assessment->update($updateData);
        }
    }

    public function useAiForSmartGoals(Request $request)
    {
        $messages = $request->input('messages');

        $response = $this->sendAiRequest($messages);

        return $response;
    }



    private function sendAiRequest($messages = [])
    {

        $key = config('services.chat_gpt.key');


        // Non-streaming response (fallback)
        $response = Http::withToken($key)->timeout(200)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o',
                'messages' => $messages,
                'temperature' => 0.2,
            ]);

        return response()->json($response->json());

    }
}

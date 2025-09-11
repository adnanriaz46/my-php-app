<?php

namespace App\Http\Controllers;

use App\Models\CountyState;
use App\Models\ContactUsRequest;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\AirtablePodcastService;
use Illuminate\Support\Facades\Cache;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('guest-page/HomePage', [
            'debug_guest_session' => [
                'has_impersonating' => $request->session()->has('impersonating_admin_id'),
                'impersonating_value' => $request->session()->get('impersonating_admin_id'),
                'user_id' => $request->user()?->id,
                'is_authenticated' => $request->user() !== null,
            ]
        ]);
    }

    public function productOverview()
    {
        return Inertia::render('guest-page/ProductOverviewPage');
    }

    public function dealFinder()
    {
        return Inertia::render('guest-page/DealFinderPage');
    }

    public function marketCoverage()
    {
        $counties = CountyState::whereIn('state', ['PA', 'NJ', 'DE', 'MD', 'VA', 'WV', 'DC'])->get()->groupBy('state');

        return Inertia::render('guest-page/MarketCoveragePage', [
            'counties' => $counties
        ]);
    }

    public function coverage($county_slug)
    {
        $county = CountyState::where('slug', $county_slug)->first();

        if (!$county) {
            return redirect()->route('guest.market_coverage');
        }

        return Inertia::render('guest-page/CoveragePage', [
            'county' => $county
        ]);
    }

    public function myBuyBox()
    {
        return Inertia::render('guest-page/MyBuyBoxPage');
    }

    public function automatedOffers()
    {
        return Inertia::render('guest-page/AutomatedOffersPage');
    }

    public function fixFlip()
    {
        return Inertia::render('guest-page/FixFlipPage');
    }

    public function buyAndHold()
    {
        return Inertia::render('guest-page/BuyAndHoldPage');
    }

    public function wholesalers()
    {

        return Inertia::render('guest-page/WholesalersPage');
    }

    public function agents()
    {

        return Inertia::render('guest-page/AgentsPage');
    }

    public function propertyAi()
    {
        return Inertia::render('guest-page/PropertyAiPage');
    }

    public function dataSolutions()
    {
        dd('data solutions');
        return Inertia::render('guest-page/DataSolutionsPage');
    }

    public function aiOnboarding()
    {

        return Inertia::render('guest-page/AiOnboardingPage');
    }

    public function assessment()
    {
        return Inertia::render('guest-page/AssessmentPage');
    }

    public function pricing()
    {
        return Inertia::render('guest-page/PricingPage');
    }

    public function instantComp()
    {
        return Inertia::render('guest-page/InstantCompPage');
    }



    public function contactUs(Request $request)
    {
        $user = $request->user();
        return Inertia::render('guest-page/ContactUsPage', [
            'user' => $user
        ]);
    }

    public function contactUsPost(Request $request, EmailService $emailService)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
        ]);

        // Create contact
        $contact = ContactUsRequest::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'message' => $request->message,
        ]);

        $body = <<<HTML
        <p>Name: {$request->first_name} {$request->last_name}</p>
        <p>Email: {$request->email}</p>
        <p>Phone: {$request->phone}</p>
        <p>Message: {$request->message}</p>
        HTML;

        //  Send email notification to user
        try {
            $emailService = app(EmailService::class);
            $emailService->sendCustom(
                config('mail.admin_email'),
                $request->first_name . ' ' . $request->last_name,
                'contact_us',
                config('mail.templates.contact_us'),
                [
                    'subject' => 'Contact Us Message',
                    'body' => $body,
                    'reply_to_email' => $request->email,
                    'reply_to_name' => $request->first_name . ' ' . $request->last_name
                ]
            );
        } catch (\Exception $e) {
            \Log::error('Contact us email error: ' . $e->getMessage());
        }

        return redirect()->route('guest.contact_us')->with('success', 'Contact us message sent successfully');
    }
    public function podcast(AirtablePodcastService $svc)
    {
        $pageSize = (int) request('pageSize', 20);
        $offset = request('offset');

        // bump this if you change the server payload shape
        $cacheVersion = 'v2';
        $cacheKey = sprintf('podcasts:%s:%d:%s', $cacheVersion, $pageSize, $offset ?: '0');

        if (request()->boolean('nocache') || request()->boolean('debugFields')) {
            // drop any stale cached payload so the next non-nocache view is fresh too
            \Cache::forget($cacheKey);
            $payload = $svc->list($pageSize, $offset ?: null);
        } else {
            $payload = \Cache::remember($cacheKey, 600, fn() => $svc->list($pageSize, $offset ?: null));
        }

        return Inertia::render('guest-page/RevampPodcastPage', $payload);
    }
    public function termsofuse()
    {
        return Inertia::render('guest-page/TermsOfUse');
    }
    public function privacy()
    {
        return Inertia::render('guest-page/Privacy');
    }
}

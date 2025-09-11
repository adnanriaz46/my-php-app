<?php

namespace App\Http\Controllers\Settings;

use App\Helper\LocationHelper;
use App\Helper\PropertyHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use App\Models\AwsS3History;
use App\Models\BuyBox;
use App\Models\User;
use App\Services\EmailService;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;

class ProfileController extends Controller
{
    public function __construct(
        protected EmailService $emailService
    ) {}

    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/Profile', [
            'mustVerifyEmail' => !$request->user()->hasVerifiedEmail(),
            'status' => $request->session()->get('status'),
            'success' => $request->session()->get('success'),
            'error' => $request->session()->get('error'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'firstName' => ['required', 'string', 'max:50'],
            'lastName' => ['required', 'string', 'max:50'],
            'streetAddress' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'zip' => ['nullable', 'string', 'max:20'],
            'phoneNumber' => ['required', 'string', 'max:20', 'regex:/^(\+1\s?)?(\(?\d{3}\)?[\s.-]?)\d{3}[\s.-]?\d{4}$/'],
            'aboutMe' => ['nullable', 'string', 'max:1000']
        ],
            [
                'phoneNumber.regex' => 'The phone number must be a valid US number (e.g., (123) 456-7890, 1234567890).',
            ]);


        $user->update([
            'name' => $request->input('firstName') . ' ' . $request->input('lastName'),
            'first_name' => $request->input('firstName'),
            'last_name' => $request->input('lastName'),
            'street_address' => $request->input('streetAddress'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'zip' => $request->input('zip'),
            'phone_number' => $request->input('phoneNumber'),
            'about_me' => $request->input('aboutMe'),
        ]);

        return to_route('profile.edit');
    }


    public function avatarUpload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:15000',
        ]);


        if ($request->hasFile('image')) {

            try {
                $imageManager = new ImageManager(new Driver());
                $image = $imageManager->read($request->file('image')->getPathname());
                $resizedImage = $image->resizeDown(512, 512, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->toJpeg(90);
                $path = 'profile-picture/' . $request->user()->uuid . '/' . uniqid() . '.jpg';
                Storage::disk('s3')->put($path, (string)$resizedImage, 'public');
                $url = Storage::disk('s3')->url($path);
                AwsS3History::addHistory('profile-picture', $path, $url, $request->user()->id);
                if ($url) {
                    User::where('id', $request->user()->id)->update([
                        'profile_picture' => $url,
                    ]);

                    return response()->json(['success' => true, 'message' => 'Your profile picture has been updated!', 'url' => $url]);
                }
            } catch (\Exception $e) {
                AwsS3History::addHistory('profile-picture', null, null, $request->user()->id, $e->getMessage());
            }
            return response()->json(['success' => false, 'error' => 'Could not be upload your profile picture.']);
        }

        return response()->json(['error' => 'File could not be uploaded'], 400);
    }

    public function professionalEdit(Request $request): Response
    {
        return Inertia::render('settings/Professional', [
            'mustVerifyCompanyEmail' => $request->user()->company_email && !$request->user()->hasVerifiedCompanyEmail(),
            'status' => $request->session()->get('status'),
            'success' => $request->session()->get('success'),
            'error' => $request->session()->get('error'),
        ]);
    }

    public function professionalUpdate(Request $request): RedirectResponse
    {


        $validated = $request->validate([
            'companyName' => ['required', 'string', 'max:50'],
            'companyEmail' => ['required', 'email', 'string', 'max:50'],
            'brokerageName' => ['nullable', 'string', 'max:100'],
            'agentLicenseNumber' => ['nullable', 'string', 'max:50'],
        ]);

        $user = $request->user();

        $user->update([
            'company_name' => $request->input('companyName'),
            'company_email' => $request->input('companyEmail'),
            'brokerage_name' => $request->input('brokerageName'),
            'agent_license_number' => $request->input('agentLicenseNumber'),
        ]);

        return to_route('profile.professional.edit');
    }

    public function companyLogoUpload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:15000',
        ]);

        if ($request->hasFile('image')) {
            try {
                $imageManager = new ImageManager(new Driver());
                $image = $imageManager->read($request->file('image')->getPathname());
                $resizedImage = $image->resizeDown(512, 512, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->toJpeg(90);

                $path = 'company-logo/' . $request->user()->uuid . '/' . uniqid() . '.jpg';

                Storage::disk('s3')->put($path, (string)$resizedImage, 'public');

                $url = Storage::disk('s3')->url($path);
                AwsS3History::addHistory('company-logo', $path, $url, $request->user()->id);
                if ($url) {
                    User::where('id', $request->user()->id)->update([
                        'company_logo' => $url,
                    ]);

                    return response()->json(['success' => true, 'message' => 'Your company logo has been uploaded!', 'url' => $url]);
                }
            } catch (\Exception $e) {
                AwsS3History::addHistory('company-logo', null, null, $request->user()->id, $e->getMessage());
            }


            return response()->json(['success' => false, 'error' => 'Image could not be uploaded, Please try again!']);
        }


        return response()->json(['error' => 'File could not be uploaded'], 400);
    }


    public function emailEdit(Request $request): Response
    {
        return Inertia::render('settings/Email', [
            'emailCategories' => config('user_email_config.categories_description'),
            'mustVerifyEmail' => !$request->user()->hasVerifiedEmail(),
            'status' => $request->session()->get('status'),
        ]);
    }

    public function emailUpdate(Request $request): RedirectResponse
    {


        $validated = $request->validate([
            'global' => "nullable|boolean",
            'emailUnsubscribedListPreference' => "nullable|array",
            'emailUnsubscribedListPreference.*' => 'nullable|string'
        ]);

        $user = $request->user();

        $user->update([
            'email_unsubscribed_global' => $request->input('global'),
            'email_unsubscribed_list_preference' => $request->input('emailUnsubscribedListPreference'),
        ]);

        return to_route('profile.email.edit');
    }

    /**
     * Send company email verification notification.
     */
    public function sendCompanyEmailVerification(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (!$user->company_email) {
            return back()->with('error', 'No company email address found.');
        }

        if ($user->hasVerifiedCompanyEmail()) {
            return back()->with('success', 'Company email already verified.');
        }

        $this->sendCompanyEmailVerificationEmail($user);

        return back()->with('status', 'company-email-verification-sent')
                    ->with('success', 'Company email verification link sent!');
    }

    /**
     * Send company email verification email using EmailService
     */
    private function sendCompanyEmailVerificationEmail(User $user): bool
    {
        $verificationUrl = $user->getCompanyEmailVerificationUrl();


        return $this->emailService->sendCompanyEmailVerification(
            $user->company_email,
            $user->name,
            $verificationUrl
        );
    }

    public function buyBoxEdit(Request $request): Response
    {
        $buybox = BuyBox::where('user_id', $request->user()->id)->first();
        $propertyTypes = PropertyHelper::getComboboxPropertyTypes();
        $invStgy = PropertyHelper::getComboboxInvestStrategies();
        $counties = LocationHelper::getComboboxCounties();

        return Inertia::render('settings/BuyBox', [
            'buybox' => $buybox,
            'propertyTypes' => $propertyTypes,
            'invStgy' => $invStgy,
            'counties' => $counties,
            'mustVerifyEmail' => !$request->user()->hasVerifiedEmail(),
            'status' => $request->session()->get('status'),
        ]);
    }

    public function buyBoxUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'investment_strategy' => "nullable|array",
            'investment_strategy.*' => 'nullable|string',

            'counties_invest' => "nullable|array",
            'counties_invest.*' => 'nullable|string',

            'property_types' => "nullable|array",
            'property_types.*' => 'nullable|string',

            'arv_min' => "nullable|numeric|lte:arv_max",
            'arv_max' => "nullable|numeric|gte:arv_min",

            'bath_min' => "nullable|numeric|lte:bath_max",
            'bath_max' => "nullable|numeric|gte:bath_min",

            'bed_min' => "nullable|numeric|lte:bed_max",
            'bed_max' => "nullable|numeric|gte:bed_min",

            'cashflow_min' => "nullable|numeric|lte:cashflow_max",
            'cashflow_max' => "nullable|numeric|gte:cashflow_min",

            'delta_psf_min' => "nullable|numeric|lte:delta_psf_max",
            'delta_psf_max' => "nullable|numeric|gte:delta_psf_min",

            'flip_profit_min' => "nullable|numeric|lte:flip_profit_max",
            'flip_profit_max' => "nullable|numeric|gte:flip_profit_min",

            'price_min' => "nullable|numeric|lte:price_max",
            'price_max' => "nullable|numeric|gte:price_min",

            'sqft_min' => "nullable|numeric|lte:sqft_max",
            'sqft_max' => "nullable|numeric|gte:sqft_min",

            'year_build_min' => "nullable|numeric|lte:year_build_max",
            'year_build_max' => "nullable|numeric|gte:year_build_min",
        ]);

        $user = $request->user();

        BuyBox::updateOrCreate(
            ['user_id' => $user->id],
            [
                'investment_strategy' => $request->input('investment_strategy'),
                'counties_invest' => $request->input('counties_invest'),
                'property_types' => $request->input('property_types'),
                'arv_min' => $request->input('arv_min'),
                'arv_max' => $request->input('arv_max'),
                'bath_min' => $request->input('bath_min'),
                'bath_max' => $request->input('bath_max'),
                'bed_min' => $request->input('bed_min'),
                'bed_max' => $request->input('bed_max'),
                'cashflow_min' => $request->input('cashflow_min'),
                'cashflow_max' => $request->input('cashflow_max'),
                'delta_psf_min' => $request->input('delta_psf_min'),
                'delta_psf_max' => $request->input('delta_psf_max'),
                'flip_profit_min' => $request->input('flip_profit_min'),
                'flip_profit_max' => $request->input('flip_profit_max'),
                'price_min' => $request->input('price_min'),
                'price_max' => $request->input('price_max'),
                'sqft_min' => $request->input('sqft_min'),
                'sqft_max' => $request->input('sqft_max'),
                'year_build_min' => $request->input('year_build_min'),
                'year_build_max' => $request->input('year_build_max'),
            ]
        );
        return to_route('profile.buy_box.edit');
    }

    public function assetsUpload(Request $request)
    {
        $request->validate([
            'path' => "required|string",
            'file' => 'required|max:15000',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $request->input('path');
            try {
                $path = "assets/" . $path . '/';
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $fullPath = $path . $filename;

                Storage::disk('s3')->putFileAs($path, $file, $filename, 'public');

                $url = Storage::disk('s3')->url($fullPath);
                AwsS3History::addHistory('assets-files', $fullPath, $url, $request->user()->id);
                if ($url) {
                    return response()->json(['success' => true, 'message' => 'Image has been uploaded!', 'path' => $url]);
                }
            } catch (\Exception $e) {
                AwsS3History::addHistory('assets-files', null, null, $request->user()->id, $e->getMessage());
            }

            return response()->json(['success' => false, 'error' => 'Could not be upload your file.']);
        }

        return response()->json(['error' => 'File could not be uploaded'], 400);
    }


    /**
     * Delete the user's profile.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

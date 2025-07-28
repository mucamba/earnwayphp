<?php

namespace App\Http\Controllers\Backend;

use App\Enums\CustomCodeType;
use App\Models\CustomCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class AppController extends BaseController
{
    public static function permissions(): array
    {
        return [
            'appInfo'                         => 'app-info',
            'styleManager|styleManagerUpdate' => 'style-manager',
            'clearCache'                      => 'app-clear-cache',
            'optimize'                        => 'app-optimize',

        ];
    }

    public function appInfo()
    {
        $data = [
            'php_version'     => phpversion(),
            'laravel_version' => app()->version(),
            'server_software' => php_uname(),
            'environment'     => app()->environment(),
            'server_ip'       => $_SERVER['SERVER_ADDR'],
            'timezone'        => config('app.timezone'),
        ];

        return view('backend.app.info', compact('data'));
    }

    public function styleManager()
    {
        $css = CustomCode::ofType(CustomCodeType::CSS);

        return view('backend.app.style_manager', compact('css'));
    }

    public function styleManagerUpdate(Request $request)
    {
        $validated = $request->validate([
            'type'    => ['required', Rule::in(CustomCodeType::values())],
            'content' => 'nullable|string',
            'status'  => 'boolean',
        ]);

        CustomCode::updateOrCreate(
            ['type' => $validated['type']],
            [
                'content' => $validated['content'],
                'status'  => $request->boolean('status'),
            ]
        );

        notifyEvs('success', __('Style Manager Updated Successfully'));

        return redirect()->back();
    }

    public function optimize()
    {
        notifyEvs('success', __('Application Optimized Successfully'));
        Artisan::call('app:optimize');

        return redirect()->back();
    }

    public function clearCache()
    {
        notifyEvs('success', __('Cache Cleared Successfully'));
        Artisan::call('app:clear');

        return redirect()->back();
    }

    public function smtpConnectionCheck(Request $request)
    {
        try {
            // Try sending a test email to the authenticated email
            Mail::raw('SMTP Test Email - Connection Successful.', function ($message) use ($request) {
                $message->to($request->input('test_email', config('mail.from.address')))
                    ->subject('SMTP Test Email');
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'SMTP connection successful. Test email sent.',
            ]);
        } catch (\Exception $e) {
            Log::error('SMTP Test Failed: '.$e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'SMTP connection failed: '.$e->getMessage(),
            ], 500);
        }
    }
}

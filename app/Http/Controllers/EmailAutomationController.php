<?php

namespace App\Http\Controllers;

use App\Mail\AutomationMail;
use App\Models\EmailAutomation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EmailAutomationController extends Controller
{
    /**
     * Display the form and table of sent emails.
     */
    public function index(): View
    {
        $records = EmailAutomation::orderBy('created_at')->get();

        return view('email-automation.index', compact('records'));
    }

    /**
     * Store the form data, send email, and save to database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname'  => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email'],
            'message'   => ['required', 'string'],
        ], [
            'firstname.required' => 'First name is required.',
            'lastname.required'  => 'Last name is required.',
            'email.required'     => 'Email is required.',
            'email.email'        => 'Please enter a valid email address.',
            'message.required'   => 'Message is required.',
        ]);

        $record = EmailAutomation::create([
            'first_name' => $validated['firstname'],
            'last_name'  => $validated['lastname'],
            'email'      => $validated['email'],
            'message'    => $validated['message'],
        ]);

        try {
            Mail::to($validated['email'])->send(new AutomationMail(
                firstName: $validated['firstname'],
                lastName: $validated['lastname'],
                email: $validated['email'],
                messageContent: $validated['message']
            ));
        } catch (\Exception $e) {
            return back()->with('error', 'Email could not be sent: ' . $e->getMessage())->withInput();
        }

        return back()->with('success', 'Email sent successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Mail\PasswordChangedMail;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Throwable;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $user->load(['operatingUnit', 'roles']);

        return view('profile.show', compact('user'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'remove_avatar' => ['nullable', 'boolean'],
        ]);

        $oldValues = [
            'name' => $user->name,
            'designation' => $user->designation,
            'phone_number' => $user->phone_number,
            'avatar' => $user->avatar,
        ];

        $avatarPath = $user->avatar;

        if ($request->boolean('remove_avatar') && $avatarPath) {
            $fullPath = public_path($avatarPath);
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
            $avatarPath = null;
        }

        if ($request->hasFile('avatar')) {
            if ($avatarPath) {
                $oldFullPath = public_path($avatarPath);
                if (file_exists($oldFullPath)) {
                    @unlink($oldFullPath);
                }
            }

            $file = $request->file('avatar');
            $fileName = 'avatar-'.$user->id.'-'.time().'.'.$file->getClientOriginalExtension();
            $destinationDir = public_path('uploads/avatars');
            if (! is_dir($destinationDir)) {
                @mkdir($destinationDir, 0755, true);
            }
            $targetPath = $destinationDir.DIRECTORY_SEPARATOR.$fileName;
            copy($file->getRealPath(), $targetPath);
            $avatarPath = 'uploads/avatars/'.$fileName;
        }

        $user->update([
            'name' => $validated['name'],
            'designation' => $validated['designation'] ?? null,
            'phone_number' => $validated['phone_number'] ?? null,
            'avatar' => $avatarPath,
        ]);

        try {
            AuditLog::record(
                action: 'profile.updated',
                auditable: $user,
                oldValues: $oldValues,
                newValues: [
                    'name' => $user->name,
                    'designation' => $user->designation,
                    'phone_number' => $user->phone_number,
                    'avatar' => $user->avatar,
                ],
                request: $request,
                userId: $user->id
            );
        } catch (Throwable $e) {
            report($e);
        }

        return back()->with('status', 'Profile details updated successfully.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        /** @var User $user */
        $user = $request->user();

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        try {
            Mail::to($user->email)->send(new PasswordChangedMail($user, now()->format('F j, Y g:i A'), $request->ip()));
        } catch (Throwable $e) {
            report($e);
        }

        return back()->with('status', 'Password updated successfully');
    }
}

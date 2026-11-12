<?php
//ProfileController.php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use Illuminate\Contracts\View\View;
class ProfileController extends Controller
{
       public function show(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $user->load(['operatingUnit', 'roles']);

        return view('profile.show', compact('user'));
    }
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);
        return back()->with('status', 'Password updated successfully');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Intern;
use App\Models\Supervisor;
use App\Models\Notification;
use App\Notifications\NewInternRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // First check if the user exists
        $user = User::where('email', $credentials['email'])->first();
        
        if ($user && Hash::check($credentials['password'], $user->password)) {
            // Check if user is intern with pending status
            if ($user->isIntern() && $user->intern && $user->intern->status === 'pending') {
                return back()->withErrors([
                    'email' => 'Akun Anda masih menunggu persetujuan admin. Silakan tunggu konfirmasi.',
                ])->onlyInput('email');
            }

            // Check if user is pembimbing with pending status
            if ($user->isPembimbing() && $user->supervisor && $user->supervisor->status === 'pending') {
                return back()->withErrors([
                    'email' => 'Akun Anda masih menunggu persetujuan admin. Silakan tunggu konfirmasi.',
                ])->onlyInput('email');
            }
            
            // Proceed with login
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        $supervisors = Supervisor::where('status', 'active')->with('user')->get();
        return view('auth.register', compact('supervisors'));
    }

    public function register(Request $request)
    {
        $role = $request->input('role', 'intern');

        // Base validation rules
        $rules = [
            'role' => 'required|in:intern,pembimbing',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ];

        // Role-specific validation
        if ($role === 'intern') {
            $rules = array_merge($rules, [
                'nis' => 'nullable|string|max:50',
                'school' => 'required|string|max:255',
                'department' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'supervisor_id' => 'nullable|exists:users,id',
            ]);
        } else {
            $rules = array_merge($rules, [
                'nip' => 'nullable|string|max:50',
                'institution' => 'required|string|max:255',
                'supervisor_phone' => 'nullable|string|max:20',
                'supervisor_address' => 'nullable|string',
            ]);
        }

        $validated = $request->validate($rules);

        DB::transaction(function() use ($validated, $role) {
            // Create user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $role,
            ]);

            if ($role === 'intern') {
                // Create intern record with pending status
                Intern::create([
                    'user_id' => $user->id,
                    'nis' => $validated['nis'] ?? null,
                    'school' => $validated['school'],
                    'department' => $validated['department'],
                    'phone' => $validated['phone'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                    'supervisor_id' => $validated['supervisor_id'] ?? null,
                    'status' => 'pending',
                ]);

                $notifMessage = $user->name . ' telah mendaftar sebagai peserta magang dari ' . $validated['school'] . ' jurusan ' . $validated['department'] . '.';
                $notifLink = '/interns?status=pending';
                $notifType = 'new_intern_registration';
                $notifTitle = 'Pendaftaran Magang Baru';
            } else {
                // Create supervisor record with pending status
                Supervisor::create([
                    'user_id' => $user->id,
                    'nip' => $validated['nip'] ?? null,
                    'institution' => $validated['institution'],
                    'phone' => $validated['supervisor_phone'] ?? null,
                    'address' => $validated['supervisor_address'] ?? null,
                    'status' => 'pending',
                ]);

                $notifMessage = $user->name . ' telah mendaftar sebagai pembimbing dari ' . $validated['institution'] . '.';
                $notifLink = '/supervisors?status=pending';
                $notifType = 'new_supervisor_registration';
                $notifTitle = 'Pendaftaran Pembimbing Baru';
            }

            // Notify all admins about new registration
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                // Email notification (only for intern for now)
                if ($role === 'intern') {
                    $admin->notify(new NewInternRegistration($user));
                }
                
                // In-app notification
                Notification::notify(
                    $admin->id,
                    $notifType,
                    $notifTitle,
                    $notifMessage,
                    $notifLink,
                    ['user_id' => $user->id]
                );
            }
        });

        return redirect()->route('login')->with('success', 
            'Pendaftaran berhasil! Akun Anda sedang menunggu persetujuan admin. Anda akan dihubungi setelah akun diaktifkan.'
        );
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

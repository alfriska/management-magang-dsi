<?php

namespace App\Livewire\Supervisors;

use App\Models\User;
use App\Models\Supervisor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
class SupervisorForm extends Component
{
    public ?Supervisor $supervisorModel = null;

    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $nip = '';
    public $phone = '';
    public $address = '';
    public $institution = '';

    public $isEditing = false;
    public $pageTitle = 'Tambah Pembimbing';

    protected function rules()
    {
        $emailRule = 'required|email|unique:users,email';
        if ($this->isEditing) {
            $emailRule = 'required|email|unique:users,email,' . $this->supervisorModel->user_id;
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => $emailRule,
            'nip' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'institution' => 'nullable|string|max:255',
        ];

        if (!$this->isEditing) {
            $rules['password'] = 'required|string|min:8|confirmed';
        } else {
            $rules['password'] = 'nullable|string|min:8|confirmed';
        }

        return $rules;
    }

    protected $messages = [
        'name.required' => 'Nama wajib diisi.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email sudah digunakan.',
        'password.required' => 'Password wajib diisi.',
        'password.min' => 'Password minimal 8 karakter.',
        'password.confirmed' => 'Konfirmasi password tidak cocok.',
    ];

    public function mount(?Supervisor $supervisor = null)
    {
        if ($supervisor && $supervisor->exists) {
            $this->supervisorModel = $supervisor;
            $this->isEditing = true;
            $this->name = $supervisor->user->name;
            $this->email = $supervisor->user->email;
            $this->nip = $supervisor->nip ?? '';
            $this->phone = $supervisor->phone ?? '';
            $this->address = $supervisor->address ?? '';
            $this->institution = $supervisor->institution ?? '';
            $this->pageTitle = 'Edit Pembimbing';
        }
    }

    public function save()
    {
        $validated = $this->validate();

        DB::transaction(function() use ($validated) {
            if ($this->isEditing) {
                // Update user
                $userData = [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                ];
                if (!empty($validated['password'])) {
                    $userData['password'] = Hash::make($validated['password']);
                }
                $this->supervisorModel->user->update($userData);

                // Update supervisor
                $this->supervisorModel->update([
                    'nip' => $validated['nip'],
                    'phone' => $validated['phone'],
                    'address' => $validated['address'],
                    'institution' => $validated['institution'],
                ]);

                $message = 'Data pembimbing berhasil diperbarui!';
            } else {
                // Create user
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role' => 'pembimbing',
                ]);

                // Create supervisor with active status (created by admin)
                Supervisor::create([
                    'user_id' => $user->id,
                    'nip' => $validated['nip'],
                    'phone' => $validated['phone'],
                    'address' => $validated['address'],
                    'institution' => $validated['institution'],
                    'status' => 'active',
                ]);

                $message = 'Pembimbing berhasil ditambahkan!';
            }

            session()->flash('success', $message);
        });

        return redirect()->route('supervisors.index');
    }

    #[Title('Form Pembimbing')]
    public function render()
    {
        return view('livewire.supervisors.supervisor-form', [
            'pageTitle' => $this->pageTitle,
        ]);
    }
}

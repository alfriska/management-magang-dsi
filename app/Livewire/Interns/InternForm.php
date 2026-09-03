<?php

namespace App\Livewire\Interns;

use App\Models\Intern;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
class InternForm extends Component
{
    public ?Intern $intern = null;

    // User fields
    public $name = '';
    public $email = '';
    public $password = '';

    // Intern fields
    public $nis = '';
    public $school = '';
    public $department = '';
    public $phone = '';
    public $address = '';
    public $start_date = '';
    public $end_date = '';
    public $supervisor_id = '';
    public $status = 'active';

    public $isEditing = false;
    public $pageTitle = 'Tambah Anggota Magang';

    protected function rules()
    {
        $emailRule = 'required|email|unique:users,email';
        if ($this->isEditing) {
            $emailRule = 'required|email|unique:users,email,' . $this->intern->user_id;
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => $emailRule,
            'nis' => 'nullable|string|max:50',
            'school' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'supervisor_id' => 'nullable|exists:users,id',
        ];

        if (!$this->isEditing) {
            $rules['password'] = 'required|string|min:8';
        }

        if ($this->isEditing) {
            $rules['status'] = 'required|in:active,completed,cancelled';
        }

        return $rules;
    }

    public function mount(?Intern $intern = null)
    {
        if ($intern && $intern->exists) {
            $this->intern = $intern;
            $this->isEditing = true;

            $this->name = $intern->user->name;
            $this->email = $intern->user->email;
            $this->nis = $intern->nis ?? '';
            $this->school = $intern->school;
            $this->department = $intern->department;
            $this->phone = $intern->phone ?? '';
            $this->address = $intern->address ?? '';
            $this->start_date = $intern->start_date->format('Y-m-d');
            $this->end_date = $intern->end_date->format('Y-m-d');
            $this->supervisor_id = $intern->supervisor_id ?? '';
            $this->status = $intern->status;

            $this->pageTitle = 'Edit Anggota Magang';
        }
    }

    public function save()
    {
        $validated = $this->validate();

        DB::transaction(function() use ($validated) {
            if ($this->isEditing) {
                $this->intern->user->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                ]);

                $this->intern->update([
                    'nis' => $validated['nis'],
                    'school' => $validated['school'],
                    'department' => $validated['department'],
                    'phone' => $validated['phone'],
                    'address' => $validated['address'],
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                    'supervisor_id' => $validated['supervisor_id'] ?: null,
                    'status' => $validated['status'],
                ]);
            } else {
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role' => 'intern',
                ]);

                Intern::create([
                    'user_id' => $user->id,
                    'nis' => $validated['nis'],
                    'school' => $validated['school'],
                    'department' => $validated['department'],
                    'phone' => $validated['phone'],
                    'address' => $validated['address'],
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                    'supervisor_id' => $validated['supervisor_id'] ?: null,
                    'status' => 'active',
                ]);
            }
        });

        $message = $this->isEditing
            ? 'Data anggota magang berhasil diperbarui!'
            : 'Anggota magang berhasil ditambahkan!';

        session()->flash('success', $message);

        return redirect()->route('interns.index');
    }

    #[Title('Form Anggota Magang')]
    public function render()
    {
        $supervisors = User::whereIn('role', ['admin', 'pembimbing'])->get();

        return view('livewire.interns.intern-form', [
            'supervisors' => $supervisors,
            'interns' => $this->intern,
            'pageTitle' => $this->pageTitle,
        ]);
    }
}

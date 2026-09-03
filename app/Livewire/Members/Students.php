<?php

namespace App\Livewire\Members;

use App\Imports\StudentsImport;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Students extends Component
{
    use WithFileUploads, WithPagination;

    public $showModal = false;

    public $editing = null;

    public $search = '';

    public $name;

    public $email;

    public $password;

    public $nis;

    public $nisn;

    public $gender = 'L';

    public $birth_date;

    public $class_id;

    public $phone;

    public $address;

    public $importFile;

    public function import()
    {
        Gate::authorize('siswa.import');
        $this->validate(['importFile' => 'required|file|mimes:xlsx,xls,csv|max:4096']);
        $import = new StudentsImport;
        Excel::import($import, $this->importFile->getRealPath());
        $msg = 'Import selesai.';
        if ($import->failed) {
            $msg .= ' '.count($import->failed).' baris gagal.';
        }
        $this->reset('importFile');
        $this->dispatch('notify', ['message' => $msg]);
    }

    public function create()
    {
        Gate::authorize('siswa.create');
        $this->reset('editing', 'name', 'email', 'password', 'nis', 'nisn', 'birth_date', 'class_id', 'phone', 'address');
        $this->gender = 'L';
        $this->showModal = true;
    }

    public function edit(Student $student)
    {
        Gate::authorize('siswa.edit');
        $this->editing = $student->id;
        $this->name = $student->user->name;
        $this->email = $student->user->email;
        $this->password = '';
        $this->phone = $student->user->phone;
        $this->fill($student->only('nis', 'nisn', 'gender', 'birth_date', 'class_id', 'address'));
        $this->showModal = true;
    }

    public function save()
    {
        Gate::authorize($this->editing ? 'siswa.edit' : 'siswa.create');
        $id = $this->editing ? Student::findOrFail($this->editing)->user_id : null;
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => $this->editing ? 'nullable|string|min:6' : 'required|string|min:6',
            'nis' => 'nullable|string|max:30|unique:students,nis,'.$this->editing,
            'nisn' => 'nullable|string|max:20|unique:students,nisn,'.$this->editing,
            'class_id' => 'nullable|exists:classes,id',
            'gender' => 'required|in:L,P',
        ]);

        $user = $this->editing ? Student::findOrFail($this->editing)->user : new User;
        $user->name = $this->name;
        $user->email = $this->email;
        $user->phone = $this->phone;
        if ($this->password) {
            $user->password = $this->password;
        }
        $user->save();

        if ($this->editing) {
            $student = Student::findOrFail($this->editing);
        } else {
            $student = new Student(['user_id' => $user->id]);
            $user->assignRole('Siswa');
        }
        $student->fill($this->only('nis', 'nisn', 'gender', 'birth_date', 'class_id', 'address'));
        $student->save();

        $this->showModal = false;
        $this->dispatch('notify', ['message' => 'Siswa disimpan.']);
    }

    public function delete(Student $student)
    {
        Gate::authorize('siswa.delete');
        $student->user->delete();
        $this->dispatch('notify', ['message' => 'Siswa dihapus.']);
    }

    public function render()
    {
        return view('livewire.members.students', [
            'students' => Student::with('user', 'classRoom')
                ->when($this->search, fn ($q) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%")))
                ->orderByDesc('id')->paginate(10),
            'classes' => ClassRoom::orderBy('name')->get(),
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@quiz.test'],
            [
                'name'        => 'Platform Admin',
                'password'    => Hash::make('password'),
                'role'        => User::ROLE_ADMIN,
                'is_approved' => true,
                'country'     => 'Pakistan',
                'bio'         => 'System administrator',
            ]
        );
        $admin->assignRole('admin');

        $teacher = User::updateOrCreate(
            ['email' => 'teacher@quiz.test'],
            [
                'name'        => 'Dr. Mirza Adnan',
                'password'    => Hash::make('password'),
                'role'        => User::ROLE_TEACHER,
                'is_approved' => true,
                'country'     => 'Pakistan',
                'bio'         => 'Senior instructor in Computer Science.',
            ]
        );
        $teacher->assignRole('teacher');

        $pendingTeacher = User::updateOrCreate(
            ['email' => 'pending.teacher@quiz.test'],
            [
                'name'        => 'Awaiting Approval',
                'password'    => Hash::make('password'),
                'role'        => User::ROLE_TEACHER,
                'is_approved' => false,
                'country'     => 'Pakistan',
                'bio'         => 'Recently registered teacher awaiting admin approval.',
            ]
        );
        $pendingTeacher->assignRole('teacher');

        $student = User::updateOrCreate(
            ['email' => 'student@quiz.test'],
            [
                'name'        => 'Haseeb Ur Rehman',
                'password'    => Hash::make('password'),
                'role'        => User::ROLE_STUDENT,
                'is_approved' => true,
                'country'     => 'Pakistan',
                'bio'         => 'BSIT student exploring AI-assisted learning.',
            ]
        );
        $student->assignRole('student');
    }
}

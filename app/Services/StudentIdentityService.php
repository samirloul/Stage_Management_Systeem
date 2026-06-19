<?php

namespace App\Services;

use App\Models\Student;

class StudentIdentityService
{
    private const MIN_NUMBER = 10001;

    public function nextStudentNumber(): string
    {
        // We lezen alle bestaande studentnummers in en halen alleen het numerieke deel eruit.
        $usedNumbers = Student::query()
            ->pluck('student_number')
            ->map(fn (string $number): int => (int) preg_replace('/\D+/', '', $number))
            ->filter(fn (int $number): bool => $number >= self::MIN_NUMBER)
            ->sort()
            ->values()
            ->all();

        // Het algoritme zoekt het eerste ontbrekende nummer (gap reuse), bv. S10002 als die is verwijderd.
        $expected = self::MIN_NUMBER;

        foreach ($usedNumbers as $usedNumber) {
            if ($usedNumber > $expected) {
                break;
            }

            if ($usedNumber === $expected) {
                $expected++;
            }
        }

        return 'S'.$expected;
    }

    public function emailFromStudentNumber(string $studentNumber): string
    {
        // Zakelijke en voorspelbare e-mailopbouw voor demo/schoolcontext.
        return $studentNumber.'@student.local';
    }
}

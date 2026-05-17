<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryScale extends Model
{
    protected $fillable = ['grade', 'mkg', 'amount'];

    public static function getAmount($education, $years)
    {
        if (!$education) return 0;
        
        $edu = strtoupper($education);
        $grade = 'IV';
        
        if (str_contains($edu, 'SD') || str_contains($edu, 'SMP') || str_contains($edu, 'SLTP')) $grade = 'I';
        elseif (str_contains($edu, 'SMA') || str_contains($edu, 'SMK') || str_contains($edu, 'SLTA')) $grade = 'II';
        elseif (str_contains($edu, 'D1') || str_contains($edu, 'D2') || str_contains($edu, 'D3')) $grade = 'III';
        elseif (str_contains($edu, 'D4') || str_contains($edu, 'S1')) $grade = 'IV';
        elseif (str_contains($edu, 'S2')) $grade = 'V';
        elseif (str_contains($edu, 'S3')) $grade = 'VI';

        $scale = self::where('grade', $grade)
            ->where('mkg', '<=', (int)$years)
            ->orderByDesc('mkg')
            ->first();

        return $scale ? (float)$scale->amount : 0;
    }
}

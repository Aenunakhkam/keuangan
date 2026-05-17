<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryDeduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'salary_id',
        'teacher_id',
        'spj_netto',
        'tab_khusus',
        'simpanan_wajib',
        'simpanan_sukarela',
        'angsuran_koperasi',
        'jumlah_koperasi',
        'dplk_slawi',
        'dplk_kemantran',
        'pinjaman_bpd_jateng',
        'jumlah_bpd',
        'bank_tgr',
        'premi_bpjs_anggota',
        'dansos',
        'lainnya_1',
        'lainnya_2',
        'denda_fingerprint',
        'jumlah_potongan',
        'gaji_bersih',
    ];

    public function salary()
    {
        return $this->belongsTo(Salary::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Boot function to hook into model events.
     * Calculate auto fields before saving.
     */
    protected static function booted()
    {
        static::saving(function ($deduction) {
            // Auto Calculate Tab Khusus (8% dari spj_netto, round to nearest integer)
            $deduction->tab_khusus = round($deduction->spj_netto * 0.08);

            // Calculate Sub-Totals
            $deduction->jumlah_koperasi = 
                $deduction->tab_khusus + 
                $deduction->simpanan_wajib + 
                $deduction->simpanan_sukarela + 
                $deduction->angsuran_koperasi;

            $deduction->jumlah_bpd = 
                $deduction->dplk_slawi + 
                $deduction->dplk_kemantran + 
                $deduction->pinjaman_bpd_jateng;

            // Calculate Grand Total Potongan
            $deduction->jumlah_potongan = 
                $deduction->jumlah_koperasi + 
                $deduction->jumlah_bpd + 
                $deduction->bank_tgr + 
                $deduction->premi_bpjs_anggota + 
                $deduction->dansos + 
                $deduction->lainnya_1 + 
                $deduction->lainnya_2 + 
                $deduction->denda_fingerprint;

            // Calculate Take Home Pay
            $deduction->gaji_bersih = $deduction->spj_netto - $deduction->jumlah_potongan;
        });
    }
}

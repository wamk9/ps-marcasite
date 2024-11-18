<?php

namespace App\Exports;

use App\Models\Course;
use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;


class PaymentExport implements FromCollection
{
    protected $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $course = Course::select(['payments.id AS register_num', 'users.name'])
        ->leftJoin('payments', 'courses.id', '=', 'payments.course_id')
        ->leftJoin('users', 'users.id', '=', 'payments.user_id')
        ->where([['courses.id', $this->id],['payments.status', 'approved']])->get();

        return $course;
    }
}

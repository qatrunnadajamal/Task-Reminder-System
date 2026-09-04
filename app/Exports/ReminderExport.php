<?php

namespace App\Exports;
use App\Models\Reminder;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;

class ReminderExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        return Reminder::where('user_id', Auth::id())
        ->get(['user_id',
        'title',
        'description',
        'due_task',
        'difficulty',
        'priority_level',
        'status',
        'created_at',
        'updated_at',]);
    }
}

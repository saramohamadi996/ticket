<?php

namespace App\Models;

use App\Policies\TicketPolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Ticket extends Model
{
    use HasFactory, HasRoles;

    protected $fillable = ['user_id', 'employee_id', 'title', 'content', 'status', 'priority'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}

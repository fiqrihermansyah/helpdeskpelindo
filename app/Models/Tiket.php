<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tiket extends Model
{
    use HasFactory;

    protected $table = 'tiket';

    protected $fillable = [
        'judul',
        'pengaju',
        'aplikasi',
        'deskripsi',
        'prioritas_id',
        'divisi_id',
        'user_id',
        'status_id',
        'file_id',
        'ticket_id'
    ];

    public static function generateTicketId($user)
    {
        if (is_null($user)) {
            throw new \Exception('User is null in generateTicketId');
        }

        $date = date('ymd'); // Get the current date in the format YYMMDD
        $userId = $user->id; // Get the user's ID

        // Get the last ticket created by the user today
        $lastTicket = self::where('user_id', $userId)
            ->whereDate('created_at', date('Y-m-d'))
            ->orderBy('created_at', 'desc')
            ->first();

        // If the user has created a ticket today, increment the last number, otherwise start at 1
        $lastNumber = $lastTicket ? (int) substr($lastTicket->ticket_id, -1) + 1 : 1;

        // Generate the ticket ID
        $ticket_id = $userId . $date . str_pad($lastNumber, 3, '0', STR_PAD_LEFT);

        return $ticket_id;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function prioritas()
    {
        return $this->belongsTo(Prioritas::class, 'prioritas_id');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id');
    }

    public function balasan()
    {
        return $this->hasMany(Reply::class, 'tiket_id');
    }

    public function files()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    public $timestamps = true;
}

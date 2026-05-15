<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $table = 'contact_messages';

    protected $primaryKey = 'id_contact_message';

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
    ];
}

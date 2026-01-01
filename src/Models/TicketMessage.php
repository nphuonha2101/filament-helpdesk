<?php

namespace Nphuonha\FilamentHelpdesk\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketMessage extends Model
{
    use HasFactory;

    protected $table = 'helpdesk_messages';

    protected $fillable = [
        'ticket_id',
        'user_id',
        'body',
        'attachments',
        'is_admin_reply',
        'message_id',
        'email_sent',
        'email_error',
        'email_sent_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_admin_reply' => 'boolean',
        'email_sent' => 'boolean',
        'email_sent_at' => 'datetime',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('filament-helpdesk.user_model', \App\Models\User::class));
    }
}

<?php

namespace Nphuonha\FilamentHelpdesk\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'helpdesk_tickets';

    protected $fillable = [
        'uuid',
        'user_id',
        'email',
        'subject',
        'status',
        'priority',
    ];

    protected $casts = [
        'status' => \Nphuonha\FilamentHelpdesk\Enums\TicketStatus::class,
        'priority' => \Nphuonha\FilamentHelpdesk\Enums\TicketPriority::class,
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model', \App\Models\User::class));
    }
}

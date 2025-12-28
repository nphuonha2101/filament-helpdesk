<?php

namespace Nphuonha\FilamentHelpdesk\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $table = 'helpdesk_templates';

    protected $fillable = [
        'name',
        'subject_template',
        'body_template',
    ];
}

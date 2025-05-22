<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JiraIssue extends Model
{
    protected $fillable = [
        'issue_id',
        'project_id',
        'project_key',
        'issue_key',
        'summary',
        'raw_data',
    ];
}

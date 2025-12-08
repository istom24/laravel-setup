<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchedulerLog extends Model
{
    use HasFactory;

    protected $fillable = ['command', 'status', 'output'];

    public static function add(string $command, string $status, string $output = null): self
    {
        return self::create([
            'command' => $command,
            'status' => $status,
            'output' => $output,
        ]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;

    protected $table = "documento";

    protected $primaryKey = "id";

    protected $fillable = [
        'id',
        'nome',
        'documento',
        'extensao',
        'path',
        'descricao',
        'created_at',
        'updated_at',
    ];
}

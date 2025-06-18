<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class Catalogo extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'id' => 'string'
    ];

    public const CREATED_AT = 'FechaCreacion';
    public const UPDATED_AT = 'UltimaActualizacion';

    protected $fillable = [
        'Cve',
        'Nombre',
        'Descripcion',
        'CreadoPor',
        'ModificadoPor',
        'EliminadoPor'
    ];

    // Habilitar timestamps personalizados
    public $timestamps = true;

    // Si usas soft deletes con un campo personalizado
    protected $dates = ['FechaCreacion', 'UltimaActualizacion'];

    // Valores predeterminados para atributos
    protected $attributes = [
        'deleted' => 0, // Valor predeterminado para el campo 'deleted'
    ];

    public function __construct(array $attributes = [], $table = null)
    {
        parent::__construct($attributes);
        if (!is_null($table)) {
            $this->setTable($table); // Método de Eloquent para establecer el nombre de la tabla
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = (string) Uuid::uuid4();
            }
        });
    }
}

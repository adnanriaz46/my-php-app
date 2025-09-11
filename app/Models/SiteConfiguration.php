<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteConfiguration extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    /**
     * Cast the value attribute based on the type column.
     */
    protected function casts(): array
    {
        return [
            'value' => 'string', // Default cast; overridden dynamically in accessor
        ];
    }

    /**
     * Get the casted value based on the type column.
     */
    public function getValueAttribute($value)
    {
        switch ($this->type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'json':
                return json_decode($value, true);
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            default:
                return $value;
        }
    }

    /**
     * Set the value based on type.
     */
    public function setValueAttribute($value)
    {
        if (is_array($value) || is_object($value)) {
            $this->attributes['value'] = json_encode($value);
            $this->attributes['type'] = 'json';
        } else {
            $this->attributes['value'] = (string) $value;
        }
    }
}

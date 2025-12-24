<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppData extends Model
{
    use HasFactory;

    protected $table = 'app_data';

    protected $fillable = ['key', 'type', 'value'];

    protected $appends = ['full_value'];

    /**
     * Accessor for 'full_value' attribute.
     *
     * This method formats the 'value' based on the 'type' of the AppData.
     *
     * @return mixed
     */
    public function getFullValueAttribute()
    {
        switch ($this->type) {
            case 'Image':
                return $this->getFullImagePath($this->value);

            case 'Repeater Image':
                $imagePaths = json_decode($this->value, true) ?? [];
                return array_map(function ($path) {
                    return $this->getFullImagePath($path);
                }, $imagePaths);

            case 'Repeater Text':
                return json_decode($this->value, true) ?? [];

            default:
                return $this->value;
        }
    }

    /**
     * Helper method to get the full URL of an image.
     *
     * @param string $path
     * @return string
     */
    private function getFullImagePath($path)
    {
        if (!$path) {
            return null;
        }

        if (\Storage::disk('public')->exists($path)) {
            return \Storage::disk('public')->url($path);
        }

        return null;
    }
}

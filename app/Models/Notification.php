<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'message',
        'icon_url',
        'is_active',
        'user_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    const IS_ACTIVE = true;
    const IS_INACTIVE = false;

    const STATUS_ARRAY = [
        self::IS_ACTIVE => 'Active',
        self::IS_INACTIVE => 'Inactive',
    ];

    const STATUS_BTN_ARRAY = [
        self::IS_ACTIVE => 'status bg-success',
        self::IS_INACTIVE => 'status bg-danger',
    ];

    protected $appends = [
        'status_text',
        'status_class',
        'full_icon_url',
    ];

    public function getStatusTextAttribute(): string
    {
        return self::STATUS_ARRAY[$this->is_active] ?? '';
    }

    public function getStatusClassAttribute(): string
    {
        return self::STATUS_BTN_ARRAY[$this->is_active] ?? '';
    }

    public function getFullIconUrlAttribute(): ?string
    {
        if ($this->icon_url) {
            return asset('storage/' . $this->icon_url);
        }
        return null;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Carbon\Carbon;

class User extends Authenticatable
{
	use SoftDeletes;

	const STATUS_ACTIVE = 1;
	const STATUS_BANNED = 2;
	const STATUS_INACTIVE = 0;
	const STATUSES = [
		1 => 'Active',
		0 => 'Inactive',
		2 => 'Banned'
	];

	/** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    const ROLE_ADMIN = 'Admin';

    const INITIAL_RUNNING_NUMBER_UNIQUE_ID = 25;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
		'display_name',
        'status',
        'email',
        'username',
        'password',
        'verify_code',
        'verification_expires_at',
        'verified',
        'profile_photo_path',
        'phone',
        'dob',
        'address',
        'postal_code',
        'point',
		'activated_at',
		'is_old_user',
		'can_order_pickup'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        // 'status' => 'boolean',
		'point' => 'integer',
		'can_order_pickup' => 'boolean'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'status_text',
        'unique_id',
		'qrcode_content',
	];


	public function decodeQrcodeContent() {
		$code = strtr($this->qrcode_content, '-_', '+/');
		$realValue = base64_decode($code);
		return $realValue;
	}

	public function getQrcodeContentAttribute()
	{
	    return rtrim(strtr(base64_encode($this->id), '+/', '-_'), '=');
	}

	public function getNameAttribute($value) {
		if (!$value) {
			return $this->first_name . ' ' . $this->last_name;
		}
		return $value;
	}

    public function secondaryEmail()
    {
        return $this->hasMany(UserSecondaryEmail::class)->select(['user_id', 'email']);;
    }

    public function getUniqueIdAttribute()
    {
        if ($this->hasRole('School') || $this->hasRole('Enterprise')) {
            return $this->username;
        }
        return null;
    }

    public function getStatusTextAttribute()
    {
		return $this->status ? self::STATUSES[$this->status] : '';
        // return $this->status == 1 ? 'Active' : 'Inactive';
    }

    public function getProfilePhotoUrlAttribute()
    {
        $path = $this->profile_photo_path;

        // Check if the profile_photo_path is a full URL
        if ($path && filter_var($path, FILTER_VALIDATE_URL)) {
            return $path; // Return as is if it's a full URL
        }

        // Otherwise, treat it as a relative path and prepend it with asset()
        if ($path) {
            return asset($path);
        }

        return '';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Get all usermeta
     *
     * @return array<string, string>
     */

    public function usermeta()
    {
        return $this->hasMany(UserMeta::class, 'user_id')->select(['user_id', 'meta_key', 'meta_value']);
    }

    public function getMetaValue($key)
    {
        return UserMeta::where('user_id', $this->id)->where('meta_key', $key)->value('meta_value');
    }

    public function updateMeta($key, $value)
    {
        return UserMeta::updateOrCreate(
            ['user_id' => $this->id, 'meta_key' => $key],
            ['meta_value' => $value]
        );
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public static function generateUniqueID($userType)
    {
        $userType = strtolower($userType);
        $prefix = $userType === 'school' ? 'sch' : 'etpr';
        $initialNumber = self::INITIAL_RUNNING_NUMBER_UNIQUE_ID;
        $padLength = 3;

		$lastUser = self::withTrashed()
		    ->role(ucfirst($userType))
		    ->latest('id')
		    ->first();
        if (!$lastUser || !$lastUser->unique_id) {
            return $prefix . str_pad($initialNumber, $padLength, '0', STR_PAD_LEFT);
        }

        $lastUniqueID = $lastUser->unique_id;
        if (preg_match('/\d+$/', $lastUniqueID, $matches)) {
            $number = (int)$matches[0] + 1;
        } else {
            $number = $initialNumber;
        }

        return $prefix . str_pad($number, $padLength, '0', STR_PAD_LEFT);
    }

    public function joinedEvents()
    {
        return $this->belongsToMany(Event::class, 'user_joined_events', 'user_id', 'event_id');
    }

    public function hasJoinedThisEvent($eventId)
    {
        $hasJoinedEvent = UserJoinedEvent::where([
            'user_id' => auth()->user()->id,
            'event_id' => $eventId
        ])->first();

        if ($hasJoinedEvent) {
            return true;
        }
        return false;
    }

    public function rewards()
    {
        return $this->hasMany(UserReward::class);
    }

	public function recyclings()
	{
	    return $this->hasMany(Recycling::class);
	}

	public function todayRecycling()
	{
	    return $this->recyclings()
	        ->whereDate('created_at', Carbon::today())
	        ->get();
	}

	public function bins() {
		return $this->hasMany(Bin::class, 'organization_id'); //organization_id = school or enterprise
	}

	public function bannedLists() {
		return $this->hasMany(BannedList::class);
	}
}

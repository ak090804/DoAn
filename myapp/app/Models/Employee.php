<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Employee extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user_id',
		'name',
		'email',
		'phone',
		'address',
		'position',
		'salary',
		'hired_at',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array
	 */
	protected $casts = [
		'hired_at' => 'datetime',
		'salary' => 'decimal:2',
	];

	/**
	 * The user account associated with the employee (optional).
	 */
	public function user()
	{
		return $this->belongsTo(User::class);
	}
}

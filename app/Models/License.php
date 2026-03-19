<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $fillable = ['machine_id', 'license_key', 'is_active'];
}
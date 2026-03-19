<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TreasuryAdjustment extends Model
{
    protected $fillable = ['type', 'amount', 'notes'];
}
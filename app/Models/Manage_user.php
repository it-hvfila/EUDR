<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manage_user extends Model
{
    use HasFactory;
    protected $connection = 'mysql2'; // ระบุการเชื่อมต่อฐานข้อมูล
    protected $table = 'users'; // ชื่อตาราง
    // public $timestamps = false; // ถ้าตารางไม่มีฟิลด์ created_at และ updated_at

    // protected $fillable = ['level', 'approver']; // ฟิลด์ที่อนุญาตให้มีกาอัปเดต
}

<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'name', 'email', 'password', 'username', 'about', 'birthdate', 'photo',
      'score', 'blocked', 'type', 'entry_year', 'google_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
      'password', 'reme,ber_token',
  ];

    public function isAdmin() {
      return $this->type == 'Admin';
    }

    public function isTeacher() {
      return $this->type == 'Teacher';
    }

    public function isStudent() {
      return $this->type == 'Student';
    }

    /**
     * The interventions this user is author of.
     */
    public function interventions() {
      return $this->hasMany('App\Models\Intervention', 'id_author');
    }

    /**
     * The ucs this user follows. Only for students.
     */
    public function follows() {
      return $this->belongsToMany('App\Models\Uc', 'follow_uc', 'id_student', 'id_uc');
    }

    /**
     * The ucs this user is responsible for. Only for teachers.
     */
    public function teaches() {
      return $this->belongsToMany('App\Models\Uc', 'teacher_uc', 'id_teacher', 'id_uc');
    }

    /**
     * The votes this user is associated with.
     */
    public function votes() {
      return $this->belongsToMany('App\Models\Intervention', 'voting', 'id_user', 'id_intervention')
                  ->withPivot('vote');
    }

    /**
    * The interventions validated by this user. Only for teachers.
    */
    public function validates() {
      return $this->belongsToMany('App\Models\Intervention', 'validation', 'id_teacher', 'id_answer')
                  ->withPivot('valid');
    }

    /**
    * The notifications that belongs to this user.
    */
    public function notifications() {
      return $this->belongsToMany('App\Models\Notification', 'receive_not', 'id_user', 'id_notification')
                  ->withPivot('read');
    }

    /**
     * Filter query by Admin type.
     */
    public function scopeAdmins($query) {
        return $query->whereType('Admin');
    }

    /**
     * Filter query by Teacher type.
     */
    public function scopeTeachers($query) {
        return $query->whereType('Teacher');
    }


    /**
     * Filter query by Student type.
     */
    public function scopeStudents($query) {
        return $query->whereType('Student');
    }
}

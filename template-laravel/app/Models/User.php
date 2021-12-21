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
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The cards this user owns.
     */
     public function cards() {
      return $this->hasMany('App\Models\Card');
    }


    // --------------- L.EIC Q&A -----------------

    /**
     * The interventions this user is author of.
     */
    public function interventions() {
      return $this->belongsTo('App\Models\Intervention');
    }

    /**
     * The votes this user is associated with.
     */
    public function votes() {
        return $this->belongsToMany('App\Models\Intervention', 'voting', 'id_user', 'id_intervention')
                    ->withPivot('vote');
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
    public function responsible() {
      return $this->belongsToMany('App\Models\Uc', 'teacher_uc', 'id_teacher', 'id_uc');
    }

    /**
    * The interventions validated by this user. Only for teachers.
    */
    public function validation() {
      return $this->belongsToMany('App\Models\Intervention', 'validation', 'id_teacher', 'id_answer')
                  ->withPivot('valid');
    }
    

    /**
     * Filter query by Admin type.
     */
    public function scopeAdmins($query) {
        return $query->whereType('Admin')->get();
    }
 
    /**
     * Filter query by Teacher type.
     */
    public function scopeTeachers($query) {
        return $query->whereType('Teacher')->get();
    }
    
    /**
     * Filter query by Student type.
     */
    public function scopeStudents($query) {
        return $query->whereType('Student')->get();
    }
}

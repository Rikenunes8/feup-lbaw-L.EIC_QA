<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Uc extends Model
{

  public $timestamps  = false;
  
  /**
   * The table associated with this model is 'uc'.
   */
  protected $table = 'uc';

  /**
   * The interventions that belong to this uc. Only questions.
   */
  public function interventions() {
    return $this->hasMany('App\Models\Intervention', 'category');
  }

  /**
   * The users responsible for this uc. Only teachers.
   */
  public function teachers() {
    return $this->belongsToMany('App\Models\User', 'teacher_uc', 'id_uc', 'id_teacher');
  }

  /**
   * The users who follow this uc. Only students.
   */
  public function followers() {
    return $this->belongsToMany('App\Models\User', 'follow_uc', 'id_uc', 'id_student');
  }
}

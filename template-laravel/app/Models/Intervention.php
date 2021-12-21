<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Intervention extends Model
{
  public $timestamps  = false;

  /**
   * The table associated with this model is 'intervention'.
   */
  protected $table = 'intervention';

  /**
   * The user this intervention belongs to.
   */
  public function author() {
    return $this->belongsTo('App\Models\User');
  }

  /**
   * The uc this intervention belongs to. Only for questions.
   */
  public function uc() {
    return $this->belongsTo('App\Models\Uc');
  }

  /**
   * The child interventions that belongs to this intervention. Only for questions and answers.
   */
  public function childs() {
    return $this->hasMany('App\Models\Intervention');
  }

  /**
   * The parent intervention this intervention belongs to. Only for answers and comments.
   */
  public function parent() {
    return $this->belongsTo('App\Models\Intervention');
  }

  /**
   * The votes this intervention is associated with.
   */
  public function votes() {
      return $this->belongsToMany('App\Models\User', 'voting', 'id_intervention', 'id_user')
                    ->withPivot('vote');
  }

  /**
   * The user this intervention is validated by. Only for answers.
   */
  public function validation() {
    return $this->belongsToMany('App\Models\User', 'validation', 'id_answer', 'id_teacher')
                  ->withPivot('valid');
  }


  /**
   * Filter query by question type.
   */
  public function scopeQuestions($query) {
    return $query->whereType('question')->get();
  }

  /**
   * Filter query by answer type.
   */
  public function scopeAnswers($query) {
    return $query->whereType('answer')->get();
  }

  /**
   * Filter query by comment type.
   */
  public function scopeComments($query) {
    return $query->whereType('comment')->get();
  }
}

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


  public function isQuestion() {
    return $this->type == 'question';
  }

  public function isAnswer() {
    return $this->type == 'answer';
  }

  public function isComment() {
    return $this->type == 'comment';
  }

  /**
   * The user this intervention belongs to.
   */
  public function author() {
    return $this->belongsTo('App\Models\User', 'id_author');
  }

  /**
   * The uc this intervention belongs to. Only for questions.
   */
  public function uc() {
    return $this->belongsTo('App\Models\Uc', 'category');
  }

  /**
   * The child interventions that belongs to this intervention. Only for questions and answers.
   */
  public function childs() {
    return $this->hasMany('App\Models\Intervention', 'id_intervention');
  }

  /**
   * The parent intervention this intervention belongs to. Only for answers and comments.
   */
  public function parent() {
    return $this->belongsTo('App\Models\Intervention', 'id_intervention');
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
  public function valid() {
    return $this->belongsToMany('App\Models\User', 'validation', 'id_answer', 'id_teacher')
                  ->withPivot('valid');
  }

  /**
     * The notifications this intervention is associated with.
     */
    public function notifications() {
      return $this->hasMany('App\Models\Notification', 'id_intervention');
  }

    /**
     * Filter query by question type.
     */
    public function scopeQuestions($query) {
      return $query->whereType('question');
    }
    

    /**
     * Filter query by answer type.
     */
    public function scopeAnswers($query) {
        return $query->whereType('answer');
    }
    

    /**
     * Filter query by comment type.
     */
    public function scopeComments($query) {
        return $query->whereType('comment');
    }

    public function scopeSearch($query, $search) {
      if (!$search) {
        return $query;
      }
      
      $search = str_replace("\\", "\\\\", $search);
      $search = str_replace("|", "\|", $search);
      $search = str_replace("&", "\&", $search);
      $search = str_replace("!", "\!", $search);
      
      // Exact Match Search --------------------
      $exactMatchStrings = [];
      $start = null;
      $offset = -1;
      while (true) {
        $offset = strpos($search, '"', $offset+1);
        if ($offset === false) break;
        if (is_null($start)) {
          $start = $offset;
        }
        else {
          $exactMatchStrings[] = substr($search, $start+1, $offset-$start-1);
          $start = null;
        }
      }

      foreach($exactMatchStrings as $str) {
        $query->where('title', 'ilike', '%'.$str.'%')
            ->orWhere('text', 'ilike', '%'.$str.'%');
      }
      // ----------------------------------------

      $search = str_replace(" ", " | ", $search);

      if (empty($exactMatchStrings)) {
        // plainto_tsquery only concatenates words with & so its not used
        $query->whereRaw('tsvectors @@ to_tsquery(\'portuguese\', ?)', [$search]);
      }

      $query->orderByRaw('ts_rank(tsvectors, to_tsquery(\'portuguese\', ?)) DESC', [$search]);
      return $query;
    }
}

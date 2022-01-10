<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{

    public $timestamps  = false;
  
    /**
     * The table associated with this model is 'notification'.
     */
    protected $table = 'notification';
    
    /**
     * The users who have received this notification.
     */
    public function users() {
        return $this->belongsToMany('App\Models\User', 'receive_not', 'id_notification', 'id_user')
                    ->withPivot('read')->withPivot('to_email');
    }

    /**
     * The intervention this notification is associated with.
     */
    public function intervention() {
        return $this->belongsTo('App\Models\Intervention', 'id_intervention');
    }
    /**
     * The user this notification is associated with.
     */
    public function user() {
        return $this->belongsTo('App\Models\User', 'id_user');
    }

    /**
     * Filter query by report type.
     */
    public function scopeReports($query) {
        return $query->whereType('report');
    }

    public function isQuestion() {
        return $this->type == 'question';
    }
    public function isAnswer() {
        return $this->type == 'answer';
    }
    public function isComment() {
        return $this->type == 'comment';
    }
    public function isValidation() {
        return $this->type == 'validation';
    }
    public function isReport() {
        return $this->type == 'report';
    }
    public function isAccount_status() {
        return $this->type == 'account_status';
    } 
}

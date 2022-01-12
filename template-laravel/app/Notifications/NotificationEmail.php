<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotificationEmail extends Notification implements ShouldQueue
{
    use Queueable;

    public $notification;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($notification)
    {
        $this->notification = $notification;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if ($this->notification->isAccount_status()) {
            $subject = 'Novo Estado de Conta';
            if ($this->notification->status == 'active') $status = 'ativa';
            else if ($this->notification->status == 'block') $status = 'bloqueada';
            else $status = 'eliminada';
            $line1 = 'Tiveste uma atualização no estado de conta. A tua conta encontra-se agora <b>'.$status.'</b>.';
        } 
        else {
            $intervention = $notification->intervention()->first();
            $question = $intervention;
            if ($question->isAnswer()) $question = $question->parent()->first();
            else if ($question->isComment()) $question = $question->parent()->parent()->first();

            if ($this->notification->isQuestion()) {
              $subject = 'Nova Questão';
              $line1 = 'Há uma nova questão em '.$question->uc->code.', com título "'.$question->title.'" vai lá ver!';
            } else if ($this->notification->isAnswer()) {
              $subject = 'Nova Resposta';
              $line1 = 'Há uma nova resposta em '.$question->uc->code.', à questão "<b>'.$question->title.'</b>", vai lá ver!';
            } else if ($this->notification->isComment()) {
              $subject = 'Novo Comentário';
              $line1 = 'Há um novo comentário em '.$question->uc->code.', na tua resposta à questão "<b>'.$question->title.'</b>", vai lá ver!';
            } else if ($this->notification->isValidation()) {
              $subject = 'Nova Validação';
              $line1 = 'Há uma nova <b>'.$this->notification->validation == 'acceptance'? 'aceitação':'rejeição'.'</b> de resposta em '.$question->uc->code.', na questão "<b>'.$question->title.'</b>", vai lá ver!';
            } else if ($this->notification->isReport()) {
              $subject = 'Nova Denúncia';
              $line1 = 'A intervenção '.$notification->intervention->id.' foi reportada.';

            }
        }
        return (new MailMessage)
                    ->subject($subject)
                    ->greeting('Olá!')
                    ->line($line1)
                    ->action('Ler Notificação', url('/notifications/'.$this->notification->id.'/read'))
                    ->line('Obrigado por usar a nossa aplicação!')
                    ->salutation('Cumprimentos,\nLEIC Q&A');
    }
    

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

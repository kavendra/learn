<?php

namespace Betta\Foundation\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\MessageBag;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

abstract class AbstractCommandNotification extends Notification
{
    use Queueable;

    /**
     * Bind the implementation
     *
     * @var Illuminate\Support\MessageBag
     */
    protected $messages;

    /**
     * String representation of the environment source
     *
     * @var string
     */
    protected $environment;

    /**
     * Subject of the Notification
     *
     * @var string
     */
    protected $subject;

    /**
     * Command that is running
     *
     * @var string
     */
    protected $command;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($command, $subject, MessageBag $messageBag)
    {
        $this->command = $command;
        $this->subject = $subject;
        $this->messages = $messageBag;

        # remember environment
        $this->environment = env('APP_ENV');
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
     * @param  mixed  $notifiable is the implementation of ReceivesNoticiationsInterface
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        # Init Message
        $message = (new MailMessage)
                    ->subject( $this->getSubject() )
                    ->line("Artisan command `{$this->command}` is complete (Environment: {$this->environment}).");

        # Add Information and Errors
        $this->addInfo($message)->addWarnings($message)->addErrors($message);

        return $message->line('Have a great day!');
    }

    /**
     * Add info lines into Mail Messge
     *
     * @param MailMessage $mailMessage
     * @return MailMessage
     */
    protected function addInfo(MailMessage &$mailMessage)
    {
        foreach($this->messages->get('info') as $info){
            $mailMessage->line($info);
        }

        return $this;
    }

    /**
     * Add Warning lines into Mail Messge
     *
     * @param MailMessage $mailMessage
     * @return MailMessage
     */
    protected function addWarnings(MailMessage &$mailMessage)
    {
        if ($warnings = $this->messages->get('warning')){
            # Add an etrac Line
            $mailMessage->line('We have some concerns');

            # Add message
            foreach($warnings as $warning){
                $mailMessage->line("Warning: {$warning}");
            }
        }

        return $this;
    }

    /**
     * Add  lines into Mail Messge
     *
     * @param MailMessage $mailMessage
     * @return MailMessage
     */
    protected function addErrors(MailMessage &$mailMessage)
    {
        if ($errors = $this->messages->get('error')){
            # Add an etrac Line
            $mailMessage->line( sprintf('We encountered the folllowing %d errors', count($errors) ) );

            # Add message
            foreach($errors as $error){
                $mailMessage->line("Error: {$error}");
            }
        }

        return $this;
    }

    /**
     * Make Subject Line
     *
     * @return String
     */
    protected function getSubject()
    {
        return trans('app.label') .' | Artisan : ' . $this->subject;
    }
}

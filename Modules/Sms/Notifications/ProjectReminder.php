<?php

namespace Modules\Sms\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Modules\Sms\Entities\SmsNotificationSetting;
use Modules\Sms\Http\Traits\WhatsappMessageTrait;
use NotificationChannels\Telegram\TelegramMessage;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class ProjectReminder extends Notification implements ShouldQueue
{
    use Queueable, WhatsappMessageTrait;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $projects;

    private $data;

    private $smsSetting;

    private $message;

    private $company;

    public function __construct($projects, User $user, $data)
    {
        $this->projects = $projects;
        $this->data = $data;

        $this->company = $user->company;
        $this->smsSetting = SmsNotificationSetting::where('slug', 'project-reminder')->where('company_id', $this->company->id)->first();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if ($this->smsSetting && $this->smsSetting->send_sms != 'yes') {
            return [];
        }

        $this->message = __('email.projectReminder.subject')."\n".__('email.projectReminder.text').' '.now($this->data['global_setting']->timezone)->addDays($this->data['project_setting']->remind_time)->toFormattedDateString();

        $via = [];

        if (! is_null($notifiable->mobile) && ! is_null($notifiable->country_id)) {
            if (sms_setting()->status) {
                array_push($via, TwilioChannel::class);
            }

            if (sms_setting()->nexmo_status) {
                array_push($via, 'vonage');
            }

            if ($this->smsSetting->msg91_flow_id && sms_setting()->msg91_status) {
                array_push($via, 'msg91');
            }
        }

        if (sms_setting()->telegram_status && $notifiable->telegram_user_id) {
            array_push($via, 'telegram');
        }

        return $via;
    }

    public function toTwilio($notifiable)
    {
        $this->toWhatsapp($notifiable, __($this->smsSetting->slug->translationString(), ['dueDate' => now($this->data['global_setting']->timezone)->addDays($this->data['project_setting']->remind_time)->toFormattedDateString()]));

        if (sms_setting()->status) {
            return (new TwilioSmsMessage)
                ->content($this->message);
        }
    }

    //phpcs:ignore
    public function toVonage($notifiable)
    {

        if (sms_setting()->nexmo_status) {
            return (new VonageMessage)
                ->content($this->message)->unicode();
        }
    }

    //phpcs:ignore
    public function toMsg91($notifiable)
    {
        if ($this->smsSetting->msg91_flow_id && sms_setting()->msg91_status) {
            return (new \Craftsys\Notifications\Messages\Msg91SMS)
                ->flow($this->smsSetting->msg91_flow_id)
                ->variable('due_date', now($this->data['global_setting']->timezone)->addDays($this->data['project_setting']->remind_time)->toFormattedDateString());
        }
    }

    public function toTelegram($notifiable)
    {

        return TelegramMessage::create()
            // Optional recipient user id.
            ->to($notifiable->telegram_user_id)
            // Markdown supported.
            ->content($this->message)
            ->button(__('email.projectReminder.action'), route('projects.index'));
    }
}

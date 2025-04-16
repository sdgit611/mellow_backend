<?php

namespace Modules\Sms\Notifications;

use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;
use Modules\Sms\Entities\SmsNotificationSetting;
use Modules\Sms\Http\Traits\WhatsappMessageTrait;
use NotificationChannels\Telegram\TelegramMessage;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class FileUpload extends Notification implements ShouldQueue
{
    use Queueable, WhatsappMessageTrait;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $file;

    private $project;

    private $company;

    private $smsSetting;

    private $message;

    public function __construct(ProjectFile $file)
    {
        $this->file = $file;
        $this->project = Project::find($this->file->project_id);

        $this->company = $this->project->company;
        $this->smsSetting = SmsNotificationSetting::where('slug', 'new-file-uploaded-to-project')->where('company_id', $this->company->id)->first();
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

        $this->message = __('email.fileUpload.subject').' '.$this->project->project_name.'.'."\n".__('modules.projects.fileName').' - '.$this->file->filename."\n".__('app.date').' - '.$this->file->created_at->format($this->company->date_format);

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
        $this->toWhatsapp($notifiable, __($this->smsSetting->slug->translationString(), ['projectName' => $this->project->project_name, 'fileName' => $this->file->filename, 'date' => $this->file->created_at->format($this->company->date_format)]));

        if ($this->smsSetting->status) {
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
                ->variable('project_name', $this->project->project_name)
                ->variable('file_name', $this->file->filename)
                ->variable('date', $this->file->created_at->format($this->company->date_format));
        }
    }

    public function toTelegram($notifiable)
    {
        return TelegramMessage::create()
            // Optional recipient user id.
            ->to($notifiable->telegram_user_id)
            // Markdown supported.
            ->content($this->message)
            ->button(__('email.fileUpload.action'), route('projects.show', [$this->project->id, 'tab' => 'files']));
    }
}

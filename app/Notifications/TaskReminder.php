<?php

namespace App\Notifications;

use App\Models\EmailNotificationSetting;
use App\Models\Task;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class TaskReminder extends BaseNotification
{

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $task;
    private $emailSetting;

    public function __construct(Task $task)
    {

        $this->task = $task;
        $this->emailSetting = EmailNotificationSetting::userAssignTask();
        $this->company = $this->task->company;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = array();

        if ($notifiable->email_notifications && $notifiable->email != '') {
            array_push($via, 'mail');
        }

        if ($this->emailSetting->send_slack == 'yes' && $this->company->slackSetting->status == 'active') {
            $this->slackUserNameCheck($notifiable) ? array_push($via, 'slack') : null;
        }

        if ($this->emailSetting->send_push == 'yes' && push_setting()->status == 'active') {
            array_push($via, OneSignalChannel::class);
        }

        if ($this->emailSetting->send_push == 'yes' && push_setting()->beams_push_status == 'active') {
            $pushNotification = new \App\Http\Controllers\DashboardController();
            $pushUsersIds = [[$notifiable->id]];
            $pushNotification->sendPushNotifications($pushUsersIds, __('email.reminder.subject'), $this->task->heading);
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $build = parent::build($notifiable);
        $url = route('front.task_detail', [$this->task->hash]);

        $url = getDomainSpecificUrl($url, $this->company);

        $content = $this->task->heading . ' #' . $this->task->task_short_code . '<p>';

        if ($this->task->due_date) {
            $content .= '<b style="color: green">' . __('app.dueDate') . ' : ' . $this->task->due_date->format($this->company->date_format) . '</b>
            </p>';
        }

        $build
            ->subject(__('email.reminder.subject') . ' #' . $this->task->task_short_code . ' - ' . config('app.name') . '.')
            ->greeting(__('email.hello') . ' ' . $notifiable->name . ',')
            ->markdown('mail.task.reminder', [
                'url' => $url,
                'content' => $content,
                'themeColor' => $this->company->header_color
            ]);

        parent::resetLocale();

        return $build;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    //phpcs:ignore
    public function toArray($notifiable)
    {
        return [
            'id' => $this->task->id,
            'created_at' => $this->task->created_at->format('Y-m-d H:i:s'),
            'heading' => $this->task->heading
        ];
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\SlackMessage
     */
    public function toSlack($notifiable)
    {

        $dueDate = (!is_null($this->task->due_date)) ? $this->task->due_date->format($this->company->date_format) : null;

        return $this->slackBuild($notifiable)
            ->content('*' . __('email.reminder.subject') . '*' . "\n" . $this->task->heading . "\n" . ' #' . $this->task->task_short_code . "\n" . __('app.dueDate') . ': ' . $dueDate);

    }

    // phpcs:ignore
    public function toOneSignal($notifiable)
    {
        return OneSignalMessage::create()
            ->setSubject(__('email.reminder.subject'))
            ->setBody($this->task->heading . ' #' . $this->task->task_short_code);
    }

}

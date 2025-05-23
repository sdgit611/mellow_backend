<?php

namespace App\Notifications;

use App\Models\EmailNotificationSetting;
use App\Models\Task;
use App\Models\User;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class TaskStatusUpdated extends BaseNotification
{


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $task;
    private $updatedBy;
    /**
     * @var mixed
     */
    private $emailSetting;

    public function __construct(Task $task, User $updatedBy = null)
    {
        $this->task = $task;
        $this->updatedBy = $updatedBy;
        $this->company = $this->task->company;
        $this->emailSetting = EmailNotificationSetting::where('company_id', $this->company->id)
        ->where('slug', 'task-status-updated')
        ->first();

    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = ['database'];

        if ($this->emailSetting->send_email == 'yes' && $notifiable->email_notifications && $notifiable->email != '') {
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
            $pushNotification->sendPushNotifications($pushUsersIds, __('email.taskUpdate.subject'), $this->task->heading);
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $build = parent::build($notifiable);
        $url = route('tasks.show', $this->task->id);
        $url = getDomainSpecificUrl($url, $this->company);

        $projectTitle = (!is_null($this->task->project)) ? __('app.project') . ' - ' . $this->task->project->project_name : '';

        $content = 'Updated Task Status: ' . $this->task->boardColumn->column_name . '<br>' . __('email.taskUpdate.updatedBy') . ': ' . $this->updatedBy->name . '<br>' . __('app.task') . ': ' . $this->task->heading . '<br>' . $projectTitle;
        $taskShortCode = (!is_null($this->task->task_short_code)) ? '#' . $this->task->task_short_code : ' ';

        $build
            ->subject(__('email.taskUpdate.subject') . $taskShortCode . ' - ' . config('app.name') . '.')
            ->markdown('mail.email', [
                'url' => $url,
                'content' => $content,
                'themeColor' => $this->company->header_color,
                'actionText' => __('email.taskUpdate.action'),
                'notifiableName' => $notifiable->name
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
            'heading' => $this->task->heading,
            'completed_on' => (!is_null($this->task->completed_on)) ? $this->task->completed_on->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param mixed $notifiable
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
        $url = route('tasks.show', $this->task->id);
        $url = getDomainSpecificUrl($url, $this->company);
        $taskShortCode = $this->task->task_short_code ? ' #' . $this->task->task_short_code : '';
        $taskStatus = $this->task->boardColumn->column_name ? 'Status: ' . $this->task->boardColumn->column_name : '';
        $taskDescription = $this->task->description ? 'Description: ' . strip_tags($this->task->description) : '';
        $taskAssigne = $this->task->users ? 'Task Assignees: ' . $this->task->users->pluck('name')->implode(', ') : '';

        return $this->slackBuild($notifiable)
            ->content('*' . __('email.taskUpdate.slackStatusUpdated') . '*' . "\n" . '<' . $url . '|' . $this->task->heading . '>' .
            "\n" . $taskShortCode . "\n" . $taskStatus . "\n" . $taskDescription .
            "\n" . $taskAssigne . (!is_null($this->task->project) ? "\n" . __('app.project') . ': ' . $this->task->project->project_name : ''));

    }

    // phpcs:ignore
    public function toOneSignal($notifiable)
    {
        return OneSignalMessage::create()
            ->setSubject(__('email.taskUpdate.subject'))
            ->setBody($this->task->heading . ' ' . __('email.taskUpdate.subject') . ' #' . $this->task->task_short_code);
    }

}

<x-cards.notification :notification="$notification" :link="route('zoom-meetings.index')" :image="user()->image_url"
                      :title="__('zoom::email.newMeeting.subject')" :text="$notification->data['meeting_name']"
                      :time="$notification->created_at"/>

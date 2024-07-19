<?php

namespace Modules\System\Notifications;

use Guanguans\Notify\Factory;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Modules\System\Enums\Message\ChannelEnum;

class Message extends Notification
{
    use Queueable;

    protected $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via($notifiable): array
    {
        return ['notify'];
    }

    public function toNotify()
    {
        $channel = $this->message->channel;
        $config = config("notify.channels.{$channel}");

        if (empty($config['driver'])) {
            throw new \RuntimeException('获取渠道配置失败');
        }

        if ($channel == ChannelEnum::MAIL->value) {
            $message = \Guanguans\Notify\Messages\EmailMessage::create()
                ->subject($this->message->subject)
                ->text($this->message->content);

            foreach ($this->message->options['option'] as $option => $val) {
                $message->{$option}($val);
            }
        } else {
            throw new \RuntimeException('暂不支持此渠道');
        }

        Factory::{$config['driver']}(array_filter_filled($config['config']))->setMessage($message)->send();
    }

    public function shouldSend($notifiable, $channel): bool
    {
        return true;
    }

    public function toArray($notifiable): array
    {
        return [
        ];
    }
}

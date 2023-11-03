<?php

namespace Merlinpanda\Account\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Merlinpanda\Account\Models\User;

class AddTemporaryEmail extends Mailable
{
    use Queueable, SerializesModels;


    protected $user;

    protected $email;

    protected $valid_time;

    protected $url;

    protected $uuid;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $email, $valid_time, $url, $uuid)
    {
        $this->user = $user;
        $this->email = $email;
        $this->valid_time = $valid_time;
        $this->url = $url;
        $this->uuid = $uuid;

        $this->subject = __("account::mail.subjects.add_temporary_email");
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('account::emails.auth.temporary_email')->with([
            'username' => $this->user->firstNameWithPrefix(),
            'email' => $this->email,
            'valid_time' => $this->valid_time,
            'url' => $this->url,
            'uuid' => $this->uuid,
        ]);
    }
}

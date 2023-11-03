<?php

namespace Merlinpanda\Account\Actions\User;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Merlinpanda\Account\Exceptions\ErrorCodes;
use Merlinpanda\Account\Mail\AddTemporaryEmail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Merlinpanda\Account\Models\User;
use Merlinpanda\Account\Models\UserEmailTemporary;

/**
 *
 */
class TryAddingEmail
{
    const TOKEN_EXPIRED_TIMELONG = 60; // min

    public function handle(User $user, string $email, string $base_url)
    {
        Validator::make([
            "email" => $email
        ], [
            "email" => "required|email"
        ]);

        // 判断邮箱是否已经被占用，如果已被占用，将抛出异常
        $this->emailIsOccupied($email);

        // 生成校验token
        $token = Hash::make($this->generateKey($user->id, $email));

        // 生成校验链接
        $check_url = $this->generateVerifyUrl($user, $email, $token, $base_url);

        // 存储校验信息
        $user_temporary_email = new UserEmailTemporary();
        $user_temporary_email->user_id = $user->id;
        $user_temporary_email->email = $email;
        $user_temporary_email->token = $token;
        $user_temporary_email->expired_at = Carbon::now()->addMinutes(self::TOKEN_EXPIRED_TIMELONG);
        $user_temporary_email->save();

        // 生成邮件内容UUID
        $uuid = Str::uuid();
        $mailer = new AddTemporaryEmail($user, $email, self::TOKEN_EXPIRED_TIMELONG, $check_url, $uuid);

        // 渲染邮件内容并存储UUID
        $html = $mailer->render();
        $user->mailUUIDs()->create([
            'html' => $html,
            'email' => $email,
            'subject' => $mailer->subject,
            'uuid' => $uuid,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 发送邮件
        Mail::to($email)->send($mailer);
    }

    /**
     * @param User $user
     * @param $email
     * @param $token
     * @param $base_url
     * @return string
     */
    private function generateVerifyUrl(User $user, $email, $token, $base_url)
    {
        $query = http_build_query([
            'user_id' => $user->id,
            'email' => $email,
            'token' => $token,
        ]);

        if (strpos($base_url, '?') !== false) {
            $url = $base_url . '?' . $query;
        } else {
            $url = $base_url . '&' . $query;
        }

        return $url;
    }

    /**
     * @param $email
     * @return void
     * @throws \Exception
     */
    private function emailIsOccupied($email): void
    {
        $exist_verified = DB::table("user_emails")->where([
            'email' => $email,
        ])->whereNotNull("email_verified_at")->exists();

        if ($exist_verified) {
            throw new \Exception(__("account::mail.failed.email_is_occupied", [ "email" => $email ]), ErrorCodes::EMAIL_IS_OCCUPIED);
        }
    }

    /**
     * @param $user_id
     * @param $email
     * @return string
     */
    private function generateKey($user_id, $email)
    {
        return sprintf("USER_%d_EMAIL_%s_KEY_%s", $user_id, $email, env('APP_KEY'));
    }

}

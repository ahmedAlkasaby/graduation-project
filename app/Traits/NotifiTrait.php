<?php

namespace App\Traits;

use Exception;
use App\Models\User;
use App\Models\Device;
use App\Models\Setting;
use App\Traits\HelperTrait;
use LaravelFCM\Facades\FCM;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\DatabaseNotification;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;


trait NotifiTrait
{
    use HelperTrait;

    protected static ?Messaging $firebase = null;

    protected function getFirebaseMessaging(): Messaging
    {
        if (self::$firebase === null) {
            self::$firebase = (new Factory)
                ->withServiceAccount(config('services.firebase.credentials'))
                ->createMessaging();
        }

        return self::$firebase;
    }

    public function notificationUserType()
    {
        $user_ids = [];
        User::active()->chunk(99999, function ($query) use (&$user_ids) {
            $user_ids +=  $query->pluck('id')->toArray();
        });
        return $user_ids;
    }

    public function notificationUser(
        $user_id,
        $title,
        $message,
        $type = "admin",
        $model = null,
        $model_id = null,
        $status = null,
        $image = null,
        $database = true,
        $firebase = true,
        $imei = null,
        $badge_count = 1
    ) {
        if ($firebase) {
            // Log::info('Preparing to send notification to Firebase.', [
            //     'user_id' => $user_id,
            //     'title' => $title,
            //     'message' => $message,
            // ]);

            $firebaseService = app(FirebaseService::class);
            $messaging = $firebaseService->getMessaging();
            // Log::info('Firebase Messaging instance created.');
        }

        // Normalize user IDs
        $user_ids = is_array($user_id) ? $user_id : [$user_id];

        // Calculate badge count for single user
        if (!is_array($user_id)) {
            $badge = DatabaseNotification::where('notifiable_id', $user_id)
                ->where('notifiable_type', 'users')
                ->whereNull('read_at')
                ->count();
            $badge_count = (int) ($badge + 1);
        }

        $notifi = [
            'title' => $title,
            'body' => $message,
            'type' => $type,
            'model_id' => $model_id,
            'model' => $model,
            'status' => $status,
            'image' => $image,
        ];

        $notifimobile = [
            'type' => $type,
            'model_id' => $model_id,
            'model' => $model,
            'status' => $status,
            'sound' => 'default',
            'screen' => 'screenA',
            'priority' => 'high',
            'mutable-content' => 1,
            'content-available' => 1,
            'badge' => $badge_count,
        ];

        $notifi_success = 0;

        // Send database notification
        if ($database) {
            $notificationClass = 'App\Notifications\\' . notificationModelName($type);
            User::active()
                ->whereIn('id', $user_ids)
                ->select('id')
                ->chunk(9999, function ($users) use ($notificationClass, $notifi) {
                    Notification::send($users, new $notificationClass($notifi));
                });
        }

        $ssl_certificate = settings()->get('ssl_certificate') === 'yes' ? 'yes' : 'no';

        // Send Firebase notification
        if ($firebase && $ssl_certificate === 'yes') {
            // Log::info('Preparing to send notification to Firebase.', [
            //     'user_ids' => $user_ids,
            //     'title' => $title,
            //     'message' => $message,
            //     'badge_count' => $badge_count,
            // ]);

            try {
                $titleString = is_array($title) ? ($title['en'] ?? '') : (string)$title;
                $messageString = is_array($message) ? ($message['en'] ?? '') : (string)$message;
                $imageString = $image ? (string)$image : null;

                $notification = FirebaseNotification::create($titleString, $messageString, $imageString);

                // Get device tokens for the target users
                $tokens = Device::whereIn('user_id', $user_ids)
                    ->whereNotNull('token')
                    ->pluck('token')
                    ->toArray();

                

                if (!empty($tokens)) {
                    $messages = [];
                    foreach ($tokens as $deviceToken) {
                        $messages[] = CloudMessage::withTarget('token', $deviceToken)
                            ->withNotification($notification)
                            ->withData(array_merge($notifimobile, [
                                'title' => $titleString,
                                'body'  => $messageString,
                            ]));
                    }

                    $sendReport = $messaging->sendAll($messages);

                    foreach ($sendReport->failures()->getItems() as $failure) {
                        $errorMessage = $failure->error()->getMessage();
                        $failedToken = $failure->target()->value();

                        if (strpos($errorMessage, 'not a valid FCM registration token') !== false) {
                            Device::where('token', $failedToken)->delete();
                        }
                    }

                    $notifi_success = $sendReport->successes()->count();
                    // Log::info('Firebase notification send report', [
                    //     'successes' => $notifi_success,
                    //     'failures' => $sendReport->failures()->count(),
                    // ]);
                } else {
                    Log::warning('No device tokens available, Firebase notification not sent.');
                }
            } catch (Exception $e) {
                 Log::error('Firebase notification error: ' . $e->getMessage());
            }
        } else {
            Log::warning('Notification not sent. Conditions not met.', [
                'firebase' => $firebase,
                'ssl_certificate' => $ssl_certificate,
            ]);
        }

        return $notifi_success;
    }

    




    public function orderStatusMessage($status, $lang = "en")
    {
        $title_status = 'Order change status';
        app()->setLocale("en");
        $name_en = __($title_status);
        $content_en = __($title_status) . ' ' . __('to') . ' ' . orderName($status);
        app()->setLocale("ar");
        $name_ar = __($title_status);
        $content_ar = __($title_status) . ' ' . __('to') . ' ' . orderName($status);
        app()->setLocale($lang);
        $title   = $this->getName($name_ar, $name_en);
        $message = $this->getName($content_ar, $content_en);
        $notify = ['title' => $title, 'message' => $message];
        return $notify;
    }

    public function orderDeliveryMessage($order_id, $lang = "en")
    {
        $title_delivery_lang = __('Order No');
        $message_delivery_lang = __('has been transferred to you');
        $title_delivery = 'Order No';
        $message_delivery = 'has been transferred to you';
        app()->setLocale("en");
        $name_en = __($title_delivery) . ' # ' . $order_id . ' ' . __($message_delivery);
        $content_en = __($title_delivery) . ' # ' . $order_id . ' ' . __($message_delivery);
        app()->setLocale("ar");
        $name_ar = __($title_delivery) . ' # ' . $order_id . ' ' . __($message_delivery);
        $content_ar = __($title_delivery) . ' # ' . $order_id . ' ' . __($message_delivery);
        app()->setLocale($lang);
        $title   = $this->getName($name_ar, $name_en);
        $message = $this->getName($content_ar, $content_en);
        $notify = ['title' => $title, 'message' => $message];
        return $notify;
    }

    public function notificationMessage($lang, $name_notify, $message_notify)
    {
        app()->setLocale("en");
        $name_en = __($name_notify);
        $content_en = __($message_notify);
        app()->setLocale("ar");
        $name_ar = __($name_notify);
        $content_ar = __($message_notify);
        app()->setLocale($lang);
        $title   = $this->getName($name_ar, $name_en);
        $message = $this->getName($content_ar, $content_en);
        $notify = ['title' => $title, 'message' => $message];
        return $notify;
    }

    public function notificationDevice($user_ids, $option, $notification, $data, $notifi_success, $imei)
    {
        $devices = Device::whereIn('user_id', $user_ids)->whereNotNull('token');
        if ($imei != null) {
            $devices->where('imei', '<>', $imei);
        }
        $devices->chunk(99999, function ($query) use ($option, $notification, $data, &$notifi_success) {
            $tokens     = $query->where('type', 'android')->pluck('token')->toArray();
            $tokens_ios = $query->whereIn('type', ['apple', 'ios'])->pluck('token')->toArray();

            if (!empty($tokens)) {
                $downstream_response = FCM::sendTo($tokens, $option, null, $data);
                $notifi_success += $downstream_response->numberSuccess();

                // Log the response for debugging

                $delete_tokens  = $downstream_response->tokensToDelete();
                if (!empty($delete_tokens)) {
                    Device::whereIn('token', $delete_tokens)->delete();
                }

                // Check for invalid tokens and delete them
                $invalid_tokens = $downstream_response->tokensWithError();
                if (!empty($invalid_tokens)) {
                    Device::whereIn('token', array_keys($invalid_tokens))->delete();
                }
            }

            if (!empty($tokens_ios)) {
                $downstream_response_ios = FCM::sendTo($tokens_ios, $option, $notification, $data);
                $notifi_success += $downstream_response_ios->numberSuccess();

                // Log the response for debugging

                $delete_tokens_ios  = $downstream_response_ios->tokensToDelete();
                if (!empty($delete_tokens_ios)) {
                    Device::whereIn('token', $delete_tokens_ios)->delete();
                }

                // Check for invalid tokens and delete them
                $invalid_tokens_ios = $downstream_response_ios->tokensWithError();
                if (!empty($invalid_tokens_ios)) {
                    Device::whereIn('token', array_keys($invalid_tokens_ios))->delete();
                }
            }

            return $notifi_success;
        });
    }
    public function notificationProductMessage($lang, $product)
    {
        $title_status = 'Product is available now';
        app()->setLocale("en");
        $name_en = $product->name['en'];
        $content_en = __($title_status);
        app()->setLocale("ar");
        $name_ar = $product->name['ar'];
        $content_ar = __($title_status);
        app()->setLocale($lang);
        $title   = $this->getName($name_ar, $name_en);
        $message = $this->getName($content_ar, $content_en);
        $notify = ['title' => $title, 'message' => $message];
        return $notify;
    }
}

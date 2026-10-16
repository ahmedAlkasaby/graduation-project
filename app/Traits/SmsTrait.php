<?php

namespace App\Traits;

use App\Models\Sms;
use Carbon\Carbon;

trait SmsTrait
{
    public function formatMessage($otp)
    {
        $message = $otp . ' ' . ('your verification code to') . ' ' . config('app.name');
        return $message;    
    }

    public function sendSms($phoneSms, $msg, $code = "20")
    {
        $key = Sms::where(['active' => 1])->first()->key ?? null;
        $phone = prefixPhoneWithCountryCode($phoneSms, $code);
        switch ($key) {
            case 'yamamah':
                $this->yamamah($phone, $msg);
                break;
            case '4jawaly':
                $this->jawaly($phone, $msg);
                break;
            case 'gateway':
                $this->gateway($phone, $msg);
                break;
            case 'hisms':
                $this->hisms($phone, $msg);
                break;
            case 'msegat':
                $this->msegat($phone, $msg);
                break;
            case 'oursms':
                $this->oursms($phone, $msg);
                break;
            case 'unifonic':
                $this->unifonic($phone, $msg);
                break;
            case 'zain':
                $this->zain($phone, $msg);
                break;
        }
    }

    public function jawaly($phone, $msg)
    {
        $data = SMS::where(['key' => '4jawaly'])->first();
        $username = $data->user_name;
        $password = $data->password;
        $text = urlencode($msg);
        $sender = urlencode($data->sender_name);

        $url = "https://www.4jawaly.net/api/sendsms.php?username=$username&password=$password&numbers=$phone&sender=$sender&message=$text&unicode=e&Rmduplicated=1&return=string";
        $result = file_get_contents($url, true);
    }

    public function gateway($phone, $msg)
    {
        $data = SMS::where(['key' => 'gateway'])->first();
        $username = $data->user_name;
        $password = $data->password;
        $uid = $data->uid;
        $text = urlencode($msg);
        $sender = urlencode($data->sender_name);
        if (is_array($phone)) {
            $phone = implode(',', $phone);
        }
        // $url    = "www.sms.gateway.sa/api/sendsms.php?username=$username&password=$password&message=$text&numbers=$phone&sender=$sender&unicode=e&Rmduplicated=1&return=string";
        $url = "http://rest.gateway.sa/api/SendSMS?api_id=" . $username . "&api_password=" . $password . "&sms_type=T&encoding=T&sender_id=" . $sender . "&phonenumber=$phone&textmessage=$text&uid=" . $uid;
        $result = file_get_contents($url, true);
        // $contextPostValues = http_build_query(array(
        //   'user'     => $username,
        //   'password' => $password,
        //   'msisdn'   => $phone,
        //   'sid'      => $sender,
        //   'msg'      => $msg,
        //   'fl'       => 0,
        // ));
        // $contextOptions['http'] = array(
        //   'method'           => 'POST',
        //   'header'           => 'Content-type: application/x-www-form-urlencoded',
        //   'content'          => $contextPostValues,
        //   'max_redirects'    => 0,
        //   'protocol_version' => 1.0,
        //   'timeout'          => 10,
        //   'ignore_errors'    => TRUE,
        // );
        // $contextResouce = stream_context_create($contextOptions);
        // $url            = "apps.gateway.sa/vendorsms/pushsms.aspx";
        // $arrayResult    = file($url, FILE_IGNORE_NEW_LINES, $contextResouce);
        // dd($arrayResult);
        // $result         = $arrayResult[0];
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function hisms($phone, $msg)
    {

        $data = SMS::where(['key' => 'hisms'])->first();
        $username = $data->user_name;
        $password = $data->password;
        $sender = urlencode($data->sender_name);
        $msg = urlencode($msg);

        $url = "https://www.hisms.ws/api.php?send_sms&username=$username&password=$password&numbers=$phone&sender=$sender&message=$msg";
        $result = file_get_contents($url, true);
        if (false === $result) {
            return false;
        } else {
            return true;
        }
    }

    public function msegat($phone, $msg)
    {
        $data = SMS::where(['key' => 'msegat'])->first();
        $username = $data->user_name;
        $password = $data->password;
        $sender = $data->sender_name;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://www.msegat.com/gw/sendsms.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);

        curl_setopt($ch, CURLOPT_POST, TRUE);

        $fields = <<<EOT
        {
        "userName": "$username",
        "numbers": "$phone",
        "userSender": "$sender",
        "apiKey": "$password",
        "msg": "$msg"
        }
        EOT;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
        ));

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        return true;
    }

    public function oursms($phone, $msg)
    {
        $data = SMS::where(['key' => 'oursms'])->first();
        $username = $data->user_name;
        $password = $data->password;
        $sender = $data->sender_name;

        $text = urlencode($msg);
        $to = '+' . $phone;
        // auth call
        //$url = "http://www.oursms.net/api/sendsms.php?username=$user&password=$password&numbers=$to&message=$text&sender=$sendername&unicode=E&return=full";
        //لارجاع القيمه json
        $url = "http://www.oursms.net/api/sendsms.php?username=$username&password=$password&numbers=$to&message=$text&sender=$sender&unicode=E&return=json";
        // لارجاع القيمه xml
        //$url = "http://www.oursms.net/api/sendsms.php?username=$user&password=$password&numbers=$to&message=$text&sender=$sendername&unicode=E&return=xml";
        // لارجاع القيمه string
        //$url = "http://www.oursms.net/api/sendsms.php?username=$user&password=$password&numbers=$to&message=$text&sender=$sendername&unicode=E";

        // Call API and get return message
        //fopen($url,"r");
        //return $url;
        $ret = file_get_contents($url);
    }

    public function unifonic($phone, $msg)
    {
        $data = SMS::where(['key' => 'unifonic'])->first();
        $sender = $data->sender_name;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL            => 'https://el.cloud.unifonic.com/rest/SMS/Messages/Send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => array('AppSid' => 'VMjM7E6Z6U4vO5pz728wLc9vCMHAEa', 'Recipient' => $phone, 'SenderID' => $sender, 'Body' => $msg),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $getResponse = json_decode($response, true);
        //        dd($getResponse);
        if (isset($getResponse['success'])) {

            if ($getResponse['success'] == true) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    //  public function unifonic($phone, $msg) {
    //    $data     = SMS::where(['key' => 'unifonic'])->first();
    ////    $username = urlencode($data->user_name);
    //    $username = $data->user_name;
    ////    $password = urlencode($data->password);
    //    $password = $data->password;
    ////    $sender  = urlencode($data->sender_name);
    //    $sender  = $data->sender_name;
    //    $numbers = is_array($phone)? implode(',', $phone) : $phone;
    //    $text    = urlencode($msg);
    ////    $url     = "http://api.unifonic.com/wrapper/sendSMS.php?userid=$username&password=$password&msg=$text&sender=$sender&to=$numbers&encoding=UTF8";
    ////    $url     = "https://basic.unifonic.com/rest/SMS/Messages/Send?userid=$username&password=$password&msg=$text&sender=$sender&to=$numbers&encoding=UTF8";
    ////    $url     = "https://el.cloud.unifonic.com/rest/SMS/Messages/Send?userid=$username&password=$password&msg=$text&sender=$sender&to=$numbers&encoding=UTF8";
    ////    $url     = "https://el.cloud.unifonic.com/rest/SMS/Messages/Send?AppSid=VMjM7E6Z6U4vO5pz728wLc9vCMHAEa&Body=$text&SenderID=$sender&Recipient=966502340048&encoding=UTF8";
    //    $url     = "https://el.cloud.unifonic.com/rest/SMS/Messages/Send?AppSid=VMjM7E6Z6U4vO5pz728wLc9vCMHAEa&Body=$text&SenderID=$sender&Recipient=$numbers&encoding=UTF8";
    //    $result  = @file_get_contents($url, true);
    //    if (false === $result) {
    //      return false;
    //    } else {
    //      return true;
    //    }
    //  }

    public function zain($phone, $msg)
    {
        $data = SMS::where(['key' => 'zain'])->first();
        $username = $data->user_name;
        $password = $data->password;
        $sender = $data->sender_name;

        $to = $phone; // Should be like 966530007039
        $text = urlencode($msg . '   ');

        $link = "https://www.zain.im/index.php/api/sendsms/?user=$username&pass=$password&to=$to&message=$text&sender=$sender";

        if (function_exists('curl_init')) {
            $curl = @curl_init($link);
            @curl_setopt($curl, CURLOPT_HEADER, FALSE);
            @curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            @curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
            @curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            $source = @curl_exec($curl);
            @curl_close($curl);
            if ($source) {
                return $source;
            } else {
                return @file_get_contents($link);
            }
        } else {
            return @file_get_contents($link);
        }
    }

    public function yamamah($phone, $msg)
    {
        $data = SMS::where(['key' => 'yamamah'])->first();
        $username = $data->user_name;
        $password = $data->password;
        $sender = $data->sender_name;

        $url = 'api.yamamah.com/SendSMS';
        $to = $phone; // Should be like 966530007039
        $text = urlencode($msg);
        $fields = array(
            "Username"        => $username,
            "Password"        => $password,
            "Message"         => $text,
            "RecepientNumber" => $to, //'00966'.ltrim($numbers,'0'),
            "ReplacementList" => "",
            "SendDateTime"    => "0",
            "EnableDR"        => False,
            "Tagname"         => $sender,
            "VariableList"    => "0",
        );

        $fields_string = json_encode($fields);

        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_POST           => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER     => array(
                'Content-Type: application/json',
            ),
            CURLOPT_POSTFIELDS     => $fields_string,
        ));
        $result = curl_exec($ch);
        curl_close($ch);
    }
}
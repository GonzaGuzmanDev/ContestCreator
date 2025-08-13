<?php namespace App\Services;
use Config;
use jyggen\Curl;

class OxoMailer{
    /**
     * @param $params
     * @return string|void
     * @throws Curl\Exception\CurlErrorException
     * @throws Curl\Exception\ProtectedOptionException
     */
    public static function sendMail($params){
        /**
         * params:
         * email_to, subject, body
         */
        /*$body = ContestAsset::where('contest_id', $con->id)->where('type', $type)->select('content')->firstOrFail();
        $body->content = str_replace($vars, $replace, $body->content);*/

        if(!Config::get('mail.enabled')){
            return;
        }

        $tokenUrl = Config::get('mail.tokenUrl');
        $userUrl = Config::get('mail.userUrl');
        $emailUrl = Config::get('mail.emailUrl');
        $tokenData = Config::get('mail.tokenData');
        $data = json_encode($tokenData);

        $request = new \jyggen\Curl\Request($tokenUrl); // jyggen\Curl\Request

        $request->setOption(CURLOPT_FOLLOWLOCATION, true);
        $request->setOption(CURLOPT_POST, true);
        $request->setOption(CURLOPT_POSTFIELDS, $data);
        $request->setOption(CURLOPT_SSL_VERIFYHOST, false);
        $request->setOption(CURLOPT_SSL_VERIFYPEER, false);

        $request->setOption(CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
        ));

        $request->execute();
        if ($request->isSuccessful()) {
            $tokenData = $request->getRawResponse();
            $pos = strpos($tokenData, '{"token":"');
            if ($pos !== false) {
                $token = substr($tokenData, $pos + 10, -2);
                $request = new \jyggen\Curl\Request($userUrl."?token=".$token); // jyggen\Curl\Request
                $request->setOption(CURLOPT_FOLLOWLOCATION, true);
                $request->setOption(CURLOPT_SSL_VERIFYHOST, false);
                $request->setOption(CURLOPT_SSL_VERIFYPEER, false);
                $request->execute();
                if($request->isSuccessful()) {
                    $userData = $request->getRawResponse();
                    $pos = strpos($userData, '{"user":');
                    if ($pos !== false) {
                        $userData = substr($userData, $pos);
                    }
                    $userGet = json_decode($userData, true);
                    $emailData = array("email_to" => $params['email_to'], "body" => $params['body'], "subject" => $params['subject'], "user_id" => $userGet['user']['id']);
                    $email = json_encode($emailData);
                    $emailRequest = new \jyggen\Curl\Request($emailUrl . '?token=' . $token); // jyggen\Curl\Request
                    //$emailRequest->setOption(CURLOPT_RETURNTRANSFER, true);
                    //$emailRequest->setOption(CURLOPT_FOLLOWLOCATION, true);
                    $emailRequest->setOption(CURLOPT_POST, true);
                    $emailRequest->setOption(CURLOPT_POSTFIELDS, $email);
                    $emailRequest->setOption(CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json', 'Expect:'
                    ));
                    $emailRequest->setOption(CURLOPT_SSL_VERIFYHOST, false);
                    $emailRequest->setOption(CURLOPT_SSL_VERIFYPEER, false);
                    $emailRequest->execute();
                    if ($emailRequest->isSuccessful()) {
                        $emailResponse = $emailRequest->getRawResponse();
                        return $emailResponse;
                    } else {
                        throw new Exception($emailRequest->getErrorMessage());
                    }
                } else {
                    throw new Exception($request->getErrorMessage());
                }
            }
        } else {
            throw new Exception($request->getErrorMessage());
        }
    }
}
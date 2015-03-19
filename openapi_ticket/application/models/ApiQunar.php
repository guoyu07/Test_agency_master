<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-1-27
 * Time: 下午6:28
 */
class ApiQunarModel extends Base_Model_Api{
    public static function sendCodeNotice($data){
        try{
             $setting = unserialize(QUNAR_SETTING);
             $send_code_url = $setting['sendcode_url'];

            $service = new Qunar_Service(array());
            
            $service->qunar_url = $send_code_url;
            $arr = $service->request('NoticeOrderEticketSendedRequest.xml', 'noticeOrderEticketSended', $data);

            if($arr && isset($arr->message)){
                return $arr->message;
            }
        }
        catch(Exception $e){
            return false;
        }

    }

}
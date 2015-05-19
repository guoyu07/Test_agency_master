<?php
/**
 * Created by PhpStorm.
 * User: grg
 * Date: 10/14/14
 * Time: 4:09 PM
 */

/**
 *  短信接口
 *
 *	http://sdk999ws.eucp.b2m.cn:8080?cdkey=0SDK-EMY-0130-AAAAA&password=123456&phone=1333333333,13444444444&message=单发即时短信测试&addserial=10086
 *
 * cdkey	用户序列号。
 * password	用户密码
 * phone	手机号码（最多1000个），多个用英文逗号(,)隔开。
 * message	短信内容（UTF-8编码）（最多500个汉字或1000个纯英文）。
 * addserial	附加号（最长10位，可置空）。
 *
 * 2013-09-04
 *
 * @author  liuhe(liuhe009@gmail.com)
 * @version 1.0
 */
class SMS extends CComponent
{
	private $_host       = 'http://sdk999ws.eucp.b2m.cn';
	private $_port       = '8080';
	private $_cdkey      = '9SDK-EMY-0999-JBTRP';
	private $_password   = '771459';

	/**
	 * 短信内容
	 *
	 * 在线支付：
	 *【一级票务】-【二级票务】【数量】，订单号：【订单号】，游玩时间：【游玩时间】。（分割）二维码：【二维码链接】【签名】
	 * 例：（第一条）武夷山-成人票3张，订单号：GBX98237892，游玩时间：2014-05-01。（第二条）二维码：http://ihuilian.com/GBX98237892【优联票务】
	 * 景区到付：
	 * 【一级票务】-【二级票务】【数量】，游玩时间：【游玩时间】。请至景区窗口支付并取票。（分割）二维码：【二维码链接】【签名】
	 * 例：（第一条）武夷山-成人票3张，游玩时间：2014-05-01。请至景区窗口支付并取票。（第二条）二维码：http://ihuilian.com/GBX98237892【优联票务】
	 */
	public function _getCreateOrderContent($orderInfo)
	{
		$str = '';
		$str .= '您已成功预订 '.$orderInfo['order_item'][0]['name']." ".$orderInfo['nums'].' 张，订单号：'.$orderInfo['id'].'，可于：'.$orderInfo['order_item'][0]['useday'].'当日游玩，';
		if($orderInfo['owner_card']){
			$str .='通过身份证 '.$orderInfo['owner_card'].' 换取入园门票或';
		}

		$str.='通过以下二维码链接进行手机二维码：http://'.PI_APP_DOMAIN.'/qrcode_index_'.$orderInfo['id'].'.html 入园';
		if($orderInfo['pay_type'] == 'offline') {
			$str .= '请至景区窗口支付并取票。';
		}

		//须加签名，不然发不出去
		return urlencode($str);
	}

	/**
	 * 创建订单或支付成功 发送短信
	 *
	 * @param mixed $orderId 订单信息
	 * @param reference $msg
	 * @return bool
	 *
	 */
	public function sendCreateOrderSms($orderId, &$msg)
	{
		if($orderId) {
			$ordersModel  = $this->load->model('orders');
			$orderInfo    = $ordersModel->getOne(array('id' => $orderId));
			if($orderInfo) {
				if($orderInfo['send_sms_nums'] >= 5) {
					$msg = '短信已经发送5次，超过上限';
					return false;
				}
				$ownerMobile       = $orderInfo['owner_mobile'];
				$user_id  = $orderInfo['created_by'];//获得下单的人
				$userInfo = $this->load->model('users')->getOne(array('id'=>$user_id));

				$orderInfo    = $this->load->model('orders')->getOneRelate($orderInfo, 'order_item,landscape,buyer_organization');
				$agencyMobile 	   = $userInfo['mobile'];
				$validateTool = $this->load->tool('validate');
				$content = $this->_getCreateOrderContent($orderInfo);
				if($validateTool->validate('mobile', $ownerMobile)) {
					//如果购票的旅行社要给游客发送短信
					if($orderInfo['buyer_organization']['sms_to_buyer']=="yes"){
						$sendResult = $this->sendSMS($ownerMobile, $content);
						if (!$sendResult) {
							$msg = '短信发送失败！';
							return false;
						} else {
							//更新短信发送成功次数
							$ordersModel->update(array('send_sms_nums' => $orderInfo['send_sms_nums'] + 1), array('id' => $orderId));
						}
					}
				}
				if($validateTool->validate('mobile', $agencyMobile)) {
					//如果购票的旅行社要给旅行社发送短信
					if($orderInfo['buyer_organization']['sms_to_agency']=="yes"){
						$sendResult = $this->sendSMS($agencyMobile, $content);
						if (!$sendResult) {
							$msg = '短信发送失败！';
							return false;
						}
					}
				}
				return true;
			} else {
				$msg = '订单不存在';
				return false;
			}
		} else {
			$msg = '订单号不能为空';
			return false;
		}
	}

	/**
	 * 单一号码，单一内容下发
	 *
	 * @param string $mobile
	 * @param string $content
	 *  * @return String
	 */
     public function sendSMS($mobile, $content,$types) {
	$result = SendMessage::api()->send(array('mobile'=>$mobile, 'content'=>$content,'type'=>$types));
        // var_dump($result);die;
        if ($result && $result['code'] == 'succ') {
            return true;
        } else {
            return false;
        }
    }
}

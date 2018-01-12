<?php
namespace Common\Model;
use Common\Action\Model;
class EmailModel extends Model {
	public function email($address,$username,$title,$content){
		require THINK_PATH.'/Vendor/Email/class.phpmailer.php';
		$mail = new PHPMailer();//建立邮件发送类
		$mail->IsSMTP(); // 使用SMTP方式发送 
		$mail->Host = "smtp.qq.com"; // 您的企业邮局域名 
		$mail->SMTPAuth = true; // 启用SMTP验证功能 
		$mail->Username = "271513820"; // 邮局用户名(请填写完整的email地址)
		$mail->Password = "123456"; // 邮局密码
		$mail->From = "271513820@qq.com"; // 邮件发送者email地址
		$mail->FromName = C('site_name');// 发件人姓名
		$mail->AddAddress($address, $username);//收件人地址,可以替换成任何想要接收邮件的email信箱,格式是AddAddress("收件人email","收件人姓名")
		$mail->Subject = $title; //邮件标题
		$mail->Body = $content; //邮件内容
		//$mail->AddReplyTo("", "");
		//$mail->AddAttachment("/var/tmp/file.tar.gz"); // 添加附件
		//$mail->IsHTML(true); // set email format to HTML //是否使用HTML格式
		//$mail->AltBody = "This is the body in plain text for non-HTML mail clients"; //附加信息，可以省略
		if(!$mail->Send()) {
			echo "邮件发送失败. <p>";
			echo "错误原因: " . $mail->ErrorInfo;
			exit;
		}
		echo "邮件发送成功。"; 
	}	
}
?>
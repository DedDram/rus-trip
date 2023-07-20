<?php

namespace Services;

use Services\PHPMailer\PHPMailer;
use Services\PHPMailer\Exception;

class EmailSender
{
    /**
     * @throws Exception
     */
    public static function Mailer($email, $message, $subject, $onlyText): bool
    {
        $address = (require __DIR__ . '/../settings.php')['mail']['address'];
        $password = (require __DIR__ . '/../settings.php')['mail']['password'];
        $domen = (require __DIR__ . '/../settings.php')['mail']['domen'];
        // Формирование заголовка письма
        $mail = new PHPMailer();
        //Enable SMTP debugging.
        //$mail->SMTPDebug = 3;
        //Set PHPMailer to use SMTP.
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';
        //Set this to true if SMTP host requires authentication to send email
        $mail->SMTPAuth = true;
        // Настройки вашей почты
        $mail->Host = 'smtp.mail.ru'; // SMTP сервера вашей почты
        $mail->Username = $address; // Логин на почте
        $mail->Password = $password; // Пароль на почте
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        $mail->setFrom($address, $domen); // Адрес самой почты и имя отправителя
        //From email address and name
        $mail->From = $address;
        $mail->FromName = $domen;
        //To address and name
        $mail->addAddress("$email");
        //Address to which recipient will reply
        $mail->addReplyTo($address, "Reply");
        //CC and BCC (слепая копия)
        //$mail->addCC("cc@example.com");
        // $mail->addBCC("zakaz@ultrapart.ru");
        //Send HTML or Plain Text email
        $mail->isHTML(true);
        // Формирование тела письма
        $mail->Subject = $subject;
        $mail->AltBody = strip_tags($message);
        $mail->Body = $message;
        // отправка сообщения
        try {
            $mail->send();
            return true;
        } catch (Exception $e) {
            throw new Exception('Ошибка при отправке письма: ' . $e->getMessage());
        }

    }


    /**
     * @throws Exception
     */
    public static function send(string $email, string $subject, string $templateName, array $templateVars = []): bool
    {
        extract($templateVars);
        ob_start();
        require dirname(__DIR__, 1).'/templates/mail/' . $templateName;
        $body = ob_get_contents();
        ob_end_clean();
        return self::Mailer($email, $body, $subject, $body);
    }

}
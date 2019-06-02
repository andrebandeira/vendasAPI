<?php

namespace Core\Util;

use Core\Exception\MainException;
use Zend\Mail\Address;
use Zend\Mail\Header\ContentType;
use Zend\Mail\Message;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Mime;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Validator\EmailAddress as EmailAddressValidator;
use Zend\Validator\Hostname;

class MailUtil
{

    public static function send($destinatario, $assunto, $conteudo,
        $smtpOptions, $anexos = null
    ) {
        $destinatario = mb_strtolower($destinatario);

        self::validaEmail($destinatario);

        $message = new Message();

        $message->addTo($destinatario);

        $message->addFrom(
            new Address(
                $smtpOptions->getConnectionConfig()['username'],
                'Vendas'
            )
        );

        $message->setSubject($assunto);

        $htmlPart = new MimePart($conteudo);
        $htmlPart->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
        $htmlPart->type = 'text/html; charset=UTF-8';

        $textPart = new MimePart($conteudo);
        $textPart->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
        $textPart->type = 'text/plain; charset=UTF-8';

        $body = new MimeMessage();

        if ($anexos) {
            $content = new MimeMessage();
            $content->addPart($htmlPart);
            $content->addPart($textPart);

            $contentPart = new MimePart($content->generateMessage());
            $contentPart->type = 'multipart/alternative;' . PHP_EOL .
                    ' boundary="' . $content->getMime()->boundary() . '"';

            $body->addPart($contentPart);
            $messageType = 'multipart/related';

            foreach ($anexos as $anexo) {
                $objeto = new MimePart($anexo['conteudo']);
                $objeto->filename    = $anexo['nome'];
                $objeto->type        = Mime::TYPE_OCTETSTREAM;
                $objeto->encoding    = Mime::ENCODING_BASE64;
                $objeto->disposition = Mime::DISPOSITION_ATTACHMENT;

                $body->addPart($objeto);
            }

        } else {
            $body->setParts(array($textPart, $htmlPart));
            $messageType = 'multipart/alternative';
        }

        $message->setBody($body);
        /* @var ContentType $contentType*/
        $contentType = $message->getHeaders()->get('content-type');
        $contentType->setType($messageType);

        //Cria as opções para envio
        $transport = new SmtpTransport($smtpOptions);
        $transport->send($message);
    }

    public static function validaEmail($email)
    {
        $emailAddressValidator = new EmailAddressValidator(
            Hostname::ALLOW_DNS | Hostname::ALLOW_LOCAL
        );

        if (! $emailAddressValidator->isValid($email)) {
            throw new MainException(
                'Atenção. O e-mail '.$email.' não é válido.'
            );
        }
    }

    public static function getSMTPOptions($login, $pass, $smtp, $ssl, $port) {
        return new SmtpOptions(array(
            'name' => 'email',
            'host' => $smtp,
            'connection_class' => 'login',
            'port' => $port,
            'connection_config' => array(
                'ssl' => $ssl,
                'username' => $login,
                'password' => $pass
            ),
        ));
    }
}
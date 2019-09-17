<?php

namespace App\Service\email;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use function Symfony\Component\Debug\Tests\testHeader;

/**
 * Class ActivationEmail
 *
 * @package App\Service\email
 */
class ActivationEmail
{
    /**
     * @var $this
     */
    private $message;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * ActivationEmail constructor.
     *
     * @param \Swift_Mailer $mailer
     * @param string        $email
     * @param string        $body
     */
    public function __construct(\Swift_Mailer $mailer, $email, $body)
    {
        $this->mailer = $mailer;
        $this->message = (new \Swift_Message('Activate you account'))
            ->setFrom('tlarousse3@gmail.com')
            ->setTo($email)
            ->setBody($body, 'text/html');
    }

    /**
     * Send the email
     */
    public function sendActivationEmail()
    {
        $this->mailer->send($this->message);
    }
}
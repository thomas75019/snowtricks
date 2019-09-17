<?php

namespace App\Service\email;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use function Symfony\Component\Debug\Tests\testHeader;

/**
 * Class SecurityEmail
 *
 * @package App\Service\email
 */
class SecurityEmail
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
     * SecurityEmail constructor.
     *
     * @param \Swift_Mailer $mailer
     * @param string        $email
     * @param string        $body
     * @param string        $subject
     */
    public function __construct(\Swift_Mailer $mailer, $email, $body, $subject)
    {
        $this->mailer = $mailer;
        $this->message = (new \Swift_Message($subject))
            ->setFrom('tlarousse3@gmail.com')
            ->setTo($email)
            ->setBody($body, 'text/html');
    }

    /**
     * Send the email
     */
    public function send()
    {
        $this->mailer->send($this->message);
    }
}
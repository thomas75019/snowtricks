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
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param string        $token
     * @param string        $email
     * @param \Swift_Mailer $mailer
     */
    public function sendActivationEmail($email, $body)
    {
        $message = (new \Swift_Message('Activate you account'))
            ->setFrom('tlarousse3@gmail.com')
            ->setTo($email)
            ->setBody($body);

        $this->mailer->send($message);
    }
}
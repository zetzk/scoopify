<?php
namespace src\support;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    private PHPMailer $mailer;

    public function __construct(
        string $chartSet = "UTF-8",
        bool $isSMTP = true,
        string $host = "mail.amsted-maxion.com.br",
        bool $isSMTPAuth = true,
        string $username = "agenda@amsted-maxion.com.br",
        string $password = "agenda$\$amcr##16",
        string $from = "agenda@amsted-maxion.com.br",
        string $fromName = "Company name",
        bool $isHTML = true,
    ) {
        $this->mailer = new PHPMailer(true);
        $this->settingsPHPMailer(
            $chartSet,
            $isSMTP,
            $host,
            $isSMTPAuth,
            $username,
            $password,
            $from,
            $fromName,
            $isHTML
        );
    }

    /**
     * Method responsible for performing the settings for the phpmailer
     * @return void
     */
    public function settingsPHPMailer(
        string $chartSet,
        bool $isSMTP,
        string $host,
        bool $isSMTPAuth,
        string $username,
        string $password,
        string $from,
        string $fromName,
        bool $isHTML,
    ): void {
        $this->mailer->CharSet = $chartSet;
        if ($isSMTP)
            $this->mailer->IsSMTP();
        $this->mailer->Host = $host;
        if ($isSMTPAuth)
            $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $username;
        $this->mailer->Password = $password;
        $this->mailer->From = $from;
        $this->mailer->FromName = $fromName;
        if ($isHTML)
            $this->mailer->IsHTML(true);
    }

    /**
     * Responsible for configuring the recipients
     * @param string|array<int, string> $addressees  array empty as default
     * @return self
     */
    public function setAddressees(string|array $addressees = []): self
    {
        if (is_array($addressees))
            foreach ($addressees as $address) {
                $this->mailer->addAddress($address);
            }
        else $this->mailer->addAddress($addressees);

        return $this;
    }

    /**
     * Responsible for configuring the body
     * @param string $body
     * @return self
     */
    public function setBody(string $body): self
    {
        $this->mailer->Body = $body;
        return $this;
    }


    /**
     * Responsible for configuring the title
     * @param string $subject
     * @return self
     */
    public function setSubject(string $subject): self
    {
        $this->mailer->Subject = $subject;
        return $this;
    }

    /**
     * Responsible for shooting the email
     * @return bool
     */
    public function fire(): bool
    {
        try {
            $this->mailer->send();
            $this->setAddressees();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

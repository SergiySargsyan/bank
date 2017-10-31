<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Operation
 *
 * @ORM\Table(name="operation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OperationRepository")
 */
class Operation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="string", length=255)
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="value_UAH", type="decimal", precision=10, scale=2)
     */
    private $valueUAH;

    /**
     * @var string
     *
     * @ORM\Column(name="value_USD", type="decimal", precision=10, scale=2)
     */
    private $valueUSD;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return Operation
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set valueUAH
     *
     * @param string $valueUAH
     *
     * @return Operation
     */
    public function setValueUAH($valueUAH)
    {
        $this->valueUAH = $valueUAH;

        return $this;
    }

    /**
     * Get valueUAH
     *
     * @return string
     */
    public function getValueUAH()
    {
        return $this->valueUAH;
    }

    /**
     * Set valueUSD
     *
     * @param string $valueUSD
     *
     * @return Operation
     */
    public function setValueUSD($valueUSD)
    {
        $this->valueUSD = $valueUSD;

        return $this;
    }

    /**
     * Get valueUSD
     *
     * @return string
     */
    public function getValueUSD()
    {
        return $this->valueUSD;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Operation
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}


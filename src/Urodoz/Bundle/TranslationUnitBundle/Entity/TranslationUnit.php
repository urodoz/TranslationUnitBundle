<?php

/*
 * This file is part of the UrodozTranslationUnit bundle.
 *
 * (c) Albert Lacarta <urodoz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Urodoz\Bundle\TranslationUnitBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="urodoz_translationunit", uniqueConstraints={@ORM\UniqueConstraint(name="translationunitrelation_idx", columns={"translatableunit_id", "locale"})})
 */
class TranslationUnit
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="TranslatableUnit")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $translatableUnit;

    /**
     * @ORM\Column(type="string", length=5)
     */
    protected $locale;

    /**
     * @ORM\Column(type="text")
     */
    protected $translation;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set translation
     *
     * @param  string          $translation
     * @return TranslationUnit
     */
    public function setTranslation($translation)
    {
        $this->translation = $translation;

        return $this;
    }

    /**
     * Get translation
     *
     * @return string
     */
    public function getTranslation()
    {
        return $this->translation;
    }

    /**
     * Set locale
     *
     * @param  string          $locale
     * @return TranslationUnit
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set translatableUnit
     *
     * @param  \Urodoz\Bundle\TranslationUnitBundle\Entity\TranslatableUnit $translatableUnit
     * @return TranslationUnit
     */
    public function setTranslatableUnit(\Urodoz\Bundle\TranslationUnitBundle\Entity\TranslatableUnit $translatableUnit = null)
    {
        $this->translatableUnit = $translatableUnit;

        return $this;
    }

    /**
     * Get translatableUnit
     *
     * @return \Urodoz\Bundle\TranslationUnitBundle\Entity\TranslatableUnit
     */
    public function getTranslatableUnit()
    {
        return $this->translatableUnit;
    }
}

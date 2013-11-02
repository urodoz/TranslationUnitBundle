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
 * @ORM\Table(name="urodoz_translatableunit")
 */
class TranslatableUnit
{

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=36)
     */
    protected $id;

    /**
     * Set id
     *
     * @param  string           $uuid
     * @return TranslatableUnit
     */
    public function setId($uuid)
    {
        $this->id = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}

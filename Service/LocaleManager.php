<?php

/*
 * This file is part of the UrodozTranslationUnit bundle.
 *
 * (c) Albert Lacarta <urodoz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Urodoz\Bundle\TranslationUnitBundle\Service;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Urodoz\Bundle\TranslationUnitBundle\Entity\TranslatableUnit;
use Urodoz\Bundle\TranslationUnitBundle\Entity\TranslationUnit;
use Urodoz\Bundle\TranslationUnitBundle\DependencyInjection\UrodozTranslationUnitExtension;

class LocaleManager implements ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Returns the configured locales
     *
     * @return array
     */
    public function getLocales()
    {
        return $this->container->getParameter(UrodozTranslationUnitExtension::PARAM_KEY_LOCALES_ENABLED);
    }

    public function getTranslation($uuid, $locale)
    {
        $dql = " SELECT t.translation FROM UrodozTranslationUnitBundle:TranslationUnit t "
                . " WHERE t.translatableUnit = :translatableUnit AND t.locale = :locale "
                ;
        $result = $this->container->get("doctrine")
                ->getManager()
                ->createQuery($dql)
                ->setParameters(array(
                    "translatableUnit" => $uuid,
                    "locale" => $locale,
                ))
                ->getSingleResult()
                ;
        if(is_array($result) && array_key_exists("translation", $result)) return $result["translation"];
        throw new \Exception("Cannot find translation for UUID{".$uuid."} and locale{".$locale."}");
    }

    public function getTranslationsPackage(array $uuids)
    {
        $dql = " SELECT t,tu FROM UrodozTranslationUnitBundle:TranslationUnit t "
                . " INNER JOIN t.translatableUnit tu "
                . " WHERE t.translatableUnit IN (:uuids) "
                . " AND t.locale IN (:locales) "
                ;
        $result = $this->container->get("doctrine")
                ->getManager()
                ->createQuery($dql)
                ->setParameter("uuids", $uuids)
                ->setParameter("locales", $this->getLocales())
                ->getResult(\PDO::FETCH_ASSOC)
                ;
        $return = array();

        foreach ($result as $item) {
            if (!isset($return[$item["translatableUnit"]["id"]])) {
                $return[$item["translatableUnit"]["id"]] = array();
                foreach ($this->getLocales() as $locale) {
                    //Init empty the initial return array with all locales
                    $return[$item["translatableUnit"]["id"]][$locale] = null;
                }
            }
            if (in_array($item["locale"], array_keys($return[$item["translatableUnit"]["id"]]))) {
                $return[$item["translatableUnit"]["id"]][$item["locale"]] = $item["translation"];
            }
        }

        return $return;
    }

    public function getTranslations($uuid)
    {
        $manager = $this->container->get("doctrine")->getManager();

        $dql = " SELECT t.translation, t.locale FROM UrodozTranslationUnitBundle:TranslationUnit t "
            . " WHERE t.translatableUnit = :translatableUnit "
            ;
        $query = $manager->createQuery($dql)->setParameter("translatableUnit", $uuid);
        $res = $query->getResult();

        $return = array();
        foreach($this->getLocales() as $locale) $return[$locale] = null;
        foreach ($res as $item) {
            if (in_array($item["locale"], array_keys($return))) {
                $return[$item["locale"]] = $item["translation"];
            }
        }

        return $return;
    }

    public function setTranslation(
            $uuid,
            $locale,
            $translation
            )
    {
        //Search the translatable unit
        $uuidEntity = $this->container->get("doctrine")
                ->getRepository("UrodozTranslationUnitBundle:TranslatableUnit")->find($uuid);
        if(!$uuidEntity) throw new \Exception("UUID {".$uuid."} not found");

        if (!in_array($locale, $this->getLocales())) {
            throw new \Exception("Locale {".$locale."} not enabled on configuration. Available locales are [".implode(",", $this->getLocales())."]");
        }

        //Find translation to update
        $transUnit = $this->container->get("doctrine")
                ->getRepository("UrodozTranslationUnitBundle:TranslationUnit")->findOneBy(array(
            "locale" => $locale,
            "translatableUnit" => $uuidEntity,
        ));
        if (!$transUnit) {
            $transUnit = new TranslationUnit();
            $transUnit->setLocale($locale);
            $transUnit->setTranslatableUnit($uuidEntity);
            $this->container->get("doctrine")->getManager()->persist($transUnit);
        }
        $transUnit->setTranslation($translation);
        $this->container->get("doctrine")->getManager()->flush();
    }

    /**
     * Returns a freshly created UUID
     *
     * @return string
     */
    public function generateNewUuid()
    {
        $manager = $this->container->get("doctrine")->getManager();
        $uuid = $this->generetaUUID();
        $exists = true;
        while ($exists) {
            $dql = " SELECT COUNT(t.id) FROM UrodozTranslationUnitBundle:TranslatableUnit t "
                    . " WHERE t.id = :uuid "
                    ;
            $query = $manager->createQuery($dql)
                    ->setParameter("uuid", $uuid)
                    ;
            if ($query->getSingleScalarResult()==0) {
                $exists = false;
            } else {
                $uuid = $this->generetaUUID();
            }
        }

        //We have an unique UUID
        $transUnit = new TranslatableUnit();
        $transUnit->setId($uuid);
        $manager->persist($transUnit);
        $manager->flush();

        return $transUnit->getId();
    }

    /**
     * Generates a UUID
     *
     * @return string
     */
    private function generetaUUID()
    {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

}

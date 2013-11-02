<?php

/*
 * This file is part of the UrodozTranslationUnit bundle.
 *
 * (c) Albert Lacarta <urodoz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Urodoz\Bundle\TranslationUnitBundle\Tests\Service;

use Urodoz\Bundle\TranslationUnitBundle\Service\LocaleManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Urodoz\Bundle\TranslationUnitBundle\DependencyInjection\UrodozTranslationUnitExtension;

/**
 * @code
 * phpunit -c app/ vendor/urodoz/translationunit/src/Urodoz/Bundle/TranslationUnitBundle/Tests/Service/LocaleManagerTest.php
 * @endcode
 *
 * @author Albert Lacarta <urodoz@gmail.com>
 */
class LocaleManagerTest extends WebTestCase
{

    /**
     * @code
     * phpunit -v --filter testGenerateNewUUID -c app/ vendor/urodoz/translationunit/src/Urodoz/Bundle/TranslationUnitBundle/Tests/Service/LocaleManagerTest.php
     * @endcode
     */
    public function testGenerateNewUUID()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $service = $container->get("urodoz.translation_manager");
        $this->assertTrue($service instanceof LocaleManager);

        $newUUID = $service->generateNewUuid();
        $this->assertEquals(36, strlen($newUUID));
    }

    /**
     * @code
     * phpunit -v --filter testSetAndFetchTransalation -c app/ vendor/urodoz/translationunit/src/Urodoz/Bundle/TranslationUnitBundle/Tests/Service/LocaleManagerTest.php
     * @endcode
     */
    public function testSetAndFetchTransalation()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $locales = $container->getParameter(UrodozTranslationUnitExtension::PARAM_KEY_LOCALES_ENABLED);

        $service = $container->get("urodoz.translation_manager");
        $this->assertTrue($service instanceof LocaleManager);

        $newUUID = $service->generateNewUuid();
        $this->assertEquals(36, strlen($newUUID));

        foreach ($locales as $locale) {
            $randomTranslation = uniqid("trans_test");
            $service->setTranslation($newUUID, $locale, $randomTranslation);
            $translation = $service->getTranslation($newUUID, $locale);
            $translations = $service->getTranslations($newUUID);
            $translationPackage = $service->getTranslationsPackage(array($newUUID));

            /*
             * Asserting translation
             */
            $this->assertEquals($randomTranslation, $translation);
            /*
             * Asserting translations
             */
            $this->assertEquals($randomTranslation, $translations[$locale]);
            /*
             * Asserting translationPackage
             */
            $this->assertEquals($randomTranslation, $translationPackage[$newUUID][$locale]);
        }
    }

}

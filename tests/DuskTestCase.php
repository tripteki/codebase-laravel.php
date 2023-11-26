<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Laravel\Dusk\TestCase as BaseTestCase;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @beforeClass
     *
     * @return void
     */
    public static function prepare()
    {
        if (! static::runningInSail()) {
            static::startChromeDriver();
        }
    }

    /**
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        $options = (new ChromeOptions)->addArguments(collect(
            [
                $this->shouldStartMaximized() ? "--start-maximized" : "--window-size=1920,1080",

            ])->unless($this->hasHeadlessDisabled(), function ($items) {
                return $items->merge(
                    [
                        "--disable-gpu",
                        "--headless",
                    ]);
            })->all());

        return RemoteWebDriver::create($_ENV["DUSK_DRIVER_URL"] ?? "http://localhost:9515", DesiredCapabilities::chrome()->setCapability(ChromeOptions::CAPABILITY, $options));
    }

    /**
     * @return bool
     */
    protected function hasHeadlessDisabled()
    {
        return isset($_SERVER["DUSK_HEADLESS_DISABLED"]) || isset($_ENV["DUSK_HEADLESS_DISABLED"]);
    }

    /**
     * @return bool
     */
    protected function shouldStartMaximized()
    {
        return isset($_SERVER["DUSK_START_MAXIMIZED"]) || isset($_ENV["DUSK_START_MAXIMIZED"]);
    }
}

<?php

namespace Src\V1\Common\Traits;

use Illuminate\Support\Str;

trait ServiceProviderTrait
{
    /**
     * @var string
     */
    protected $versionModule = "V1";

    /**
     * @var string
     */
    protected $moduleName;

    /**
     * @param string
     * @return void
     */
    public function applyVersionModule($versionModule)
    {
        $this->versionModule = $versionModule;
    }

    /**
     * @return string
     */
    public function versionModule()
    {
        return $this->versionModule;
    }

    /**
     * @return string
     */
    public function versionLower()
    {
        return Str::lower($this->versionModule());
    }

    /**
     * @return string
     */
    public function versionUpper()
    {
        return Str::upper($this->versionModule());
    }

    /**
     * @param string
     * @return void
     */
    public function applyModuleName($moduleName)
    {
        $this->moduleName = $moduleName;
    }

    /**
     * @return string
     */
    public function moduleName()
    {
        return $this->moduleName;
    }

    /**
     * @return string
     */
    public function moduleLower()
    {
        return Str::lower($this->moduleName());
    }

    /**
     * @return string
     */
    public function moduleUpper()
    {
        return Str::upper($this->moduleName());
    }

    /**
     * @param string $path
     * @return string
     */
    public function modulePath($path = "/")
    {
        return base_path("src/".$this->versionUpper()."/".$this->moduleLower().$path);
    }
};

<?php

namespace Src\V1\Api\I18N\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Src\V1\Api\Common\Services\Service as BaseService;

class I18NService extends BaseService
{
    /**
     * @return string
     */
    public function fallbackLang(): string
    {
        return config("app.fallback_locale");
    }

    /**
     * @return array
     */
    public function availableLangs(): array
    {
        return array_map(fn ($directory): string => basename($directory), File::directories(lang_path()));
    }

    /**
     * @param string $lang
     * @return void
     */
    protected function setLang(string $lang): void
    {
        if (in_array($lang, $this->availableLangs())) App::setLocale($lang);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param string $lang
     * @return void
     */
    public function setLanguageFromSession(Request $request, string $lang): void
    {
        session(["lang" => $lang]);
        $this->setLang($lang);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function getLanguageFromSession(Request $request): string
    {
        if ($language = session("lang")) {
            $this->setLang($language);
        }

        return $language ?? $this->fallbackLang();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function getLanguageFromCustomHeader(Request $request): string
    {
        if ($language = $request->header("x-lang")) $this->setLang($language);

        return $language ?? $this->fallbackLang();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function getLanguageFromQueryString(Request $request): string
    {
        if ($language = $request->query("lang")) $this->setLang($language);

        return $language ?? $this->fallbackLang();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function getLanguageFromAcceptHeader(Request $request): string
    {
        $language = null;

        if ($acceptLanguage = $request->header("accept-language")) {

            $languages = explode(",", $acceptLanguage);
            $language = strtok($languages[0], "-");

            $this->setLang($language);
        }

        return $language ?? $this->fallbackLang();
    }
}

<?php

namespace Modules\I18N\App\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use App\Services\Service as BaseService;

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
     * @return string|null
     */
    public function getLanguageFromCustomHeader(Request $request): ?string
    {
        return $this->applyLang($request->header("x-lang"));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    public function getLanguageFromQueryString(Request $request): ?string
    {
        $language = $request->query("lang");

        return $this->applyLang(is_string($language) ? $language : null);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    public function getLanguageFromAcceptHeader(Request $request): ?string
    {
        foreach (explode(",", (string) $request->header("accept-language", "")) as $part) {
            $language = $this->applyLang(strtolower(trim(explode("-", explode(";", $part)[0])[0])));

            if ($language !== null) {
                return $language;
            }
        }

        return null;
    }

    /**
     * @param string|null $lang
     * @return string|null
     */
    protected function applyLang(?string $lang): ?string
    {
        if ($lang === null || $lang === "" || ! in_array($lang, $this->availableLangs(), true)) {
            return null;
        }

        App::setLocale($lang);

        return $lang;
    }
}

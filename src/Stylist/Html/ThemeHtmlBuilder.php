<?php

namespace FloatingPoint\Stylist\Html;

use FloatingPoint\Stylist\Facades\StylistFacade;
use Illuminate\Routing\UrlGenerator;
use Spatie\Html\Html;

class ThemeHtmlBuilder
{
    private Html $html;

    private UrlGenerator $url;

    public function __construct(Html $html, UrlGenerator $url)
    {
        $this->html = $html;
        $this->url = $url;
    }

    /**
     * Generate a link to a JavaScript file.
     */
    public function script(string $url, array $attributes = [], ?bool $secure = null): string
    {
        $asset = $this->url->asset($this->assetUrl($url), $secure);

        return $this->html->element('script')
            ->attributes($attributes)
            ->attribute('src', $asset);
    }

    /**
     * Generate a link to a CSS file. With Stylist, this could actually generate
     * numerous style tags, due to CSS inheritance requirements.
     */
    public function style(string $url, array $attributes = [], ?bool $secure = null): string
    {
        $styles = [];
        $theme = StylistFacade::current();

        // If our theme has a parent, we want its stylesheet, as well.
        // @todo: This is dog-ugly - need to figure out a better approach.
        if ($theme->hasParent()) {
            $parent = StylistFacade::get($theme->getParent());
            StylistFacade::activate($parent);
            $styles[] = $this->style($url, $attributes, $secure);
            StylistFacade::activate($theme);
        }

        $asset = $this->url->asset($this->assetUrl($url), $secure);
        $attributes = array_merge([
            'media' => 'all',
            'type' => 'text/css',
        ], $attributes);

        $styles[] = $this->html->element('link')
            ->attributes($attributes)
            ->attribute('rel', 'stylesheet')
            ->attribute('href', $asset);

        return implode("\n", $styles);
    }

    /**
     * Generate an HTML image element.
     */
    public function image(string $url, ?string $alt = null, array $attributes = [], ?bool $secure = null): string
    {
        $asset = $this->url->asset($this->assetUrl($url), $secure);

        if (! is_null($alt)) {
            $attributes['alt'] = $alt;
        }

        return $this->html->img($asset)
            ->attributes($attributes);
    }

    /**
     * Returns the theme's public URI location. This is not a full URL. If you wish
     * for a full URL, simply add the site's URL configuration to this path.
     */
    public function url(string $file = ''): string
    {
        return url($this->assetUrl($file));
    }

    /**
     * Generate a HTML link to an asset.
     */
    public function linkAsset(string $url, ?string $title = null, array $attributes = [], ?bool $secure = null): string
    {
        $asset = $this->url->asset($this->assetUrl($url), $secure);

        return $this->html->a($asset)
            ->text($title ?: $asset)
            ->attributes($attributes);
    }

    /**
     * Do a few checks to get the theme path for a given asset url.
     */
    protected function assetUrl(string $url): string
    {
        if ($this->url->isValidUrl($url)) {
            return $url;
        }

        $theme = StylistFacade::current();

        if ($theme) {
            $themePath = $theme->getAssetPath();

            $url = "themes/$themePath/$url";
        }

        return $url;
    }
}

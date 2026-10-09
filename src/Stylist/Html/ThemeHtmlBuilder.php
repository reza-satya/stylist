<?php
namespace FloatingPoint\Stylist\Html;

use FloatingPoint\Stylist\Facades\StylistFacade;
use Illuminate\Routing\UrlGenerator;
use Spatie\Html\Html;

class ThemeHtmlBuilder
{
    /**
     * @var Html
     */
    private $html;

    /**
     * @var UrlGenerator
     */
    private $url;

    /**
     * @param Html $html
     * @param UrlGenerator $url
     */
    public function __construct(Html $html, UrlGenerator $url)
    {
        $this->html = $html;
        $this->url = $url;
    }

    /**
     * Generate a link to a JavaScript file.
     *
     * @param  string  $url
     * @param  array   $attributes
     * @param  bool    $secure
     * @return string
     */
    public function script($url, $attributes = array(), $secure = null)
    {
        $asset = $this->url->asset($this->assetUrl($url), $secure);

        return $this->html->element('script')
            ->attributes($attributes)
            ->attribute('src', $asset);
    }

    /**
     * Generate a link to a CSS file. With Stylist, this could actually generate
     * numerous style tags, due to CSS inheritance requirements.
     *
     * @param  string  $url
     * @param  array   $attributes
     * @param  bool    $secure
     * @return string
     */
    public function style($url, $attributes = array(), $secure = null)
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
     *
     * @param  string  $url
     * @param  string  $alt
     * @param  array   $attributes
     * @param  bool    $secure
     * @return string
     */
    public function image($url, $alt = null, $attributes = array(), $secure = null)
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
     *
     * @param string $file
     * @return string
     */
    public function url($file = '')
    {
        return url($this->assetUrl($file));
    }

    /**
     * Generate a HTML link to an asset.
     *
     * @param  string  $url
     * @param  string  $title
     * @param  array   $attributes
     * @param  bool    $secure
     * @return string
     */
    public function linkAsset($url, $title = null, $attributes = array(), $secure = null)
    {
        $asset = $this->url->asset($this->assetUrl($url), $secure);

        return $this->html->a($asset)
            ->text($title ?: $asset)
            ->attributes($attributes);
    }

    /**
     * Do a few checks to get the theme path for a given asset url.
     *
     * @param string $url
     * @return string
     */
    protected function assetUrl($url)
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

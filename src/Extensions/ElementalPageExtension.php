<?php

namespace DNADesign\Elemental\Extensions;

use DNADesign\Elemental\Models\ElementalArea;
use SilverStripe\Control\Controller;
use SilverStripe\View\Parsers\HTML4Value;
use SilverStripe\View\SSViewer;
use SilverStripe\Forms\HTMLEditor\HTMLEditorConfig;

class ElementalPageExtension extends ElementalAreasExtension
{
    private static $has_one = [
        'ElementalArea' => ElementalArea::class,
    ];

    private static $owns = [
        'ElementalArea',
    ];

    private static $cascade_duplicates = [
        'ElementalArea',
    ];

    /**
     * Returns the contents of each ElementalArea has_one's markup for use in Solr search indexing
     *
     * @return string
     */
    public function getElementsForSearch()
    {
        $cmsThemes = SSViewer::get_themes();
        $userThemes = HTMLEditorConfig::getThemes();
        SSViewer::set_themes($userThemes);

        $output = [];
        foreach ($this->owner->hasOne() as $key => $class) {
            if ($class !== ElementalArea::class) {
                continue;
            }

            /** @var ElementalArea $area */
            $area = $this->owner->$key();
            if ($area) {
                $output[] = strip_tags($area->forTemplate());
            }
        }
        SSViewer::set_themes($cmsThemes);
        return implode($output);
    }

    public function MetaTags(&$tags)
    {
        $controller = Controller::curr();
        $request = $controller->getRequest();
        if ($request->getVar('ElementalPreview') !== null) {
            $html = HTML4Value::create($tags);
            $xpath = "//meta[@name='x-page-id' or @name='x-cms-edit-link']";
            $removeTags = $html->query($xpath);
            $body = $html->getBody();
            foreach ($removeTags as $tag) {
                $body->removeChild($tag);
            }
            $tags = $html->getContent();
        }
    }
}

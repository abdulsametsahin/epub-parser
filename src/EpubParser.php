<?php 

namespace abdulsametsahin;

/**
*  A Epub class to get content of an epub.
*
*  @author Abdulsamet ŞAHİN
*/
class EpubParser {

    /**  @var string $epub_path, extracted epub folder */
    public $epub_path;
    /**  @var string $items_path, path of the main content folder */
    public $items_path;
    /**  @var object $content, all of the contents of the epub */
    public $content;
    /**  @var object $items, htmls, images etc.. */
    public $items;
    /**  @var object $nav, navigation of the epub contents */
    public $nav;
    /**  @var object $cover, cover page of the epub */
    public $cover;
    /**  @var object $toc, table of the content */
    public $toc;
    /**  @var object $start, starting page */
    public $start;

    /**
    * This method loads the epub and gets all of the information about it.
    *
    * @param string $epub_path full path of epub folder..
    *
    * @return $this
    */
    public function load($epub_path)
    {
        $this->epub_path = $epub_path;

        /**
         * Getting content of the book.
         */
        $container = $this->openXML($epub_path . "/META-INF/container.xml");
        $content_path = $epub_path."/".$container->rootfiles->rootfile->a->{"full-path"};
        $this->content = $this->openXML($content_path);
        
        /**
         * Set the items path.
         */
        if (count(explode("/", $content_path)) == 3)
            $this->items_path = $epub_path . "/" . explode("/", $content_path)[1] ."/";
        else
            $this->items_path = $epub_path . "/";
            
        /**
         * Store all kind of items
         */
        foreach($this->content->manifest->item as $item)
            $this->items[$item->a->id] = (object) $item->a;
        
        $this->items = (object) $this->items;

        /**
         * Detect cover, toc and start
         */
        $this->cover = false;
        $this->toc = false;
        $this->start = false;

        foreach($this->content->guide->reference as $ref){
            if (strtolower($ref->a->type) == "cover")
                $this->cover = $ref->a;

            if (strtolower($ref->a->type) == "toc")
                $this->toc = $ref->a;

            if (strtolower($ref->a->title) == "start")
                $this->start = $ref->a;
        }
        

        /**
         * Toc file stores the navigation.
         * Some of htmls may have an uid to seperate pages.
         */
        $toc = $this->openXML($this->epub_path . "/OEBPS/toc.ncx");
        foreach ($toc->navMap->navPoint as $point) {
            $this->nav[$point->a->playOrder] = (object) [
                'label' => $point->navLabel->text,
                'src' => $point->content->a->src,
                'uid' => strpos($point->content->a->src, "#") ? explode("#", $point->content->a->src)[1] : null
            ];
            /**
             * Some of pages may have sub pages.
             */
            if (isset($point->navPoint) && count($point->navPoint)) {
                foreach ($point->navPoint as $p) {
                    $this->nav[$p->a->playOrder] = (object) [
                        'label' => $p->navLabel->text,
                        'src' => $p->content->a->src,
                        'uid' => strpos($p->content->a->src, "#") ? explode("#", $p->content->a->src)[1] : null
                    ];
                }
            }
        }
        $this->nav = (object) $this->nav;

        return $this;
    }

    /**
    * To get metadata
    *
    * @param string $key 
    *
    * @return string, array
    */
    public function getMetaData($key)
    {
        return $this->content->metadata->{$key};
    }

    /**
     * @return object, Cover page.
     */
    public function getCover()
    {
        return $this->cover;
    }

    /**
     * @return object, Table Of Contents
     */
    public function getToc()
    {
        return $this->toc;
    }

    /**
     * @return object, Starting page
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param string $xml_path, xml file path
     * 
     * @return object, content of the given xml file.
     */
    public function openXML($xml_path)
    {
        if (!file_exists($xml_path)) {
            throw new \Exception("XML File doesn't exists! Plese check the given path. ($xml_path)", 1);
        }

        $xml_content = file_get_contents($xml_path);
        $xml_content = str_replace("dc:", null, $xml_content);
        $xml = simplexml_load_string($xml_content);
        $xml = json_encode($xml);
        $xml = str_replace("@attributes", "a", $xml);
        $xml = json_decode($xml);
        return $xml; 
    }
}
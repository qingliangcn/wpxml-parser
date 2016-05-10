<?php
namespace qingliangcn\WPXmlParser;

class WPXmlParser
{
    /**
     * wordpress version
     * @var string
     */
    public $wp_version;

    /**
     * wp importor version
     * @var string
     */
    public $wp_importor_version = "";

    /**
     * posts of blog
     * @var array
     */
    public $posts = [];

    /**
     * @var array
     */
    public $author = [];

    /**
     * description of blog site
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $base_site_url;

    /**
     * @var string
     */
    public $base_blog_url;

    /**
     * @var string
     */
    public $title = "";

    /**
     * @var array
     */
    public $categories = [];

    /**
     * @var array
     */
    public $tags = [];

    /**
     * @var array
     */
    public $pages = [];

    /**
     * @var array
     */
    public $attachments = [];


    /**
     * 解析xml数据文件
     *
     * @param $xmlFile string
     * @return array
     * @throws \Exception
     */
	public function parse($xmlFile) {
        if (file_exists($xmlFile) === false) {
            throw new \Exception("File not exists: {$xmlFile}");
        }

        $this->_init();

        $result = array();

        $xml = simplexml_load_file($xmlFile);
        $generator = (string)$xml->channel->generator;
        $description = (string)$xml->channel->description;
        $title = (string)$xml->channel->title;

        $wordpressVersion = str_replace("https://wordpress.org/?v=", "", $generator);

        // ---------------------------------------
        // wp namespace
        $wpNamespace = $xml->channel->children('http://wordpress.org/export/1.2/');
        $wpImportorVersion = (string)$wpNamespace->wxr_version;

        // url
        $baseSiteUrl = (string)$wpNamespace->base_site_url;
        $baseBlogUrl = (string)$wpNamespace->base_blog_url;

        // 作者信息
        $author = $wpNamespace->author;
        $authorInfo = [];
        $authorInfo['author_id'] = (int)$author->author_id;
        $authorInfo['author_login'] = (string)$author->author_login;
        $authorInfo['author_email'] = (string)$author->author_email;
        $authorInfo['author_display_name'] = (string)$author->author_display_name;
        $authorInfo['author_first_name'] = (string)$author->author_first_name;
        $authorInfo['author_last_name'] = (string)$author->author_last_name;

        // 分类信息
        $categoryList = [];
        foreach ($wpNamespace->category as $category) {
            $categoryInfo = [];
            $categoryInfo['term_id'] = (int)$category->term_id;
            $categoryInfo['category_nicename'] = (string)$category->category_nicename;
            $categoryInfo['category_parent'] = (string)$category->category_parent;
            $categoryInfo['cat_name'] = (string)$category->cat_name;
            $categoryList[] = $categoryInfo;
        }

        // tags
        $tags = [];
        foreach ($wpNamespace->tag as $tag) {
            $tagInfo = [];
            $tagInfo['term_id'] = (int)$tag->term_id;
            $tagInfo['tag_slug'] = (string)$tag->tag_slug;
            $tagInfo['tag_name'] = (string)$tag->tag_name;
            $tags[] = $tagInfo;
        }

        $items = $xml->channel->item;
        $posts = [];
        $attachements = [];
        $pages = [];

        foreach ($items as $item) {
            $title = (string)$item->title;
            $link = (string)$item->link;
            $description = (string)$item->description;
            $pubDateTime = strftime("%Y-%m-%d %H:%M:%S", strtotime($item->pubDate));

            $content = (string)$item->children('http://purl.org/rss/1.0/modules/content/')->encoded;
            $excerpt = (string)$item->children('http://wordpress.org/export/1.2/excerpt/')->encoded;
            $creator = (string)$item->children('http://purl.org/dc/elements/1.1/')->creator;

            $itemWpNamespace = $item->children('http://wordpress.org/export/1.2/');

            $postType = (string)$itemWpNamespace->post_type;

            if ($postType == 'attachment') {
                $attachements[] =  [
                    'attachment_url' => (string)$itemWpNamespace->attachment_url,
                    'title' => $title
                ];
            } else if ($postType == 'page') {
                $pages[] = [
                        'link' => $link,
                        'pubData' => $pubDateTime,
                        'description' => $description,
                        'content' => $content,
                        'excerpt' => $excerpt,
                        'creator' => $creator,
                ];
            } else if ($postType == 'post') {
                $categories = [];
                foreach($item->category as $category)
                {
                    if($category['nicename'] != "uncategorized" && $category['domain'] == "category")
                    {
                        $categories[] = (string)$category;
                    }
                }

                $category = implode(",", $categories);

                $posts[] = [
                    'title' => $title,
                    'link' => $link,
                    'pubData' => $pubDateTime,
                    'description' => $description,
                    'content' => $content,
                    'excerpt' => $excerpt,
                    'creator' => $creator,
                    'categoryStr' => $category,
                    'categories' => $categories
                ];
            }
        }

        $this->wp_importor_version = $result['wp_importor_version'] = $wpImportorVersion;
        $this->posts = $result['posts'] = $posts;
        $this->wp_version = $result['wp_version'] = $wordpressVersion;
        $this->author = $result['author'] = $authorInfo;
        $this->description = $result['description'] = $description;
        $this->base_site_url = $result['base_site_url'] = $baseSiteUrl;
        $this->base_blog_url = $result['base_blog_url'] = $baseBlogUrl;
        $this->title = $result['title'] = $title;
        $this->categories = $result['categories'] = $categoryList;
        $this->tags = $result['tags'] = $tags;
        $this->attachments = $result['attachments'] = $attachements;
        $this->pages = $result['pages'] = $pages;

        return $result;
    }

    private function _init() {
        $this->title = "";
        $this->posts = [];
        $this->wp_version = "";
        $this->author = [];
        $this->description = "";
        $this->base_site_url = "";
        $this->base_blog_url = "";
        $this->categories = [];
        $this->wp_importor_version = "";
        $this->tags = [];
    }
}
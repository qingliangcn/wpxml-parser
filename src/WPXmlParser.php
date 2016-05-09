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
        $articles = $xml->channel->item;

        $author = $xml->channel->children('http://wordpress.org/export/1.2/')->author;

        $authorInfo = [];
        $authorInfo['author_id'] = (int)$author->author_id;
        $authorInfo['author_login'] = (string)$author->author_login;
        $authorInfo['author_email'] = (string)$author->author_email;
        $authorInfo['author_display_name'] = (string)$author->author_display_name;
        $authorInfo['author_first_name'] = (string)$author->author_first_name;
        $authorInfo['author_last_name'] = (string)$author->author_last_name;

        $baseSiteUrl = (string)$xml->channel->children('http://wordpress.org/export/1.2/')->base_site_url;
        $baseBlogUrl = (string)$xml->channel->children('http://wordpress.org/export/1.2/')->base_blog_url;

        $posts = array();

        foreach ($articles as $article) {
            $title = (string)$article->title;
            $link = (string)$article->link;
            $description = (string)$article->description;
            $pubDateTime = strftime("%Y-%m-%d %H:%M:%S", strtotime($article->pubDate));
            $content = (string)$article->children('http://purl.org/rss/1.0/modules/content/')->encoded;
            $excerpt = (string)$article->children('http://wordpress.org/export/1.2/excerpt/')->encoded;
            $creator = (string)$article->children('http://purl.org/dc/elements/1.1/')->creator;

            $categories = [];
            foreach($article->category as $category)
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

        $this->posts = $result['posts'] = $posts;
        $this->wp_version = $result['wp_version'] = $wordpressVersion;
        $this->author = $result['author'] = $authorInfo;
        $this->description = $result['description'] = $description;
        $this->base_site_url = $result['base_site_url'] = $baseSiteUrl;
        $this->base_blog_url = $result['base_blog_url'] = $baseBlogUrl;
        $this->title = $result['title'] = $title;

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
    }
}
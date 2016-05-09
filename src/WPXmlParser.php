<?php
namespace qingliangcn\WPXmlTranslator;

class WPXmlTranslator 
{
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

        $result = array();

        $xml = simplexml_load_file($xmlFile);
        $generator = (string)$xml->channel->generator;
        $description = (string)$xml->channel->description;

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

        $result['generator'] = $generator;
        $result['posts'] = $posts;
        $result['wp_version'] = $wordpressVersion;
        $result['author'] = $authorInfo;
        $result['description'] = $description;
        $result['base_site_url'] = $baseSiteUrl;
        $result['base_blog_url'] = $baseBlogUrl;

        return $result;
    }
}
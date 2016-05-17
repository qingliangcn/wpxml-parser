# wpxml-parser

translator wordpress xml data to array and object 

## 安装

###composer

```bash
composer require qingliangcn/wpxml-parser
```

###Download the Release

在[release](https://github.com/qingliangcn/wpxml-parser/releases)中选择最新版本并下载，解压后在项目中适当位置引入 src/WPXmlParser.php

## 使用

### 方法1：数组形式

```php
$parser = new \qingliangcn\WPXmlParser\WPXmlParser();
$result = $parser->parse("wordpress.xml");
```

结果格式:

```
array(7) {
  ["generator"]=>
  string(30) "https://wordpress.org/?v=4.5.2"
  ["posts"]=>
  array(72) {
    [0]=>
    array(8) {
      ["title"]=>
      string(13) "Title"
      ["link"]=>
      string(38) "http://www.xxxx.org/2007/01/21/"
      ["pubData"]=>
      string(19) "2007-01-14 00:46:06"
      ["description"]=>
      string(0) ""
      ["excerpt"]=>
      string(0) ""
      ["content"]=>
      string(0) "post content is here"
      ["creator"]=>
      string(5) "admin"
      ["categoryStr"]=>
      string(15) "PostCategory"
      ["categories"]=>
      array(1) {
        [0]=>
        string(15) "PostCategory"
      }
    }
  ["wp_version"]=>
  string(5) "4.5.2"
  ["author"]=>
  array(6) {
    ["author_id"]=>
    int(1)
    ["author_login"]=>
    string(5) "admin"
    ["author_email"]=>
    string(16) "xxx@yyy.com"
    ["author_display_name"]=>
    string(6) "authorName"
    ["author_first_name"]=>
    string(0) "xxx"
    ["author_last_name"]=>
    string(0) "yyyy"
  }
  ["description"]=>
  string(0) ""
  ["base_site_url"]=>
  string(26) "http://www.xxxx.org"
  ["base_blog_url"]=>
  string(26) "http://www.xxxx.org"
}
```

### 方法2:对象形式

```php
$parser = new \qingliangcn\WPXmlParser\WPXmlParser();

$result = $parser->parse("wordpress.xml");

$title = $parser->title;
$blogUrl = $parser->base_blog_url;
//文章列表
$posts = $parser->posts;
//分类列表
$categories = $parser->categories;
//tag列表
$tags = $parser->tags;
//多媒体(图片、附件等)列表
$attachments = $parser->attachments;
```

### 注意

如果xml文件不存在，则会抛出异常，类型为Exception.

## 返回数据

一级信息

| key | type | description |
|---|---|---|
| title | string | 博客标题 |
| wp_version | string | wordpress版本号 |
| author | array | 作者信息 |
| posts | array | 文章列表 |
| tags | array | tag列表 |
| pages | array | page列表 |
| attachments | array | 附件列表 |
| description | string | 博客描述 |
| base_site_url | string | 站点地址 |
| base_blog_url | string | 博客地址 |

作者信息(author)

| key | type | description |
|---|---|---|
| author_id | int | 作者用户ID |
| author_login | string | 作者登陆用户名 |
| author_email | array | 作者email |
| author\_display_name | array | 作者显示名称 |
| author\_first_name | string | 作者名 |
| author\_last_name | string | 作者姓 |


文章信息(posts)

| key | type | description |
|---|---|---|
| title | string | 文章标题 |
| link | string | 文章地址 |
| pubData | string | 格式化的时间, 2015-06-05 23:11:03 |
| description | string | 文章描述 |
| content | string | 文章内容 |
| excerpt | string |  |
| creator | string | 文章作者 |
| categoryStr | string | 分类字符串 |
| categories | array | 分类数组 |
| tags | array | tags |


## Todo

1. ~~支持page导出~~
2. 完整post信息导出
3. ~~支持多媒体导出~~
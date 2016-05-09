# wpxml-parser

translator wodpress xml data to array

## 安装

###composer

```bash
composer require qingliangcn/wpxml-parser
```

###Download the Release

在[release](https://github.com/qingliangcn/wpxml-parser/releases)中选择最新版本并下载，解压后在项目中适当位置引入 src/WPXmlParser.php


## 使用

```php
$parser = new \qingliangcn\WPXmlTranslator\WPXmlTranslator();
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

### 注意

如果xml文件不存在，则会抛出异常，类型为Exception.
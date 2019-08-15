# DUXCMS 2.1

## 多语言

### 添加语种
[在此文件](https://github.com/xiaodit/duxcms-2.0-php7/blob/master/data/config/lang.php#L6)添加其它语种

### 开启语言
1. 后台设置->多语言设置->开启
2. 选择默认语言

### 切换语言
#### 前台切换
```php
// 切换英文
http://www.domain.com/index.php?lang=en
// 切换中文
http://www.domain.com/index.php?lang=zh
```
#### 前台获取语言配置
模板调用 `$lang_list` 获取列表

```php
<?php var_dump($lang_list);?>
```

具体用法
```html
<ul>
<!--foreach{$lang_list as $key=>$vo}-->
  <li><a href="/index.php?lang={$key}">{$vo.label}</a></li>
<!--{/foreach}-->
</ul>
```

## 百度推送
模板使用 `<!--#pushBaidu-->`

## 小功能
### 栏目页
* 添加分类文章统计（支持频道、列表） `$categoryInfo['article_count']`

## 助手函数
* `hasSub($cid)` 是否有下级分类
* `articleSumByCid(int $cid, $positionId = '', $isShow = true)` 获取分类文章统计



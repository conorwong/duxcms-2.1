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
* 模板使用 `<!--#pushBaidu-->`
* 后台实时提交 系统设置->百度链接提交->开启


## 小功能
### 栏目页
* 添加分类文章统计（支持频道、列表） `$categoryInfo['article_count']`

## 助手函数
* `hasSub($cid)` 是否有下级分类
* `articleSumByCid(int $cid, $positionId = '', $isShow = true)` 获取分类文章统计

## 标签
| 标签 | 说明| 参数 | 例子
| ----|----|----|----
| empty | 数组为空时| name | `{empty name="$list"}{/empty}`
| noempty | 数组不为空时 | name | `{noempty name="$list"}{/noempty}`
| defined | 常量已定义时 | name | `{defined name="APP_NAME"}{/defined}`
| nodefined | 常量未定义时 | name | `{nodefined name="APP_NAME"}{/nodefined}`
| isset | 变量定义时 | name | `{isset name="$test"}{/isset}`
| noset | 变量未定义时 | name | `{noset name="$test"}{/noset}`
| between | 变量存在某个区间时 | name, value | `{between name="$test" value="1,2"}{/between}`
| nobetween | 变量不存在某个区间时 | name, value | `{nobetween name="$test" value="1,2"}{/nobetween}`
| in | 变量存在数组时 | name, value | `{in name="$test" value="1,2"}{/in}`
| noin | 变量不存在数组时 | name, value | `{noin name="$test" value="1,2"}{/noin}`

## 分词功能
由于http://keyword.discuz.com 出现403(应该关服务了)

使用[@梁斌penny](https://weibo.com/pennyliang)的分词服务, 使用此项目的'模板工'们，你们应该感谢梁厂长！



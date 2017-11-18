# PHP Code Clean

`Zeupin\PhpCodeClean` 是一个PHP的代码清理工具，它可以清理PHP文件中的特定部分，比如各种注释，尽可能精简php执行文件。

## 功能

* 清除当前目录下的PHP文件中的所有注释，含文档注释和普通注释。
* 保留根文档注释。一般根文档注释里面是版权声明。
* 保留含有`!!!`字符串的文档注释。

## 使用

1. 下载 `phpcodeclean.php` 文件。

**直接下载**

<https://github.com/zeupin/phpcodeclean/>

**Composer**

```bash
composer require zeupin/phpcodeclean
```

2. 写个简单的处理脚本。

```php
// 引用进来
require 'path/to/phpcodeclean.php';

// 执行
$codeclean = new \Zeupin\PhpCodeClean();
$codeclean->ignoreFile('.git');
$target = '设置你要处理的目录';
$codeclean->clean($target);
```

3. 运行

```bash
php phpcodeclean.php
```

## 警告

1. 请务必理解本文件的用途，并谨慎使用本文件，切勿冒然尝试。
2. 运行本文件，将会修改运行目录下的所有PHP文件，剔除其中的注释语句。
3. 请务必先做好版本控制，再使用本文件。
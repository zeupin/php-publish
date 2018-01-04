# PHP Code Clean

`Zeupin\PhpCodeClean` 是一个PHP的代码清理工具，它可以清理PHP文件中的特定部分，比如各种注释，以达到精简php执行文件的目的。

## 功能

* 清除当前目录下的PHP文件中的所有注释，含文档注释和普通注释。
* 保留根文档注释。一般根文档注释里面是版权声明。
* 保留含有`!!!`字符串的文档注释。

## 使用

### 1. 下载 `PhpCodeClean.php` 文件。

**直接下载**

<https://github.com/zeupin/phpcodeclean/blob/master/src/Zeupin/PhpCodeClean.php>

**Composer**

```bash
composer require zeupin/phpcodeclean
```

### 2. 写个简单的处理脚本（文件名假定为`clean.php`）

```php
// 引用进来
require 'path/to/PhpCodeClean.php';

// 执行
$codeclean = new \Zeupin\PhpCodeClean();
$codeclean->ignoreFile('.git');
$target = '设置你要处理的目录';
$codeclean->clean($target);
```

### 3. 运行脚本`clean.php`

```bash
php clean.php
```

## 警告

1. 请务必理解本文件的用途，并谨慎使用本文件，切勿冒然尝试。
2. 运行本文件，将会修改运行目录下的所有PHP文件，剔除其中的注释语句。
3. 请务必先做好版本控制，再使用本文件。

## 版权声明

版权所有 (c) 2017-2018 上海宙品信息科技有限公司。<br>Copyright (c) 2017-2018 Zeupin LLC. <http://zeupin.com>

源代码采用MIT授权协议。<br>Licensed under The MIT License.

如需在您的项目中使用，必须保留本源代码中的完整版权声明。<br>Redistributions of files MUST retain the above copyright notice.

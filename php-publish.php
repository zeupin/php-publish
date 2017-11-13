<?php
/**
 * Dida Framework  <http://dida.zeupin.com>
 * Copyright 2017 Zeupin LLC. MIT License.
 *
 * WARNING: WITHOUT WARRANTY OF ANY KIND!!!
 */

/**
 * 处理指定的目录。
 *
 * @param string $dir
 * @param array $excludes   要排除的文件或者目录的绝对路径
 */
function process($dir, array $excludes = [])
{
    $dir = realpath($dir);
    $files = scandir($dir);

    foreach ($files as $file) {
        // 略过以.开头的文件和目录
        if (substr($file, 0, 1) === '.') {
            continue;
        }

        // 绝对路径
        $path = realpath($dir . "/{$file}");

        // 检查是否在忽略列表中
        if (in_array($path, $excludes)) {
            continue;
        }

        if (is_file($path) && (substr($file, -4) === '.php')) {
            // 如果是php文件，就处理
            processPhpFile($path);
        } elseif (is_dir($path)) {
            // 如果是目录，就递归处理
            process($path, $excludes);
        }
    }
}


/**
 * 处理PHP文件，剔除所有注释语句。
 *
 * @param string $file
 */
function processPhpFile($file)
{
    echo $file . PHP_EOL;

    // 获取文件内容
    $content = file_get_contents($file);

    // 转换为unix格式
    $content = str_replace("\r\n", "\n", $content);

    // tokenize
    $tokens = token_get_all($content);
//    file_put_contents("$file.tokens.txt", var_export($tokens, true));

    // 准备输出数组
    $output = [];

    foreach ($tokens as $key => $token) {
        if (is_array($token)) {
            $type = $token[0];

            switch ($type) {
                case 377: // T_COMMENT
                    // 删除本行注释前面的空白
                    if (is_array($tokens[$key - 1]) && $tokens[$key - 1][0] == 382) {
                        $output[$key - 1] = rtrim($output[$key - 1], ' ');
                    }

                    // 如果是单行注释
                    if (substr($token[1], 0, 2) === '//') {
                        // 如果前面是空白
                        if (is_array($tokens[$key - 1]) && $tokens[$key - 1][0] == 382) {
                            // 如果前面的空白不含换行
                            if (strpos($output[$key - 1], "\n") === false) {
                                if (strpos($token[1], "\n") !== false) {
                                    $output[$key] = "\n";
                                }
                            }
                        }

                        continue;
                    } else {
                        // 如果是多行注释
                        if (is_array($tokens[$key + 1]) && $tokens[$key + 1][0] == 382) {
                            // 删除前换行
                            $prevSpace = explode("\n", $output[$key - 1]);
                            if (count($prevSpace) > 1) {
                                array_pop($prevSpace);
                                $output[$key - 1] = implode("\n", $prevSpace);
                            }

                            // 删除本行的行尾空白
                            $tokens[$key + 1][1] = ltrim($tokens[$key + 1][1], ' ');

                            // 删除本行的换行符
                            if (substr($tokens[$key + 1][1], 0, 1) === "\n") {
                                $tokens[$key + 1][1] = substr($tokens[$key + 1][1], 1);
                            }
                        }
                    }

                    break;

                case 378: // T_DOC_COMMENT
                    // 保留根文档注释，一般是版权信息
                    if (($key == 1) && is_array($tokens[0]) && $tokens[0][0] == 379) {
                        $output[$key] = $token[1];
                        continue;
                    }

                    // 保留含有 !!! 的文档注释
                    if (strpos($token[1], '!!!')) {
                        $output[$key] = $token[1];
                        continue;
                    }

                    // 删除本行的前导空白
                    if (is_array($tokens[$key - 1]) && $tokens[$key - 1][0] == 382) {
                        $lastspace = $output[$key - 1];
                        $lastspace = rtrim($lastspace, " ");
                        if (substr($lastspace, -1) === "\n") {
                            $lastspace = substr($lastspace, 0, strlen($lastspace) - 1);
                        }
                        $output[$key - 1] = $lastspace;
                    }

                    // 删除本行的行尾空白和本行的换行符
                    if (is_array($tokens[$key + 1]) && $tokens[$key + 1][0] == 382) {
                        $nextspace = $tokens[$key + 1][1];
                        $nextspace = ltrim($nextspace, " ");
                        if (substr($nextspace, 0, 1) === "\n") {
                            $nextspace = substr($nextspace, 1);
                        }
                        $tokens[$key + 1][1] = $nextspace;
                    }

                    break;

                default:
                    $output[$key] = $token[1];
            }
        } elseif (is_string($token)) {
            $output[$key] = $token;
        }
    }

//    file_put_contents("$file.tokens1.txt", var_export($tokens, true));
    file_put_contents("$file", implode('', $output));

}

// 开始执行，扫描文件所在目录
process(__DIR__, [__FILE__]);

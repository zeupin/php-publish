<?php
/**
 * Dida Framework  <http://dida.zeupin.com>
 * Copyright 2017 Zeupin LLC. MIT License.
 *
 * WARNING: WITHOUT WARRANTY OF ANY KIND!!!
 */

/**
 * ����ָ����Ŀ¼��
 *
 * @param string $dir
 * @param array $excludes   Ҫ�ų����ļ�����Ŀ¼�ľ���·��
 */
function process($dir, array $excludes = [])
{
    $dir = realpath($dir);
    $files = scandir($dir);

    foreach ($files as $file) {
        // �Թ���.��ͷ���ļ���Ŀ¼
        if (substr($file, 0, 1) === '.') {
            continue;
        }

        // ����·��
        $path = realpath($dir . "/{$file}");

        // ����Ƿ��ں����б���
        if (in_array($path, $excludes)) {
            continue;
        }

        if (is_file($path) && (substr($file, -4) === '.php')) {
            // �����php�ļ����ʹ���
            processPhpFile($path);
        } elseif (is_dir($path)) {
            // �����Ŀ¼���͵ݹ鴦��
            process($path, $excludes);
        }
    }
}


/**
 * ����PHP�ļ����޳�����ע����䡣
 *
 * @param string $file
 */
function processPhpFile($file)
{
    echo $file . PHP_EOL;

    // ��ȡ�ļ�����
    $content = file_get_contents($file);

    // ת��Ϊunix��ʽ
    $content = str_replace("\r\n", "\n", $content);

    // tokenize
    $tokens = token_get_all($content);
//    file_put_contents("$file.tokens.txt", var_export($tokens, true));

    // ׼���������
    $output = [];

    foreach ($tokens as $key => $token) {
        if (is_array($token)) {
            $type = $token[0];

            switch ($type) {
                case 377: // T_COMMENT
                    // ɾ������ע��ǰ��Ŀհ�
                    if (is_array($tokens[$key - 1]) && $tokens[$key - 1][0] == 382) {
                        $output[$key - 1] = rtrim($output[$key - 1], ' ');
                    }

                    // ����ǵ���ע��
                    if (substr($token[1], 0, 2) === '//') {
                        // ���ǰ���ǿհ�
                        if (is_array($tokens[$key - 1]) && $tokens[$key - 1][0] == 382) {
                            // ���ǰ��Ŀհײ�������
                            if (strpos($output[$key - 1], "\n") === false) {
                                if (strpos($token[1], "\n") !== false) {
                                    $output[$key] = "\n";
                                }
                            }
                        }

                        continue;
                    } else {
                        // ����Ƕ���ע��
                        if (is_array($tokens[$key + 1]) && $tokens[$key + 1][0] == 382) {
                            // ɾ��ǰ����
                            $prevSpace = explode("\n", $output[$key - 1]);
                            if (count($prevSpace) > 1) {
                                array_pop($prevSpace);
                                $output[$key - 1] = implode("\n", $prevSpace);
                            }

                            // ɾ�����е���β�հ�
                            $tokens[$key + 1][1] = ltrim($tokens[$key + 1][1], ' ');

                            // ɾ�����еĻ��з�
                            if (substr($tokens[$key + 1][1], 0, 1) === "\n") {
                                $tokens[$key + 1][1] = substr($tokens[$key + 1][1], 1);
                            }
                        }
                    }

                    break;

                case 378: // T_DOC_COMMENT
                    // �������ĵ�ע�ͣ�һ���ǰ�Ȩ��Ϣ
                    if (($key == 1) && is_array($tokens[0]) && $tokens[0][0] == 379) {
                        $output[$key] = $token[1];
                        continue;
                    }

                    // �������� !!! ���ĵ�ע��
                    if (strpos($token[1], '!!!')) {
                        $output[$key] = $token[1];
                        continue;
                    }

                    // ɾ�����е�ǰ���հ�
                    if (is_array($tokens[$key - 1]) && $tokens[$key - 1][0] == 382) {
                        $lastspace = $output[$key - 1];
                        $lastspace = rtrim($lastspace, " ");
                        if (substr($lastspace, -1) === "\n") {
                            $lastspace = substr($lastspace, 0, strlen($lastspace) - 1);
                        }
                        $output[$key - 1] = $lastspace;
                    }

                    // ɾ�����е���β�հ׺ͱ��еĻ��з�
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

// ��ʼִ�У�ɨ���ļ�����Ŀ¼
process(__DIR__, [__FILE__]);

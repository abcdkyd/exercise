<?php
/**
 * Curl 封装
 */
class Curl
{

    /**
     * 执行请求
     * @param string $method  请求方式 GET/POST/PUT/DELETE
     * @param string $url 地址
     * @param string|array $fields 附带参数，可以是数组，也可以是字符串
     * @param string $userAgent 浏览器UA
     * @param string $httpHeaders header头部，数组形式
     * @param string $username 用户名
     * @param string $password  密码
     * @param array $sslCerts 证书信息
     * @param boolean $returnHeader 是否返回HTTP Header 信息，默认不返回
     * @return boolean
     */
    public function execute($method, $url, $fields = '', $userAgent = '', $httpHeaders = '', $username = '', $password = '', $sslCerts = array(), $returnHeader = false)
    {
        $ch = $this->create();
        if (false === $ch) {
            return false;
        }
        if (empty($url) || ! is_string($url)) {
            return false;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        // 是否显示头部信息
        curl_setopt($ch, CURLOPT_HEADER, false);
        // 设置是否返回信息
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // 设置为客户端支持gzip压缩
        curl_setopt($ch, CURLOPT_ENCODING ,'gzip');
        // 重定向
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        if ($username != '') {
            curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
        }
        
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }

        $method = strtolower($method);
        if ('post' == $method) {
            curl_setopt($ch, CURLOPT_POST, true);
            if (is_array($fields)) {
                $sets = array();
                foreach ($fields as $key => $val) {
                    $sets[] = $key . '=' . urlencode($val);
                }
                $fields = implode('&', $sets);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        } else 
            if ('put' == $method) {
                curl_setopt($ch, CURLOPT_PUT, true);
            }
        
        if (! empty($sslCerts)) {
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, $sslCerts['crt']);
            curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $sslCerts['crt_pwd']);
            
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLKEY, $sslCerts['key']);
            curl_setopt($ch, CURLOPT_SSLKEYPASSWD, $sslCerts['key_pwd']);
            
            if (isset($sslCerts['server_ca'])) {
                curl_setopt($ch, CURLOPT_CAINFO, $sslCerts['server_ca']);
            }
        }
        
        // curl_setopt($ch, CURLOPT_PROGRESS, true);
        // curl_setopt($ch, CURLOPT_VERBOSE, true);
        // curl_setopt($ch, CURLOPT_MUTE, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 设置curl超时秒数
        if (strlen($userAgent)) {
            curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        }
        if (is_array($httpHeaders)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);
        }
        
        $ret = curl_exec($ch);
        $header = curl_getinfo($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            if ($returnHeader) {
                return array(
                    'header' => $header,
                    'body' => array(
                        'error_code' => curl_errno($ch),
                        'error_msg' => curl_error($ch)
                    )
                );
            } else {
                return array(
                    'error_code' => curl_errno($ch),
                    'error_msg' => curl_error($ch)
                );
            }
        } else {
            curl_close($ch);
            if (! is_string($ret) || ! strlen($ret)) {
                return false;
            }
            
            if ($returnHeader) {
                return array(
                    'header' => $header,
                    'body' => $ret
                );
            } else {
                return $ret;
            }
        }
    }

    /**
     * 发送POST请求
     * 
     * @param string $url 地址
     * @param string|array $fields 附带参数，可以是数组，也可以是字符串
     * @param string $userAgent  浏览器UA
     * @param array $httpHeaders  header头部，数组形式
     * @param string $username 用户名
     * @param string $password 密码
     * @return boolean
     */
    public function post($url, $fields, $userAgent = '', $httpHeaders = '', $username = '', $password = '')
    {
        $ret = $this->execute('POST', $url, $fields, $userAgent, $httpHeaders, $username, $password);
        if (false === $ret) {
            return false;
        }
        if (is_array($ret)) {
            return false;
        }
        return $ret;
    }

    /**
     * GET
     * 
     * @param string $url 地址
     * @param string $userAgent  浏览器UA
     * @param array $httpHeaders header头部，数组形式
     * @param string $username 用户名
     * @param string $password 密码
     * @return boolean
     */
    public function get($url, $userAgent = '', $httpHeaders = '', $username = '', $password = '')
    {
        $ret = $this->execute('GET', $url, "", $userAgent, $httpHeaders, $username, $password);
        if (false === $ret) {
            return false;
        }
        if (is_array($ret)) {
            return false;
        }
        return $ret;
    }

    /**
     * curl支持 检测
     * 
     * @return resource|boolean
     */
    public function create()
    {
        $ch = null;
        if (! function_exists('curl_init')) {
            return false;
        }
        $ch = curl_init();
        if (! is_resource($ch)) {
            return false;
        }
        return $ch;
    }
}


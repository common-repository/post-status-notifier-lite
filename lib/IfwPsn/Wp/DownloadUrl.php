<?php

class IfwPsn_Wp_DownloadUrl
{
    private $url;
    private $filepath;
    private $error;
    private $response;

    public function __construct(string $url)
    {
        $this->url = $url;

        $this->initHooks();
    }

    protected function initHooks()
    {
        $this->initHttpRequestArgsHook();
        $this->initHttpResponseHook();
        $this->initHttpApiDebugHook();
    }

    protected function initHttpRequestArgsHook()
    {
        add_filter('http_request_args', function (array $parsed_args, $submittedUrl) {
            if ($submittedUrl === $this->url) {
                $parsed_args = $this->filterHttpRequestArgs($parsed_args);
            }
            return $parsed_args;
        }, 10, 2);
    }

    public function download(): bool
    {
        set_time_limit(0);

        if (!function_exists('download_url')) {
            require_once(ABSPATH . "wp-admin" . '/includes/image.php');
            require_once(ABSPATH . "wp-admin" . '/includes/file.php');
            require_once(ABSPATH . "wp-admin" . '/includes/media.php');
        }

        $downloadResult = download_url($this->url);

        if (is_wp_error($downloadResult)) {
            $this->handleError($downloadResult);
        } elseif (file_exists($downloadResult)) {
            $this->filepath = $downloadResult;
            return true;
        }

        return false;
    }

    protected function filterHttpRequestArgs(array $parsed_args): array
    {
        return $parsed_args;
    }

    protected function initHttpApiDebugHook()
    {
        /**
         * Fires after an HTTP API response is received and before the response is returned.
         *
         * @param array\WP_Error $response HTTP response or WP_Error object.
         * @param string $context Context under which the hook is fired.
         * @param string $class HTTP transport used.
         * @param array $parsed_args HTTP request arguments.
         * @param string $url The request URL.
         */
        add_action('http_api_debug', function ($response, $context, $class, $parsed_args, $url) {
            if ($url === $this->url) {
            }
        }, 10, 5);

    }

    protected function initHttpResponseHook()
    {
        add_filter('http_response', function ($response, $parsed_args, $url) {
            if ($url === $this->url) {
                $this->response = $response;
                $this->onResponse($response);
            }
            return $response;
        }, 10, 3);
    }

    protected function onResponse($response)
    {
    }

    public function getContentType(): ?string
    {
        $httpResponse = $this->getHttpResponse();

        if ($httpResponse instanceof \WP_HTTP_Requests_Response) {
            return $httpResponse->get_headers()->offsetGet('content-type');
        }

        return null;
    }

    public function getFilesize(): ?int
    {
        $httpResponse = $this->getHttpResponse();

        if ($httpResponse instanceof \WP_HTTP_Requests_Response &&
            $httpResponse->get_headers()->offsetExists('content-length')) {
            return (int)$httpResponse->get_headers()->offsetGet('content-length');
        } elseif (file_exists($this->filepath)) {
            $size = filesize($this->filepath);
            if ($size !== false) {
                return $size;
            }
        }

        return null;
    }

    public function getFilename()
    {
        $filename = basename($this->url);

        $httpResponse = $this->getHttpResponse();

        if ($httpResponse instanceof \WP_HTTP_Requests_Response &&
            $httpResponse->get_headers()->offsetExists('content-disposition')) {
            preg_match('/filename="(.*)"/', $httpResponse->get_headers()->offsetGet('content-disposition'), $matches);
            if (!empty($matches[1])) {
                $filename = $matches[1];
            }
        }

        if (strpos($filename, '?') !== false) {
            $split = explode('?', $filename);
            $filename = $split[0];
        }

        return $filename;
    }

    public function getStatusCode()
    {
        return $this->getResponseCode();
    }

    public function isStatusOk(): bool
    {
        return $this->getStatusCode() === 200;
    }

    public function getResponseCode()
    {
        if (is_array($this->response) && isset($this->response['response']['code'])) {
            return $this->response['response']['code'];
        }
        return null;
    }

    public function getResponseMessage()
    {
        if (is_array($this->response) && isset($this->response['response']['message'])) {
            return $this->response['response']['message'];
        }
        return null;
    }

    /**
     * @return \WP_HTTP_Requests_Response|null
     */
    public function getHttpResponse(): ?\WP_HTTP_Requests_Response
    {
        if (is_array($this->response) && isset($this->response['http_response'])) {
            return $this->response['http_response'];
        }
        return null;
    }

    protected function handleError(\WP_Error $error)
    {
        $this->error = $error;
    }

    public function hasError(): bool
    {
        return $this->error instanceof \WP_Error;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getErrorCode()
    {
        return $this->error->get_error_code();
    }

    public function getErrorMessage()
    {
        return $this->error->get_error_message();
    }

    /**
     * @return mixed
     */
    public function getFilepath()
    {
        return $this->filepath;
    }
}
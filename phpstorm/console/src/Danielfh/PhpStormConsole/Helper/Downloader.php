<?php

namespace Danielfh\PhpStormConsole\Helper;

use Symfony\Component\Console\Helper\ProgressBar;

class Downloader
{
    protected static function getRedirectUrl($url, $sslOff = false)
    {
        $currentContext        = stream_context_get_default();
        $currentContextOptions = stream_context_get_options($currentContext);

        $newContext = [
            'http' => [
                'method' => 'HEAD',
            ],
        ];

        if ($sslOff) {
            $newContext['ssl'] = [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ];
        }

        stream_context_set_default($newContext);

        $headers = get_headers($url, 1);
        stream_context_set_default($currentContextOptions);

        if ($headers !== false && isset($headers['Location'])) {
            return $headers['Location'];
        }

        return false;
    }

    protected static function newContext(ProgressBar $progressBar, $sslOff = false)
    {
        $options = [];

        if ($sslOff) {
            $options['ssl'] = [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ];
        }
        
        $ctx = stream_context_create($options, ['notification' => function ($notificationCode, $severity, $message, $messageCode, $bytesTransferred, $bytesMax) use ($progressBar) {
            switch ($notificationCode) {
                case STREAM_NOTIFY_FILE_SIZE_IS:
                    $progressBar->start($bytesMax);
                    break;
                case STREAM_NOTIFY_PROGRESS:
                    $progressBar->setProgress($bytesTransferred);
                    break;
            }
        }]);

        return $ctx;
    }

    public static function getContent(string $source, ProgressBar $progressBar, $sslOff = false)
    {
        if (filter_var($source, FILTER_VALIDATE_URL)) {
            while ($isRedirection = self::getRedirectUrl($source, true)) {
                $source = $isRedirection;
            }
        }

        $ctx    = self::newContext($progressBar, $sslOff);
        $result = file_get_contents($source, false, $ctx);

        $progressBar->finish();

        return $result;
    }

    public static function saveContent(string $source, string $destination, ProgressBar $progressBar, $sslOff = false)
    {
        if (filter_var($source, FILTER_VALIDATE_URL)) {
            while ($isRedirection = self::getRedirectUrl($source, true)) {
                $source = $isRedirection;
            }
        }

        $ctx    = self::newContext($progressBar, $sslOff);
        $result = copy($source, $destination, $ctx);

        $progressBar->finish();

        if (!$result) {
            throw new \Exception("Error while downloading {$source}");
        }

        return $result;
    }
}